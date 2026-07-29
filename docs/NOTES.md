# Hinweise zur orgaMAX-API

Dokumentierte Abweichungen und Eigenheiten der offiziellen OpenAPI-Spec
(https://api.orgamax.de/openapi/documentation/, Kopie unter
[docs/OpenAPI/orgamax-openapi.json](OpenAPI/orgamax-openapi.json)), die beim
SDK-Design berücksichtigt wurden.

## Authentifizierung

- Zweistufiger Flow: API-Key + API-Secret als **Basic-Auth** an
  `POST /auth/token` (Body: `{"ownershipId": "..."}`) liefert ein JWT, das als
  **Bearer-Token** für alle übrigen Routen dient.
- Die `ownershipId` stammt aus der Callback-URL der Erweiterung
  (Query-Parameter `iid`).
- Im SDK: `Client::fromCredentials($apiKey, $apiSecret, $ownershipId)` führt den
  kompletten Flow aus; `new Client($token)` nutzt ein vorhandenes Token.

## Request-/Response-Konventionen

- **Request-Bodies sind flache Objekte** — die in der Spec teils angegebenen
  Wrapper (`{"article": {...}}`) bzw. `{meta, data}`-Envelopes für Requests
  widersprechen sämtlichen Spec-Beispielen und werden vom SDK nicht gesendet.
- **Antworten sind enveloped**: Einzelobjekte als `{"meta": {}, "data": {...}}`,
  Listen als `{"meta": {count, totalCount?, filter?}, "data": [...]}`.
  Create-/Update-Antworten liefern meist nur `{"meta": {}, "data": {"id": "90"}}`
  (im SDK: `ResourceResponse`); `POST /customer/` liefert dagegen den ganzen
  Kunden.
- Paginierung einheitlich über `offset`, `limit`, `orderBy`, `desc`, `search`
  (Query-Parameter der `search()`-Methoden).

## Inkonsistenzen der Spec

- **Statuscodes** variieren ohne erkennbares System: Create liefert je nach
  Ressource 200 (`expense`, `order`, `todo`, Settings) oder 201 (`article`,
  `customer`, `supplier`, `file/upload`); Update liefert 200 oder 204
  (`PUT /expense/{id}`, `PUT /setting/deliveryCondition/{id}`). Das SDK
  erwartet je Operation exakt den dokumentierten Code.
- **Trailing Slashes** sind uneinheitlich (`POST /article/` mit,
  `POST /supplier` ohne). Das SDK folgt der Spec buchstabengetreu.
- `PUT /setting/payCondition/` trägt die ID **im Body**, nicht im Pfad.
- `POST /todo/message/{id}` **löscht** eine To-do-Message (trotz POST).
- `GET /todo/{id}` liefert die **Messages** eines To-dos, nicht das To-do.
- `GET /invoice/{id}/download` ist deprecated → `GET /invoice/document/{id}`.
- Tippfehler in der Spec, die reale Feldnamen sein können und daher im SDK
  beibehalten wurden: `mimetyp` (File), `tenantd` (TodoMessage),
  `documenthPath` (Invoice-Lock; das SDK mappt vorsorglich auch
  `documentPath`). Schema-Namen wie `Micellaneous` und Enum-Werte wie
  `accept` (statt `accepted`) stammen ebenfalls aus der Spec.
- Datumsfelder werden als Strings im Format `Y-m-d` geschrieben (z. B.
  `InvoicePayment.date`); reine Lese-Timestamps (`createdAt`, `updatedAt`,
  `sentAt`, `recordDate`) hydratisiert das SDK zu `DateTime`.
- Enum-Werte in Antworten werden strikt hydratisiert (`BackedEnum::from`).
  Sollte die API undokumentierte Werte liefern (die Spec enthält Tippfehler
  wie `receject` in Filter-Namen), schlägt die Hydration bewusst laut fehl.

## Felder außerhalb der Spec

Die Entities bilden ausschließlich dokumentierte Felder ab; alles andere geht
bei der Hydration verloren. Wo eine Anwendung auf undokumentierte Felder
angewiesen ist (z. B. die freigeschalteten API-Zugriffe eines Mandanten),
liefert `Settings\AccountSettingEndpoint::raw()` die Antwort unverändert als
Array.

## Transport

`Client` akzeptiert als letzten Konstruktorparameter einen vorkonfigurierten
Guzzle-Client (Tests mit `MockHandler`, Proxy, eigene Timeouts). Die Ziel-URL
baut der Client vollständig selbst (`baseUrl` + `/openapi` + Pfad) — eine
`base_uri` am injizierten Transport ist nicht nötig und wird nicht ausgewertet.

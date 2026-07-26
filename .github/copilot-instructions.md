# orgamax-php-sdk — Architektur-Regeln für AI-Assistenten

PHP-SDK für die orgaMAX-API (https://api.orgamax.de/openapi). Basiert auf
`daniel-jorg-schuppelius/php-api-toolkit` (ClientAbstract, EndpointAbstract,
NamedEntity, NamedValues, Exceptions, Auth-Strategien).

## Layer

- `src/API/Client.php` — einziger HTTP-Client; prefixt relative URIs mit
  `/openapi`, Bearer-Auth. `Client::fromCredentials()` führt den Auth-Flow
  (Basic-Auth → POST /auth/token → JWT) aus.
- `src/API/Endpoints/` — eine Klasse pro Ressource (`<Ressource>Endpoint`),
  Settings-Routen unter `Endpoints/Settings/`. Client per Konstruktor-Injection,
  keine Facade am Client.
- `src/Contracts/Abstracts/` — `ItemResponseAbstract` (`{meta, data:{}}`),
  `ListResponseAbstract` (`{meta, data:[]}`),
  `API/DocumentEndpointAbstract` (PDF-Route `{resource}/document/{id}`).
- `src/Entities/<Ressource>/` — Entity, Collection (`NamedValues`),
  List-Envelope, Item-Envelope; geteilte Entities unter `Entities/Common/`.
- `src/Enums/` — string-backed Enums für dokumentierte Wertemengen.

## Entity-Pattern (Reflection-Hydration!)

1. Properties `protected` + nullable typed, Namen exakt wie die API-Felder.
2. Konstruktor immer `($data = null, ?LoggerInterface $logger = null)` mit
   `@param array<string, mixed>|object|null $data`.
3. Getter `return $this->x ?? null;`, Setter nur für Schreibfelder.
4. Collections setzen `$entityName` + `$valueClassName` vor `parent::__construct`.
5. Datums-Schreibfelder sind `?string` (Y-m-d) — `?DateTime` würde als
   RFC3339 serialisiert; Lese-Timestamps sind `?DateTime`.

## Endpoint-Pattern

- Pfade API-relativ ohne führenden Slash; Trailing Slashes exakt wie die Spec
  (`POST article/` mit, `POST supplier` ohne).
- Statuscodes je Operation exakt aus der Spec (mal 200, mal 201, mal 204).
- Requests: flache Objekte via `parent::postContents($entity->toArray(), ...)`.
- Jede Methode: `self::logDebug()` + `self::logInfoWithTimer()` (Mutationen)
  bzw. `self::logDebugWithTimer()` (Lesezugriffe).
- Fehlende ID → `self::logErrorAndThrow(InvalidArgumentException::class, ...)`;
  nicht unterstützte Operation → `NotAllowedException` mit Code 405.

## Tests

- Offline-Tests (CI-Abdeckung): `tests/Endpoints/Offline/`, Basis
  `Tests\Contracts\OfflineEndpointTest`, Mock über `Tests\Mocks\MockApiClient`
  (`addResponse(method, uriPattern, status, body)`; Listen-Patterns brauchen
  Wildcards, da Query-Strings an die URI angehängt werden).
- Live-Tests: Basis `Tests\Contracts\EndpointTest`, übersprungen bei
  `ORGAMAX_SKIP_API_TESTS=1` (Default) oder fehlender `.samples/config.json`.
- Methodennamen `test_snake_case`.

## Anti-Patterns

- Keine eigenen Exception-Klassen — alles aus `APIToolkit\Exceptions`.
- Kein `readonly`, keine Constructor-Promotion in Entities (bricht Hydration).
- Keine Request-Envelopes senden (`{meta, data}` gilt nur für Antworten).
- `phpstan` Level 8 ohne Baseline; `pint` mit K&R-Braces (`class Foo {`).

## Quellen

- Spec-Kopie: `docs/OpenAPI/orgamax-openapi.json`
- API-Eigenheiten: `docs/NOTES.md`
- Coverage-Check: `php tools/check-endpoint-coverage.php`

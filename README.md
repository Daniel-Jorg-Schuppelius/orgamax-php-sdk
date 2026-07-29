# orgaMAX PHP SDK

![PHP Version](https://img.shields.io/badge/PHP-8.2%20%7C%208.3%20%7C%208.4-blue)
![License](https://img.shields.io/badge/license-MIT-green)
[![PHP Composer](https://github.com/dschuppelius/orgamax-php-sdk/actions/workflows/php.yml/badge.svg)](https://github.com/dschuppelius/orgamax-php-sdk/actions/workflows/php.yml)

Ein modernes PHP-SDK für die [orgaMAX-API](https://api.orgamax.de/openapi/documentation/) — nach dem Vorbild von [lexoffice-php-sdk](https://github.com/dschuppelius/lexoffice-php-sdk) und [datev-php-sdk](https://github.com/dschuppelius/datev-php-sdk), aufgebaut auf dem [php-api-toolkit](https://github.com/dschuppelius/php-api-toolkit).

## 🚀 Features

- **Vollständige API-Abdeckung**: alle 70 Operationen der orgaMAX-OpenAPI-Spec (Artikel, Kunden, Lieferanten, Rechnungen, Angebote, Aufträge, Lieferscheine, Ausgaben, Dateien, To-dos, Tags, Benutzer, Kontenrahmen, Einstellungen)
- **Typisierte Entities** mit automatischer Reflection-Hydration (JSON ↔ Objekt) und Enums für dokumentierte Wertemengen
- **Kompletter Auth-Flow**: `Client::fromCredentials()` erledigt Basic-Auth → Token-Bezug → Bearer-Client in einem Aufruf
- **Robuster HTTP-Layer** aus dem Toolkit: Retry mit Backoff und `Retry-After`, Throttling, Statuscode-→-Exception-Mapping, Log-Redaction sensibler Daten
- **PDF-Belegabruf** für Rechnungen, Angebote, Aufträge und Lieferscheine
- **Multipart-Datei-Upload** inkl. Belegerkennung (`analyze`)
- PHPStan Level 8, Laravel Pint, PHPUnit 11 — CI über GitHub Actions (PHP 8.2–8.4)

## 📋 Voraussetzungen

- PHP >= 8.2 < 8.5
- Composer
- Ein orgaMAX-Konto mit registrierter Erweiterung (Marketplace → *Eigene Erweiterungen*)

## 📦 Installation

```bash
composer require daniel-jorg-schuppelius/orgamax-php-sdk
```

## ⚙️ Konfiguration

Die orgaMAX-API nutzt einen zweistufigen Auth-Flow:

1. In der [orgaMAX-Anwendung](https://app.orgamax.de) als App-Entwickler registrieren und unter *Marketplace → Eigene Erweiterungen* eine Erweiterung anlegen.
2. **API-Schlüssel** und **geheimen API-Schlüssel** notieren.
3. Die **ownershipId** aus der Callback-URL der Erweiterung entnehmen (Query-Parameter `iid`).
4. Gewünschte API-Zugriffe der Erweiterung unter *API-Zugriffe* freischalten.

```php
use Orgamax\API\Client;

// Variante 1: kompletter Auth-Flow (holt das Bearer-Token selbst)
$client = Client::fromCredentials('api-key', 'api-secret', 'ownership-id');

// Variante 2: vorhandenes Bearer-Token direkt verwenden
$client = new Client($token);

// Optional: eigener PSR-3-Logger und Request-Throttling
$client = new Client($token, Client::DEFAULT_BASE_URL, $logger, true);

// Optional: eigener Guzzle-Transport (Test-MockHandler, Proxy, Timeouts).
// Die Ziel-URL baut der Client vollständig selbst — eine base_uri des
// injizierten Clients ist nicht nötig.
$client = new Client($token, Client::DEFAULT_BASE_URL, $logger, false, $guzzle);
```

## 📚 Verwendung

### Artikel anlegen und suchen

```php
use Orgamax\API\Endpoints\ArticlesEndpoint;
use Orgamax\Entities\Articles\Article;

$articles = new ArticlesEndpoint($client);

$resource = $articles->create(new Article([
    'title' => 'My Article',
    'unit' => 'Stk.',
    'number' => '0015',
    'price' => 200.25,
    'vatPercent' => 19,
]));
echo $resource->getData()?->getId(); // "90"

$list = $articles->search(['limit' => 20, 'search' => 'My']);
echo $list?->getMeta()?->getTotalCount();
foreach ($list?->getValues() ?? [] as $article) {
    echo $article->getTitle();
}
```

### Kunden verwalten

```php
use APIToolkit\Entities\ID;
use Orgamax\API\Endpoints\CustomersEndpoint;
use Orgamax\Entities\Customers\Customer;

$customers = new CustomersEndpoint($client);

$response = $customers->create(new Customer([
    'customerDefaultAddress' => [
        'billingAddress' => ['kind' => 'person', 'lastName' => 'Müller'],
    ],
]));

$customer = $customers->get(new ID(6507));
echo $customer?->getName();
```

### Rechnung abrufen, Zahlung erfassen, PDF laden

```php
use APIToolkit\Entities\ID;
use Orgamax\API\Endpoints\InvoicesEndpoint;
use Orgamax\Entities\Invoices\InvoicePayment;

$invoices = new InvoicesEndpoint($client);

$invoice = $invoices->get(new ID(74));
$invoices->addPayment(new ID(74), new InvoicePayment([
    'amount' => 12.37,
    'type' => 'payment',
    'date' => '2026-07-26',
]));

file_put_contents('rechnung.pdf', $invoices->document(new ID(74)));
```

### Datei hochladen und analysieren

```php
use APIToolkit\Entities\ID;
use Orgamax\API\Endpoints\FilesEndpoint;

$files = new FilesEndpoint($client);
$fileData = $files->upload('/pfad/zum/beleg.pdf', 'Rechnungseingang', ['#projekt-a']);
$analysis = $files->analyze(new ID(12));
echo $analysis?->getPayee();
```

## 🏗️ Projektstruktur

```
src/
├── API/
│   ├── Client.php                  # HTTP-Client (Bearer, /openapi-Prefixing, Auth-Flow)
│   └── Endpoints/                  # 15 Endpoint-Klassen, eine je Ressource
│       └── Settings/               # Account, Miscellaneous, ArticleSettings, Pay-/DeliveryConditions
├── Contracts/
│   ├── Abstracts/                  # Item-/List-Response-Envelopes, DocumentEndpointAbstract
│   └── Interfaces/API/             # SearchableEndpointInterface, DocumentEndpointInterface
├── Entities/                       # Typisierte Domain-Modelle, ein Verzeichnis je Ressource
│   ├── Common/                     # Address, CustomerData, Position(s), ListMeta, ResourceResponse …
│   └── …/                          # Articles, Customers, Suppliers, Invoices, Offers, Orders,
│                                   # DeliveryNotes, Expenses, Files, Todos, Tags, Users,
│                                   # Bookkeeping, Settings, Auth
└── Enums/                          # 11 string-backed Enums (States, Kinds, PaymentType …)

tests/
├── Contracts/                      # Basisklassen (Offline + Live, von der Suite ausgeschlossen)
├── Endpoints/Offline/              # 18 Offline-Testklassen (CI-Abdeckung, MockApiClient)
├── Entities/                       # Hydration-/Serialisierungs-Tests
└── Mocks/MockApiClient.php         # ApiClientInterface-Mock mit Response-Registry
```

## 🔌 API-Endpunkte

| Endpoint | Routen | Beschreibung |
|---|---|---|
| `AuthEndpoint` | POST /auth/token | Bearer-Token aus ownershipId beziehen |
| `ArticlesEndpoint` | CRUD + Liste /article | Artikelverwaltung |
| `CustomersEndpoint` | Create/Get/Update + Liste /customer | Kundenverwaltung |
| `SuppliersEndpoint` | CRUD + Liste /supplier | Lieferantenverwaltung |
| `InvoicesEndpoint` | /invoice: Get, Liste, Payment, Lock, Send, Document (PDF) | Rechnungen |
| `OffersEndpoint` | /offer: Get, Liste, Document (PDF) | Angebote |
| `OrdersEndpoint` | /order: Create, Get, Liste, Document, Order→Invoice | Aufträge |
| `DeliveryNotesEndpoint` | /deliveryNote: Get, Liste, Document (PDF) | Lieferscheine |
| `ExpensesEndpoint` | CRUD + Liste /expense, Receipt-Delete | Ausgaben |
| `FilesEndpoint` | /file: Upload, Download, Meta, Analyze, Delete, Liste | Dokumente & Belegerkennung |
| `TodosEndpoint` | /todo: Create, Liste, Messages, Link/Unlink, Status, DueDate | To-dos |
| `TagsEndpoint` | GET /tags | Tags |
| `UsersEndpoint` | GET /user | Benutzer |
| `BookkeepingEndpoint` | GET /bookkeeping/getchartofaccounts | Kontenrahmen |
| `Settings\*` | /setting/account, /setting/miscellaneous, /setting/article, /setting/payCondition, /setting/deliveryCondition | Einstellungen |

Eigenheiten der API (Statuscodes, Trailing Slashes, Spec-Tippfehler) sind in [docs/NOTES.md](docs/NOTES.md) dokumentiert; die Spec-Kopie liegt unter [docs/OpenAPI/orgamax-openapi.json](docs/OpenAPI/orgamax-openapi.json).

## 🧪 Tests

```bash
composer test        # PHPUnit (Offline-Tests, ohne API-Zugang lauffähig)
composer lint        # PHPStan Level 8
composer format      # Pint (Check) / composer format:fix
composer qa          # alles zusammen
```

Live-Tests gegen die echte API: `.samples/config.json.sample` nach `.samples/config.json` kopieren, Zugangsdaten eintragen und `ORGAMAX_SKIP_API_TESTS` in `phpunit.xml` entfernen bzw. auf `0` setzen.

```bash
php tools/check-endpoint-coverage.php   # SDK-Abdeckung gegen die OpenAPI-Spec prüfen
```

## 📖 Abhängigkeiten

| Paket | Zweck |
|---|---|
| [daniel-jorg-schuppelius/php-api-toolkit](https://github.com/dschuppelius/php-api-toolkit) | ClientAbstract (Guzzle, Retry, Throttling), EndpointAbstract, NamedEntity/NamedValues (Hydration), Exceptions, Auth-Strategien |
| guzzlehttp/guzzle *(transitiv)* | HTTP-Transport |
| dschuppelius/php-error-toolkit *(transitiv)* | Logging (PSR-3), ErrorLog-Trait |
| dschuppelius/php-config-toolkit *(transitiv)* | Config-Loader für Live-Tests |

## 📄 Lizenz

MIT — siehe [LICENSE](LICENSE).

## 👤 Autor

**Daniel Jörg Schuppelius** — [schuppelius.org](https://schuppelius.org)

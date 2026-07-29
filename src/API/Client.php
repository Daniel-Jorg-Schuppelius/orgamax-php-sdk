<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : Client.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API;

use APIToolkit\API\Authentication\{BasicAuthentication, BearerAuthentication};
use APIToolkit\Contracts\Abstracts\API\ClientAbstract;
use GuzzleHttp\Client as HttpClient;
use Orgamax\API\Authentication\OwnershipTokenAuthentication;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * API-Client für die orgaMAX-API (https://api.orgamax.de/openapi).
 *
 * Die API nutzt einen zweistufigen Auth-Flow: API-Key und API-Secret werden
 * als Basic-Auth an POST /auth/token gesendet (zusammen mit der ownershipId
 * aus der Callback-URL der Erweiterung) und liefern ein JWT, das als
 * Bearer-Token für alle weiteren Routen dient.
 *
 * Da Guzzle pfadbehaftete base_uri nicht zuverlässig auflöst, baut der Client
 * die Ziel-URL selbst: Endpoints verwenden API-relative Pfade (z. B.
 * "article/{id}"), daraus wird baseUrl + /openapi + Pfad. Absolute URLs
 * passieren unverändert. Die vollständige URL macht den Client unabhängig von
 * der base_uri eines injizierten Guzzle-Transports.
 */
class Client extends ClientAbstract {
    public const DEFAULT_BASE_URL = 'https://api.orgamax.de';

    public const BASE_PATH = '/openapi';

    /**
     * @param HttpClient|null $httpClient Vorkonfigurierter Guzzle-Client — Naht
     *                                    für Tests (MockHandler) und für
     *                                    Anwendungen mit eigenem Transport.
     */
    public function __construct(
        #[\SensitiveParameter] ?string $token,
        string $baseUrl = self::DEFAULT_BASE_URL,
        ?LoggerInterface $logger = null,
        bool $sleepAfterRequest = false,
        ?HttpClient $httpClient = null
    ) {
        parent::__construct($baseUrl, $logger, $sleepAfterRequest, $httpClient);

        // Kein Default-Content-Type: Multipart-Uploads setzen ihren eigenen;
        // JSON-Requests nutzen die Guzzle-json-Option.
        $this->setDefaultHeaders([
            'Accept' => 'application/json',
        ]);

        if ($token !== null) {
            $this->setAuthentication(new BearerAuthentication($token));
        }
    }

    /**
     * Erzeugt einen Client mit Basic-Auth aus API-Key und API-Secret.
     * Damit lässt sich ausschließlich die Auth-Route (POST /auth/token)
     * aufrufen, um ein Bearer-Token zu beziehen.
     */
    public static function forCredentials(
        string $apiKey,
        #[\SensitiveParameter] string $apiSecret,
        string $baseUrl = self::DEFAULT_BASE_URL,
        ?LoggerInterface $logger = null,
        bool $sleepAfterRequest = false,
        ?HttpClient $httpClient = null
    ): self {
        $client = new self(null, $baseUrl, $logger, $sleepAfterRequest, $httpClient);
        $client->setAuthentication(new BasicAuthentication($apiKey, $apiSecret));

        return $client;
    }

    /**
     * Kompletter Auth-Flow: bezieht mit API-Key, API-Secret und ownershipId
     * ein Bearer-Token und liefert einen damit authentifizierten Client.
     *
     * Das Token läuft ab; der Client erneuert es selbst, sobald die API mit
     * 401 antwortet (siehe {@see OwnershipTokenAuthentication}). Ein bereits
     * vorhandenes Token kann übergeben werden, um den ersten Auth-Request zu
     * sparen.
     */
    public static function fromCredentials(
        string $apiKey,
        #[\SensitiveParameter] string $apiSecret,
        string $ownershipId,
        string $baseUrl = self::DEFAULT_BASE_URL,
        ?LoggerInterface $logger = null,
        bool $sleepAfterRequest = false,
        ?HttpClient $httpClient = null,
        #[\SensitiveParameter] ?string $token = null
    ): self {
        $client = new self(null, $baseUrl, $logger, $sleepAfterRequest, $httpClient);
        $client->setAuthentication(new OwnershipTokenAuthentication(
            $apiKey,
            $apiSecret,
            $ownershipId,
            $baseUrl,
            $token,
            $logger,
            $httpClient
        ));

        return $client;
    }

    /**
     * Baut aus einem API-relativen URI die vollständige Ziel-URL
     * (baseUrl + /openapi + Pfad). Absolute URLs (http/https) bleiben
     * unverändert; ein gerooteter Pfad ("/...") wird nur an die baseUrl
     * gehängt, trägt den Basispfad also selbst.
     */
    protected function prefixUri(string $uri): string {
        if ($uri === '' || str_starts_with($uri, 'http://') || str_starts_with($uri, 'https://')) {
            return $uri;
        }

        if (str_starts_with($uri, '/')) {
            return $this->getBaseUrl() . $uri;
        }

        return $this->getBaseUrl() . self::BASE_PATH . '/' . $uri;
    }

    /**
     * @param array<string, mixed> $options
     */
    public function get(string $uri, array $options = []): ResponseInterface {
        return parent::get($this->prefixUri($uri), $options);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function post(string $uri, array $options = []): ResponseInterface {
        return parent::post($this->prefixUri($uri), $options);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function put(string $uri, array $options = []): ResponseInterface {
        return parent::put($this->prefixUri($uri), $options);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function patch(string $uri, array $options = []): ResponseInterface {
        return parent::patch($this->prefixUri($uri), $options);
    }

    /**
     * @param array<string, mixed> $options
     */
    public function delete(string $uri, array $options = []): ResponseInterface {
        return parent::delete($this->prefixUri($uri), $options);
    }
}

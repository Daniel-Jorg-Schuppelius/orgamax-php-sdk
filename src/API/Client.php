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
use Orgamax\API\Endpoints\AuthEndpoint;
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
 * Da Guzzle pfadbehaftete base_uri nicht zuverlässig auflöst, wird der
 * Basispfad /openapi hier selbst vorangestellt: Endpoints verwenden
 * API-relative Pfade (z. B. "article/{id}"); absolute URLs und gerootete
 * Pfade ("/...") passieren unverändert.
 */
class Client extends ClientAbstract {
    public const DEFAULT_BASE_URL = 'https://api.orgamax.de';

    public const BASE_PATH = '/openapi';

    public function __construct(
        #[\SensitiveParameter] ?string $token,
        string $baseUrl = self::DEFAULT_BASE_URL,
        ?LoggerInterface $logger = null,
        bool $sleepAfterRequest = false
    ) {
        parent::__construct($baseUrl, $logger, $sleepAfterRequest);

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
        bool $sleepAfterRequest = false
    ): self {
        $client = new self(null, $baseUrl, $logger, $sleepAfterRequest);
        $client->setAuthentication(new BasicAuthentication($apiKey, $apiSecret));

        return $client;
    }

    /**
     * Kompletter Auth-Flow: bezieht mit API-Key, API-Secret und ownershipId
     * ein Bearer-Token und liefert einen damit authentifizierten Client.
     */
    public static function fromCredentials(
        string $apiKey,
        #[\SensitiveParameter] string $apiSecret,
        string $ownershipId,
        string $baseUrl = self::DEFAULT_BASE_URL,
        ?LoggerInterface $logger = null,
        bool $sleepAfterRequest = false
    ): self {
        $authClient = self::forCredentials($apiKey, $apiSecret, $baseUrl, $logger, $sleepAfterRequest);
        $token = (new AuthEndpoint($authClient, $logger))->token($ownershipId);

        return new self($token->getToken(), $baseUrl, $logger, $sleepAfterRequest);
    }

    /**
     * Stellt API-relativen URIs den Basispfad /openapi voran.
     * Absolute URLs (http/https) und gerootete Pfade ("/...") bleiben unverändert.
     */
    protected function prefixUri(string $uri): string {
        if ($uri === '' || str_starts_with($uri, 'http://') || str_starts_with($uri, 'https://') || str_starts_with($uri, '/')) {
            return $uri;
        }

        return self::BASE_PATH . '/' . $uri;
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

<?php
/*
 * Created on   : Wed Jul 29 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : OwnershipTokenAuthentication.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Orgamax\API\Authentication;

use APIToolkit\Contracts\Interfaces\API\RefreshableAuthenticationInterface;
use ERRORToolkit\Traits\ErrorLog;
use GuzzleHttp\Client as HttpClient;
use Orgamax\API\Client;
use Orgamax\API\Endpoints\AuthEndpoint;
use Psr\Log\LoggerInterface;
use Throwable;

/**
 * Bearer-Authentifizierung mit selbstbeschafftem Token.
 *
 * Das JWT der orgaMAX-API läuft ab. Diese Authentifizierung hält API-Key,
 * -Secret und ownershipId und besorgt sich über POST /auth/token bei Bedarf
 * ein neues Token — auch nach einem 401: der ClientAbstract ruft dafür
 * refresh() auf und wiederholt den Request einmal, bevor er den Fehler
 * durchreicht. Ohne sie muss jede einbettende Anwendung den Token-Lebenszyklus
 * selbst nachbauen.
 */
class OwnershipTokenAuthentication implements RefreshableAuthenticationInterface {
    use ErrorLog;

    protected string $apiKey;

    protected string $apiSecret;

    protected string $ownershipId;

    protected string $baseUrl;

    protected ?string $token = null;

    protected ?HttpClient $httpClient;

    public function __construct(
        string $apiKey,
        #[\SensitiveParameter] string $apiSecret,
        string $ownershipId,
        string $baseUrl = Client::DEFAULT_BASE_URL,
        ?string $token = null,
        ?LoggerInterface $logger = null,
        ?HttpClient $httpClient = null
    ) {
        $this->apiKey = $apiKey;
        $this->apiSecret = $apiSecret;
        $this->ownershipId = $ownershipId;
        $this->baseUrl = $baseUrl;
        $this->token = $token;
        $this->httpClient = $httpClient;

        $this->initializeLogger($logger);
    }

    /**
     * @return array<string, mixed>
     */
    public function __debugInfo(): array {
        return [
            'apiKey' => $this->apiKey,
            'apiSecret' => '[redacted]',
            'ownershipId' => $this->ownershipId,
            'token' => $this->token === null ? null : '[redacted]',
        ];
    }

    public function getAuthHeaders(): array {
        if ($this->token === null) {
            $this->refresh();
        }

        return ['Authorization' => 'Bearer ' . ($this->token ?? '')];
    }

    public function getType(): string {
        return 'Bearer';
    }

    public function isValid(): bool {
        return $this->token !== null && $this->token !== ''
            || ($this->apiKey !== '' && $this->apiSecret !== '' && $this->ownershipId !== '');
    }

    /**
     * Das aktuelle Token — null, solange keines beschafft wurde. Anwendungen
     * können es persistieren und beim nächsten Start wieder mitgeben.
     */
    public function getToken(): ?string {
        return $this->token;
    }

    public function refresh(): bool {
        if ($this->apiKey === '' || $this->apiSecret === '' || $this->ownershipId === '') {
            self::logWarning('orgaMAX token refresh skipped: API key, secret or ownershipId missing.');

            return false;
        }

        try {
            $authClient = Client::forCredentials($this->apiKey, $this->apiSecret, $this->baseUrl, self::$logger, false, $this->httpClient);
            $token = (new AuthEndpoint($authClient, self::$logger))->token($this->ownershipId)->getToken();
        } catch (Throwable $e) {
            // Der Vertrag verlangt false statt einer Ausnahme, damit der
            // ursprüngliche Fehler (z. B. 401) durchgereicht werden kann.
            self::logWarning('orgaMAX token refresh failed: ' . $e->getMessage());

            return false;
        }

        if ($token === null || $token === '') {
            self::logWarning('orgaMAX token refresh returned an empty token.');

            return false;
        }

        $this->token = $token;

        return true;
    }
}

<?php
/*
 * Created on   : Wed Jul 29 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : OwnershipTokenAuthenticationTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\API;

use GuzzleHttp\{Client as HttpClient, HandlerStack};
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Orgamax\API\Authentication\OwnershipTokenAuthentication;
use Orgamax\API\Client;
use Orgamax\API\Endpoints\ArticlesEndpoint;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * Das JWT der orgaMAX-API läuft ab. Die Authentifizierung besorgt es selbst
 * und erneuert es nach einem 401, statt den Lebenszyklus jeder einbettenden
 * Anwendung aufzubürden.
 */
class OwnershipTokenAuthenticationTest extends TestCase {
    /** @var array<int, RequestInterface> */
    private array $requests = [];

    private MockHandler $handler;

    /**
     * Guzzles Middleware::history() schreibt in ein array|ArrayAccess und ist
     * damit nicht typisierbar; dieser Recorder hält nur die Requests fest.
     */
    private function recorder(): callable {
        return function (callable $handler): callable {
            return function (RequestInterface $request, array $options) use ($handler) {
                $this->requests[] = $request;

                return $handler($request, $options);
            };
        };
    }

    protected function setUp(): void {
        parent::setUp();
        $this->handler = new MockHandler;
        $this->requests = [];
    }

    public function test_token_is_fetched_on_first_request(): void {
        $this->handler->append(
            new Response(200, [], (string) json_encode(['token' => 'jwt-1'])),
            new Response(200, [], (string) json_encode(['meta' => ['count' => 0, 'totalCount' => 0], 'data' => []])),
        );

        $client = Client::fromCredentials('api-key', 'api-secret', 'ownership-1', Client::DEFAULT_BASE_URL, null, false, $this->httpClient());
        $client->setRequestInterval(0.0);
        (new ArticlesEndpoint($client))->search([]);

        $this->assertCount(2, $this->requests);
        $this->assertSame('https://api.orgamax.de/openapi/auth/token', (string) $this->requests[0]->getUri());
        $this->assertSame('Bearer jwt-1', $this->requests[1]->getHeaderLine('Authorization'));
    }

    public function test_expired_token_is_refreshed_after_401_and_request_is_retried(): void {
        $this->handler->append(
            new Response(401, [], '{"message":"token expired"}'),
            new Response(200, [], (string) json_encode(['token' => 'jwt-2'])),
            new Response(200, [], (string) json_encode(['meta' => ['count' => 0, 'totalCount' => 0], 'data' => []])),
        );

        $client = Client::fromCredentials('api-key', 'api-secret', 'ownership-1', Client::DEFAULT_BASE_URL, null, false, $this->httpClient(), 'jwt-expired');
        $client->setRequestInterval(0.0);
        $list = (new ArticlesEndpoint($client))->search([]);

        $this->assertNotNull($list);
        $this->assertCount(3, $this->requests);
        $this->assertSame('Bearer jwt-expired', $this->requests[0]->getHeaderLine('Authorization'));
        $this->assertSame('https://api.orgamax.de/openapi/auth/token', (string) $this->requests[1]->getUri());
        $this->assertSame('Bearer jwt-2', $this->requests[2]->getHeaderLine('Authorization'));
    }

    public function test_refresh_returns_false_instead_of_throwing_when_credentials_are_missing(): void {
        $auth = new OwnershipTokenAuthentication('', '', '', Client::DEFAULT_BASE_URL, null, null, $this->httpClient());

        $this->assertFalse($auth->refresh());
        $this->assertNull($auth->getToken());
    }

    public function test_refresh_returns_false_when_the_auth_route_fails(): void {
        $this->handler->append(new Response(500, [], '{"message":"boom"}'));

        $auth = new OwnershipTokenAuthentication('api-key', 'api-secret', 'ownership-1', Client::DEFAULT_BASE_URL, null, null, $this->httpClient());

        $this->assertFalse($auth->refresh());
    }

    private function httpClient(): HttpClient {
        $stack = HandlerStack::create($this->handler);
        $stack->push($this->recorder());

        return new HttpClient(['handler' => $stack]);
    }
}

<?php
/*
 * Created on   : Wed Jul 29 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ClientTransportTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\API;

use GuzzleHttp\{Client as HttpClient, HandlerStack};
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Orgamax\API\Client;
use Orgamax\API\Endpoints\{ArticlesEndpoint, AuthEndpoint};
use PHPUnit\Framework\TestCase;

/**
 * Der Guzzle-Transport ist injizierbar: einbettende Anwendungen (und Tests)
 * setzen einen eigenen Handler ein, ohne echte HTTP-Aufrufe zu machen. Die
 * Ziel-URL baut der Client vollständig selbst — eine base_uri des injizierten
 * Transports wird nicht vorausgesetzt.
 */
class ClientTransportTest extends TestCase {
    private MockHandler $handler;

    protected function setUp(): void {
        parent::setUp();
        $this->handler = new MockHandler;
    }

    public function test_injected_transport_receives_absolute_uri_and_bearer_token(): void {
        $this->handler->append(new Response(200, ['Content-Type' => 'application/json'], (string) json_encode([
            'meta' => ['count' => 1, 'totalCount' => 1],
            'data' => [['id' => 90, 'number' => '0015', 'title' => 'My Article', 'unit' => 'Stk.']],
        ])));

        $client = new Client('jwt-token', Client::DEFAULT_BASE_URL, null, false, $this->httpClient());
        $list = (new ArticlesEndpoint($client))->search(['limit' => 20]);

        $this->assertNotNull($list);
        $this->assertSame(1, $list->getMeta()?->getTotalCount());

        $request = $this->handler->getLastRequest();
        $this->assertNotNull($request);
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame('https://api.orgamax.de/openapi/article/?limit=20', (string) $request->getUri());
        $this->assertSame('Bearer jwt-token', $request->getHeaderLine('Authorization'));
    }

    public function test_auth_client_uses_basic_authentication_over_injected_transport(): void {
        $this->handler->append(new Response(200, ['Content-Type' => 'application/json'], (string) json_encode(['token' => 'jwt-token'])));

        $client = Client::forCredentials('api-key', 'api-secret', Client::DEFAULT_BASE_URL, null, false, $this->httpClient());
        $token = (new AuthEndpoint($client))->token('ownership-1');

        $this->assertSame('jwt-token', $token->getToken());

        $request = $this->handler->getLastRequest();
        $this->assertNotNull($request);
        $this->assertSame('https://api.orgamax.de/openapi/auth/token', (string) $request->getUri());
        $this->assertSame('Basic ' . base64_encode('api-key:api-secret'), $request->getHeaderLine('Authorization'));
    }

    private function httpClient(): HttpClient {
        return new HttpClient(['handler' => HandlerStack::create($this->handler)]);
    }
}

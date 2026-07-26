<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : AuthEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use APIToolkit\Exceptions\NotAllowedException;
use Orgamax\API\Endpoints\AuthEndpoint;
use Orgamax\Entities\Auth\Token;
use Tests\Contracts\OfflineEndpointTest;

class AuthEndpointOfflineTest extends OfflineEndpointTest {
    private AuthEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new AuthEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        $tokenBody = json_encode([
            'token' => 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.test.signature',
        ]);
        $this->assertNotFalse($tokenBody);
        $this->mockClient->addResponse('POST', 'auth/token', 200, $tokenBody);
    }

    public function test_token(): void {
        $token = $this->endpoint->token('5facdad6ae8813157f7bcf91');

        $this->assertInstanceOf(Token::class, $token);
        $this->assertSame('eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.test.signature', $token->getToken());
        $this->assertRequestMade('POST', 'auth/token');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame('5facdad6ae8813157f7bcf91', $payload['ownershipId']);
    }

    public function test_get_throws_not_allowed_exception(): void {
        $this->expectException(NotAllowedException::class);
        $this->endpoint->get();
    }
}

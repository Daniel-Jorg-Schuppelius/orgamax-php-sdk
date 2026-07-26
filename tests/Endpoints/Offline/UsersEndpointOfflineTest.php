<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : UsersEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use APIToolkit\Exceptions\NotAllowedException;
use Orgamax\API\Endpoints\UsersEndpoint;
use Orgamax\Entities\Users\UserList;
use Tests\Contracts\OfflineEndpointTest;

class UsersEndpointOfflineTest extends OfflineEndpointTest {
    private UsersEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new UsersEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        $listBody = json_encode([
            'meta' => [
                'count' => 2,
            ],
            'data' => [
                [
                    'id' => '3f2b8c1e-9a41-4d6b-8f1c-2e7a5d9b0c34',
                    'firstName' => 'Max',
                    'lastName' => 'Mustermann',
                    'email' => 'max.mustermann@example.org',
                ],
                [
                    'id' => '7a1d4e92-5c3b-4f8a-b6d2-0e9c8f7a6b51',
                    'firstName' => 'Erika',
                    'lastName' => 'Musterfrau',
                    'email' => 'erika.musterfrau@example.org',
                ],
            ],
        ]);
        $this->assertNotFalse($listBody);
        // Query-Parameter werden vom Toolkit an die URI angehängt, daher Wildcard.
        $this->mockClient->addResponse('GET', 'user*', 200, $listBody);
    }

    public function test_search_users(): void {
        $result = $this->endpoint->search(['limit' => 2]);

        $this->assertInstanceOf(UserList::class, $result);
        $this->assertEquals(2, $result->getMeta()?->getCount());
        $this->assertCount(2, $result->getValues());
        $this->assertEquals('3f2b8c1e-9a41-4d6b-8f1c-2e7a5d9b0c34', $result->getValues()[0]->getId());
        $this->assertEquals('Max', $result->getValues()[0]->getFirstName());
        $this->assertEquals('Musterfrau', $result->getValues()[1]->getLastName());
        $this->assertEquals('erika.musterfrau@example.org', $result->getValues()[1]->getEmail());
        $this->assertRequestMade('GET', 'user*');
    }

    public function test_get_user_throws_not_allowed(): void {
        $this->expectException(NotAllowedException::class);
        $this->endpoint->get();
    }
}

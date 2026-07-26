<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : TagsEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use APIToolkit\Exceptions\NotAllowedException;
use Orgamax\API\Endpoints\TagsEndpoint;
use Orgamax\Entities\Tags\TagList;
use Tests\Contracts\OfflineEndpointTest;

class TagsEndpointOfflineTest extends OfflineEndpointTest {
    private TagsEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new TagsEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        $listBody = json_encode([
            'meta' => [
                'count' => 2,
            ],
            'data' => [
                ['id' => 1, 'label' => 'VIP', 'backgroundColor' => '#d32f2f', 'color' => '#ffffff'],
                ['id' => 2, 'label' => 'Neukunde', 'backgroundColor' => '#1976d2', 'color' => '#ffffff'],
            ],
        ]);
        $this->assertNotFalse($listBody);
        // Query-Parameter werden vom Toolkit an die URI angehängt, daher Wildcard.
        $this->mockClient->addResponse('GET', 'tags*', 200, $listBody);
    }

    public function test_search_tags(): void {
        $result = $this->endpoint->search();

        $this->assertInstanceOf(TagList::class, $result);
        $this->assertEquals(2, $result->getMeta()?->getCount());
        $this->assertCount(2, $result->getValues());
        $this->assertEquals('VIP', $result->getValues()[0]->getLabel());
        $this->assertEquals('#d32f2f', $result->getValues()[0]->getBackgroundColor());
        $this->assertEquals('#ffffff', $result->getValues()[1]->getColor());
        $this->assertRequestMade('GET', 'tags*');
    }

    public function test_get_tag_throws_not_allowed(): void {
        $this->expectException(NotAllowedException::class);
        $this->endpoint->get();
    }
}

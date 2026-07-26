<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ArticlesEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\API\Endpoints\ArticlesEndpoint;
use Orgamax\Entities\Articles\{Article, ArticleList};
use Orgamax\Entities\Common\ResourceResponse;
use Tests\Contracts\OfflineEndpointTest;

class ArticlesEndpointOfflineTest extends OfflineEndpointTest {
    private ArticlesEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new ArticlesEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        $createBody = json_encode([
            'meta' => [],
            'data' => ['id' => '90'],
        ]);
        $this->assertNotFalse($createBody);
        $this->mockClient->addResponse('POST', 'article/', 201, $createBody);

        $getBody = json_encode([
            'meta' => [],
            'data' => [
                'id' => 90,
                'number' => '0015',
                'title' => 'My Article',
                'unit' => 'Stk.',
                'calculationBase' => 'net',
                'price' => 200.25,
                'vatPercent' => 19,
                'category' => 'Zubehör',
                'isStockManaged' => false,
                'graduatedPriceList' => [
                    ['quantity' => 1000, 'netUnitPrice' => 40],
                ],
                'images' => [
                    ['id' => 'img-1', 'url' => 'https://example.org/img-1.png'],
                ],
            ],
        ]);
        $this->assertNotFalse($getBody);
        $this->mockClient->addResponse('GET', 'article/90', 200, $getBody);

        $listBody = json_encode([
            'meta' => [
                'count' => 2,
                'totalCount' => 42,
                'filter' => ['default' => []],
            ],
            'data' => [
                ['id' => 90, 'number' => '0015', 'title' => 'My Article', 'price' => 200.25, 'vatPercent' => 19],
                ['id' => 91, 'number' => '0016', 'title' => 'Other Article', 'price' => 99.90, 'vatPercent' => 7],
            ],
        ]);
        $this->assertNotFalse($listBody);
        // Query-Parameter werden vom Toolkit an die URI angehängt, daher Wildcard.
        // GET article/90 ist zuvor registriert und matcht exakt weiterhin zuerst.
        $this->mockClient->addResponse('GET', 'article/*', 200, $listBody);

        $updateBody = json_encode([
            'data' => ['id' => '90'],
        ]);
        $this->assertNotFalse($updateBody);
        $this->mockClient->addResponse('PUT', 'article/90', 200, $updateBody);

        $this->mockClient->addResponse('DELETE', 'article/90', 204, '');
    }

    public function test_create_article(): void {
        $article = new Article([
            'title' => 'My Article',
            'unit' => 'Stk.',
            'number' => '0015',
            'price' => 200.25,
            'vatPercent' => 19,
        ]);

        $result = $this->endpoint->create($article);

        $this->assertInstanceOf(ResourceResponse::class, $result);
        $this->assertEquals('90', $result->getData()?->getId());
        $this->assertRequestMade('POST', 'article/');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame('My Article', $payload['title']);
        $this->assertSame('Stk.', $payload['unit']);
        $this->assertArrayNotHasKey('id', $payload);
    }

    public function test_create_invalid_article_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->create(new Article(['title' => 'Incomplete']));
        $this->assertNoRequestsMade();
    }

    public function test_get_article(): void {
        $article = $this->endpoint->get(new ID(90));

        $this->assertInstanceOf(Article::class, $article);
        $this->assertEquals(90, $article->getId());
        $this->assertEquals('My Article', $article->getTitle());
        $this->assertEquals('net', $article->getCalculationBase()?->value);
        $this->assertEquals(19.0, $article->getVatPercent());
        $this->assertCount(1, $article->getGraduatedPriceList() ?? []);
        $this->assertEquals('img-1', $article->getImages()?->getValues()[0]->getId());
        $this->assertRequestMade('GET', 'article/90');
    }

    public function test_get_article_without_id_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->get();
    }

    public function test_search_articles(): void {
        $result = $this->endpoint->search(['limit' => 2]);

        $this->assertInstanceOf(ArticleList::class, $result);
        $this->assertEquals(2, $result->getMeta()?->getCount());
        $this->assertEquals(42, $result->getMeta()?->getTotalCount());
        $this->assertCount(2, $result->getValues());
        $this->assertEquals('Other Article', $result->getValues()[1]->getTitle());
        $this->assertRequestMade('GET', 'article/*');
    }

    public function test_update_article(): void {
        $article = new Article([
            'title' => 'My Article',
            'unit' => 'Stk.',
            'number' => '0015',
            'price' => 100,
        ]);

        $result = $this->endpoint->update(new ID(90), $article);

        $this->assertInstanceOf(ResourceResponse::class, $result);
        $this->assertEquals('90', $result->getData()?->getId());
        $this->assertRequestMade('PUT', 'article/90');
    }

    public function test_delete_article(): void {
        $this->assertTrue($this->endpoint->delete(new ID(90)));
        $this->assertRequestMade('DELETE', 'article/90');
    }
}

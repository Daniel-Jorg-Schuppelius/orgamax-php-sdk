<?php
/*
 * Created on   : Wed Jul 29 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : PagedSearchTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\API;

use GuzzleHttp\{Client as HttpClient, HandlerStack};
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Response;
use Orgamax\API\Client;
use Orgamax\API\Endpoints\ArticlesEndpoint;
use Orgamax\Entities\Articles\Article;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

/**
 * searchAll() läuft limit/offset über den OffsetPaginator des api-toolkits
 * durch, statt Aufrufern die Offset-Rechnung zu überlassen.
 */
class PagedSearchTest extends TestCase {
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

    public function test_search_all_walks_every_offset(): void {
        $this->handler->append(
            new Response(200, [], $this->page([90, 91], 3)),
            new Response(200, [], $this->page([92], 3)),
        );

        $numbers = [];
        foreach ((new ArticlesEndpoint($this->client()))->searchAll([], 2) as $article) {
            $this->assertInstanceOf(Article::class, $article);
            $numbers[] = $article->getId();
        }

        $this->assertSame([90, 91, 92], $numbers);
        $this->assertCount(2, $this->requests);
        $this->assertStringContainsString('limit=2&offset=0', (string) $this->requests[0]->getUri());
        $this->assertStringContainsString('limit=2&offset=2', (string) $this->requests[1]->getUri());
    }

    public function test_search_all_stops_on_a_short_page(): void {
        $this->handler->append(new Response(200, [], $this->page([90], 1)));

        $this->assertCount(1, iterator_to_array((new ArticlesEndpoint($this->client()))->searchAll([], 5), false));
        $this->assertCount(1, $this->requests);
    }

    public function test_limit_is_capped_at_the_server_maximum(): void {
        $this->handler->append(new Response(200, [], $this->page([90], 1)));

        iterator_to_array((new ArticlesEndpoint($this->client()))->searchAll([], 5000), false);

        $this->assertStringContainsString('limit=250', (string) $this->requests[0]->getUri());
    }

    /**
     * @param array<int, int> $ids
     */
    private function page(array $ids, int $totalCount): string {
        return (string) json_encode([
            'meta' => ['count' => count($ids), 'totalCount' => $totalCount],
            'data' => array_map(static fn (int $id): array => [
                'id' => $id,
                'number' => (string) $id,
                'title' => 'Artikel ' . $id,
                'unit' => 'Stk.',
            ], $ids),
        ]);
    }

    private function client(): Client {
        $stack = HandlerStack::create($this->handler);
        $stack->push($this->recorder());

        $client = new Client('jwt-token', Client::DEFAULT_BASE_URL, null, false, new HttpClient(['handler' => $stack]));
        $client->setRequestInterval(0.0);

        return $client;
    }
}

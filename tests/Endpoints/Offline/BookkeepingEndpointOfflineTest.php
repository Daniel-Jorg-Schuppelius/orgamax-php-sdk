<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : BookkeepingEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use APIToolkit\Exceptions\NotAllowedException;
use Orgamax\API\Endpoints\BookkeepingEndpoint;
use Orgamax\Entities\Bookkeeping\ChartOfAccountsList;
use Tests\Contracts\OfflineEndpointTest;

class BookkeepingEndpointOfflineTest extends OfflineEndpointTest {
    private BookkeepingEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new BookkeepingEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        $listBody = json_encode([
            'meta' => [
                'count' => 2,
            ],
            'data' => [
                [
                    'id' => '9b4e2f7a-1c3d-4a8b-9e6f-5d0c8b7a2e13',
                    'accountNo' => 8400,
                    'accountDescriptionLong' => 'Erlöse 19 % USt',
                    'accountDescriptionShort' => 'Erlöse 19%',
                    'accountDescriptionCustom' => 'Standarderlöse',
                    'accountCategory' => 'revenue',
                    'allowedVatRates' => ['vatRates' => [19]],
                ],
                [
                    'id' => '4c8a1d6e-7b2f-4e9c-8a3d-1f5e0b9c6d24',
                    'accountNo' => 8300,
                    'accountDescriptionLong' => 'Erlöse 7 % USt',
                    'accountDescriptionShort' => 'Erlöse 7%',
                    'accountCategory' => 'revenue',
                    'allowedVatRates' => ['vatRates' => [7]],
                ],
            ],
        ]);
        $this->assertNotFalse($listBody);
        // Query-Parameter werden vom Toolkit an die URI angehängt, daher Wildcard.
        $this->mockClient->addResponse('GET', 'bookkeeping/getchartofaccounts*', 200, $listBody);
    }

    public function test_get_chart_of_accounts(): void {
        $result = $this->endpoint->getChartOfAccounts(['limit' => 2]);

        $this->assertInstanceOf(ChartOfAccountsList::class, $result);
        $this->assertEquals(2, $result->getMeta()?->getCount());
        $this->assertCount(2, $result->getValues());
        $this->assertEquals('9b4e2f7a-1c3d-4a8b-9e6f-5d0c8b7a2e13', $result->getValues()[0]->getId());
        $this->assertEquals(8400.0, $result->getValues()[0]->getAccountNo());
        $this->assertEquals('Erlöse 19 % USt', $result->getValues()[0]->getAccountDescriptionLong());
        $this->assertEquals('revenue', $result->getValues()[1]->getAccountCategory());
        $this->assertEquals(['vatRates' => [7]], $result->getValues()[1]->getAllowedVatRates());
        $this->assertRequestMade('GET', 'bookkeeping/getchartofaccounts*');
    }

    public function test_search_is_alias_for_get_chart_of_accounts(): void {
        $result = $this->endpoint->search();

        $this->assertInstanceOf(ChartOfAccountsList::class, $result);
        $this->assertCount(2, $result->getValues());
        $this->assertEquals('Erlöse 7%', $result->getValues()[1]->getAccountDescriptionShort());
        $this->assertRequestMade('GET', 'bookkeeping/getchartofaccounts*');
    }

    public function test_get_throws_not_allowed(): void {
        $this->expectException(NotAllowedException::class);
        $this->endpoint->get();
    }
}

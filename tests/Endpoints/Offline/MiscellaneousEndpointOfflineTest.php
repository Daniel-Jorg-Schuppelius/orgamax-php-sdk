<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : MiscellaneousEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use Orgamax\API\Endpoints\Settings\MiscellaneousEndpoint;
use Orgamax\Entities\Settings\Miscellaneous;
use Tests\Contracts\OfflineEndpointTest;

class MiscellaneousEndpointOfflineTest extends OfflineEndpointTest {
    private MiscellaneousEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new MiscellaneousEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        $body = json_encode([
            'data' => [
                'articleCategories' => ['Zubehör', 'Werbung'],
                'articleUnits' => ['Stk.', 'Liter'],
                'customerCategories' => ['Endkunden', 'Agentur'],
                'jobTitles' => ['Abteilungsleiter', 'Inhaber'],
                'salutations' => ['Herr', 'Frau'],
                'titles' => ['Dipl.-Ing', 'Dr.'],
            ],
        ]);
        $this->assertNotFalse($body);
        $this->mockClient->addResponse('GET', 'setting/miscellaneous', 200, $body);
    }

    public function test_get_miscellaneous(): void {
        $result = $this->endpoint->get();

        $this->assertInstanceOf(Miscellaneous::class, $result);
        $this->assertEquals(['Zubehör', 'Werbung'], $result->getData()?->getArticleCategories());
        $this->assertEquals(['Stk.', 'Liter'], $result->getData()?->getArticleUnits());
        $this->assertEquals(['Endkunden', 'Agentur'], $result->getData()?->getCustomerCategories());
        $this->assertEquals(['Abteilungsleiter', 'Inhaber'], $result->getData()?->getJobTitles());
        $this->assertEquals(['Herr', 'Frau'], $result->getData()?->getSalutations());
        $this->assertEquals(['Dipl.-Ing', 'Dr.'], $result->getData()?->getTitles());
        $this->assertRequestMade('GET', 'setting/miscellaneous');
    }
}

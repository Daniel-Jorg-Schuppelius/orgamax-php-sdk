<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : PayConditionsEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\API\Endpoints\Settings\PayConditionsEndpoint;
use Orgamax\Entities\Settings\{PayCondition, PayConditionList, PayConditions};
use Tests\Contracts\OfflineEndpointTest;

class PayConditionsEndpointOfflineTest extends OfflineEndpointTest {
    private PayConditionsEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new PayConditionsEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        $getBody = json_encode([
            'meta' => [],
            'data' => [
                'id' => 7,
                'name' => '14 Tage netto',
                'isBasic' => false,
                'offerText' => 'Please pay in {{dueDays}} days',
                'invoiceText' => 'Please pay in {{dueDays}} days',
                'dueDays' => 14,
            ],
        ]);
        $this->assertNotFalse($getBody);
        $this->mockClient->addResponse('GET', 'setting/payCondition/7', 200, $getBody);

        // GET /setting/payCondition (alle) liefert {"payConditionData": [...]}.
        $listBody = json_encode([
            'payConditionData' => [
                ['id' => 7, 'name' => '14 Tage netto', 'isBasic' => false, 'dueDays' => 14],
                ['id' => 8, 'name' => 'Sofort', 'isBasic' => true, 'dueDays' => 0],
            ],
        ]);
        $this->assertNotFalse($listBody);
        $this->mockClient->addResponse('GET', 'setting/payCondition*', 200, $listBody);

        // POST/PUT antworten mit einem Array aller PayConditions.
        $mutationBody = json_encode([
            ['id' => 7, 'name' => '14 Tage netto', 'isBasic' => false, 'dueDays' => 14],
            ['id' => 9, 'name' => 'My PayCondition', 'invoiceText' => 'My invoice text', 'offerText' => 'My offer Text', 'dueDays' => 14, 'isBasic' => false],
        ]);
        $this->assertNotFalse($mutationBody);
        $this->mockClient->addResponse('POST', 'setting/payCondition/', 200, $mutationBody);
        $this->mockClient->addResponse('PUT', 'setting/payCondition/', 200, $mutationBody);
    }

    public function test_search_pay_conditions(): void {
        $result = $this->endpoint->search();

        $this->assertInstanceOf(PayConditionList::class, $result);
        $this->assertCount(2, $result->getValues());
        $this->assertEquals('14 Tage netto', $result->getValues()[0]->getName());
        $this->assertEquals('Sofort', $result->getValues()[1]->getName());
        $this->assertTrue($result->getValues()[1]->isBasic());
        $this->assertRequestMade('GET', 'setting/payCondition');
    }

    public function test_get_pay_condition(): void {
        $payCondition = $this->endpoint->get(new ID(7));

        $this->assertInstanceOf(PayCondition::class, $payCondition);
        $this->assertEquals(7, $payCondition->getId());
        $this->assertEquals('14 Tage netto', $payCondition->getName());
        $this->assertEquals(14, $payCondition->getDueDays());
        $this->assertEquals('Please pay in {{dueDays}} days', $payCondition->getInvoiceText());
        $this->assertRequestMade('GET', 'setting/payCondition/7');
    }

    public function test_get_pay_condition_without_id_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->get();
    }

    public function test_create_pay_condition(): void {
        $payCondition = new PayCondition([
            'name' => 'My PayCondition',
            'invoiceText' => 'My invoice text',
            'offerText' => 'My offer Text',
            'dueDays' => 14,
        ]);

        $result = $this->endpoint->create($payCondition);

        $this->assertInstanceOf(PayConditions::class, $result);
        $this->assertCount(2, $result->getValues());
        $this->assertEquals('My PayCondition', $result->getValues()[1]->getName());
        $this->assertRequestMade('POST', 'setting/payCondition/');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame('My PayCondition', $payload['name']);
        $this->assertSame(14, $payload['dueDays']);
        $this->assertArrayNotHasKey('id', $payload);
    }

    public function test_create_invalid_pay_condition_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->create(new PayCondition(['dueDays' => 14]));
        $this->assertNoRequestsMade();
    }

    public function test_update_pay_condition(): void {
        $payCondition = new PayCondition([
            'id' => 9,
            'name' => 'My PayCondition',
            'invoiceText' => 'My invoice text',
            'offerText' => 'My offer Text',
            'dueDays' => 14,
        ]);

        $result = $this->endpoint->update($payCondition);

        $this->assertInstanceOf(PayConditions::class, $result);
        $this->assertCount(2, $result->getValues());
        // PUT geht OHNE id im Pfad auf setting/payCondition/ — die id steht im Body.
        $this->assertRequestMade('PUT', 'setting/payCondition/');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame(9, $payload['id']);
        $this->assertSame('My PayCondition', $payload['name']);
    }

    public function test_update_pay_condition_without_id_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->update(new PayCondition(['name' => 'My PayCondition']));
        $this->assertNoRequestsMade();
    }
}

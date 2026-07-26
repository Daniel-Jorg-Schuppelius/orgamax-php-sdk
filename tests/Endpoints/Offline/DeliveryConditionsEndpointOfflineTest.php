<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : DeliveryConditionsEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\API\Endpoints\Settings\DeliveryConditionsEndpoint;
use Orgamax\Entities\Settings\{DeliveryCondition, DeliveryConditionList};
use Tests\Contracts\OfflineEndpointTest;

class DeliveryConditionsEndpointOfflineTest extends OfflineEndpointTest {
    private DeliveryConditionsEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new DeliveryConditionsEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        $getBody = json_encode([
            'meta' => [],
            'data' => [
                'id' => 3,
                'name' => 'Frei Haus',
                'isDefault' => true,
                'text' => 'My delivery text',
                'deliveryDays' => 5,
            ],
        ]);
        $this->assertNotFalse($getBody);
        $this->mockClient->addResponse('GET', 'setting/deliveryCondition/3', 200, $getBody);

        // GET /setting/deliveryCondition (alle) liefert {"deliveryConditionData": [...]}.
        $listBody = json_encode([
            'deliveryConditionData' => [
                ['id' => 3, 'name' => 'Frei Haus', 'isDefault' => true, 'deliveryDays' => 5],
                ['id' => 4, 'name' => 'Ab Werk', 'isDefault' => false, 'deliveryDays' => 14],
            ],
        ]);
        $this->assertNotFalse($listBody);
        $this->mockClient->addResponse('GET', 'setting/deliveryCondition*', 200, $listBody);

        // POST liefert die angelegte DeliveryCondition als Objekt zurück (200).
        $createBody = json_encode([
            'id' => 5,
            'name' => 'My DeliveryCondition',
            'text' => 'My delivery text',
            'deliveryDays' => 5,
            'isDefault' => false,
        ]);
        $this->assertNotFalse($createBody);
        $this->mockClient->addResponse('POST', 'setting/deliveryCondition/', 200, $createBody);

        // PUT /setting/deliveryCondition/{id} antwortet mit 204 (no content).
        $this->mockClient->addResponse('PUT', 'setting/deliveryCondition/3', 204, '');
    }

    public function test_search_delivery_conditions(): void {
        $result = $this->endpoint->search();

        $this->assertInstanceOf(DeliveryConditionList::class, $result);
        $this->assertCount(2, $result->getValues());
        $this->assertEquals('Frei Haus', $result->getValues()[0]->getName());
        $this->assertTrue($result->getValues()[0]->isDefault());
        $this->assertEquals('Ab Werk', $result->getValues()[1]->getName());
        $this->assertRequestMade('GET', 'setting/deliveryCondition');
    }

    public function test_get_delivery_condition(): void {
        $deliveryCondition = $this->endpoint->get(new ID(3));

        $this->assertInstanceOf(DeliveryCondition::class, $deliveryCondition);
        $this->assertEquals(3, $deliveryCondition->getId());
        $this->assertEquals('Frei Haus', $deliveryCondition->getName());
        $this->assertEquals(5.0, $deliveryCondition->getDeliveryDays());
        $this->assertTrue($deliveryCondition->isDefault());
        $this->assertRequestMade('GET', 'setting/deliveryCondition/3');
    }

    public function test_get_delivery_condition_without_id_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->get();
    }

    public function test_create_delivery_condition(): void {
        $deliveryCondition = new DeliveryCondition([
            'name' => 'My DeliveryCondition',
            'text' => 'My delivery text',
            'deliveryDays' => 5,
        ]);

        $result = $this->endpoint->create($deliveryCondition);

        $this->assertInstanceOf(DeliveryCondition::class, $result);
        $this->assertEquals(5, $result->getId());
        $this->assertEquals('My DeliveryCondition', $result->getName());
        $this->assertRequestMade('POST', 'setting/deliveryCondition/');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame('My DeliveryCondition', $payload['name']);
        $this->assertSame('My delivery text', $payload['text']);
        $this->assertArrayNotHasKey('id', $payload);
    }

    public function test_create_invalid_delivery_condition_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->create(new DeliveryCondition(['deliveryDays' => 5]));
        $this->assertNoRequestsMade();
    }

    public function test_update_delivery_condition(): void {
        $deliveryCondition = new DeliveryCondition([
            'name' => 'My DeliveryCondition',
            'text' => 'My delivery text',
            'deliveryDays' => 14,
        ]);

        $this->assertTrue($this->endpoint->update(new ID(3), $deliveryCondition));
        $this->assertRequestMade('PUT', 'setting/deliveryCondition/3');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame('My DeliveryCondition', $payload['name']);
        $this->assertSame(14.0, $payload['deliveryDays']);
    }
}

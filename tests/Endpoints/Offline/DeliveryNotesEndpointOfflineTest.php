<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : DeliveryNotesEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\API\Endpoints\DeliveryNotesEndpoint;
use Orgamax\Entities\DeliveryNotes\{DeliveryNote, DeliveryNoteList};
use Orgamax\Enums\DeliveryNoteState;
use Tests\Contracts\OfflineEndpointTest;

class DeliveryNotesEndpointOfflineTest extends OfflineEndpointTest {
    private DeliveryNotesEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new DeliveryNotesEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        // Spezifischere Patterns vor den Wildcards registrieren.
        $this->mockClient->addResponse(
            'GET',
            'deliveryNote/document/7*',
            200,
            '%PDF-1.7 mock delivery note document',
            ['Content-Type' => 'application/pdf']
        );

        $getBody = json_encode([
            'meta' => [],
            'data' => [
                'id' => 7,
                'number' => 'LS-2026-0007',
                'date' => '2026-07-20',
                'state' => 'delivered',
                'customerId' => 12,
                'orderId' => 33,
                'orderNumber' => 'AB-2026-0033',
                'deliveryConditionId' => '2',
                'letterNumerationId' => '5',
                'letterPaperSettingId' => '1',
                'sentAt' => '2026-07-21T09:30:00+02:00',
                'notes' => 'Bitte an der Rampe abgeben',
                'customerData' => [
                    'city' => 'Berlin',
                    'companyName' => 'Muster GmbH',
                    'country' => 'Deutschland',
                    'countryIso' => 'DE',
                    'kind' => 'company',
                    'name' => 'Max Mustermann',
                    'number' => '10012',
                    'street' => 'Musterstraße 1',
                    'zip' => '10115',
                ],
                'deliveryConditionData' => [
                    'id' => 2,
                    'name' => 'Standardversand',
                    'isDefault' => true,
                    'text' => 'Lieferung frei Haus',
                    'deliveryDays' => 3,
                ],
                'positions' => [
                    [
                        'id' => 'pos-1',
                        'title' => 'My Article',
                        'description' => 'Artikelbeschreibung',
                        'showDescription' => true,
                        'amount' => 5,
                        'unit' => 'Stk.',
                        'metaData' => ['id' => 90, 'type' => 'article', 'number' => '0015'],
                    ],
                    [
                        'id' => 'pos-2',
                        'title' => 'Other Article',
                        'amount' => 2,
                        'unit' => 'Stk.',
                    ],
                ],
            ],
        ]);
        $this->assertNotFalse($getBody);
        $this->mockClient->addResponse('GET', 'deliveryNote/7', 200, $getBody);

        $listBody = json_encode([
            'meta' => [
                'count' => 2,
                'filter' => ['all' => [], 'delivered' => [], 'draft' => []],
            ],
            'data' => [
                [
                    'id' => 7,
                    'number' => 'LS-2026-0007',
                    'date' => '2026-07-20',
                    'state' => 'delivered',
                    'customerId' => 12,
                    'orderId' => 33,
                    'orderNumber' => 'AB-2026-0033',
                    'customerData' => ['companyName' => 'Muster GmbH', 'city' => 'Berlin'],
                ],
                [
                    'id' => 8,
                    'number' => 'LS-2026-0008',
                    'date' => '2026-07-22',
                    'state' => 'draft',
                    'customerId' => 15,
                    'customerData' => ['name' => 'Erika Musterfrau', 'city' => 'Hamburg'],
                ],
            ],
        ]);
        $this->assertNotFalse($listBody);
        // Query-Parameter werden an die URI angehängt, daher Wildcard.
        $this->mockClient->addResponse('GET', 'deliveryNote*', 200, $listBody);
    }

    public function test_get_delivery_note(): void {
        $deliveryNote = $this->endpoint->get(new ID(7));

        $this->assertInstanceOf(DeliveryNote::class, $deliveryNote);
        $this->assertEquals(7, $deliveryNote->getId());
        $this->assertEquals('LS-2026-0007', $deliveryNote->getNumber());
        $this->assertEquals('2026-07-20', $deliveryNote->getDate());
        $this->assertEquals(DeliveryNoteState::DELIVERED, $deliveryNote->getState());
        $this->assertEquals(12, $deliveryNote->getCustomerId());
        $this->assertEquals(33, $deliveryNote->getOrderId());
        $this->assertEquals('AB-2026-0033', $deliveryNote->getOrderNumber());
        $this->assertEquals('2', $deliveryNote->getDeliveryConditionId());
        $this->assertEquals('2026-07-21', $deliveryNote->getSentAt()?->format('Y-m-d'));
        $this->assertEquals('Muster GmbH', $deliveryNote->getCustomerData()?->getCompanyName());
        $this->assertEquals('Standardversand', $deliveryNote->getDeliveryConditionData()?->getName());
        $this->assertCount(2, $deliveryNote->getPositions() ?? []);
        $this->assertEquals('My Article', $deliveryNote->getPositions()?->getValues()[0]->getTitle());
        $this->assertEquals('0015', $deliveryNote->getPositions()?->getValues()[0]->getMetaData()?->getNumber());
        $this->assertRequestMade('GET', 'deliveryNote/7');
    }

    public function test_get_delivery_note_without_id_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->get();
    }

    public function test_search_delivery_notes(): void {
        $result = $this->endpoint->search(['limit' => 2]);

        $this->assertInstanceOf(DeliveryNoteList::class, $result);
        $this->assertEquals(2, $result->getMeta()?->getCount());
        $this->assertArrayHasKey('all', $result->getMeta()?->getFilter() ?? []);
        $this->assertArrayHasKey('delivered', $result->getMeta()?->getFilter() ?? []);
        $this->assertArrayHasKey('draft', $result->getMeta()?->getFilter() ?? []);
        $this->assertCount(2, $result->getValues());
        $this->assertEquals('LS-2026-0008', $result->getValues()[1]->getNumber());
        $this->assertEquals(DeliveryNoteState::DRAFT, $result->getValues()[1]->getState());
        $this->assertRequestMade('GET', 'deliveryNote?*');
    }

    public function test_get_delivery_note_document(): void {
        $document = $this->endpoint->document(new ID(7), 'pdf');

        $this->assertStringStartsWith('%PDF', $document);
        $this->assertRequestMade('GET', 'deliveryNote/document/7*');
    }
}

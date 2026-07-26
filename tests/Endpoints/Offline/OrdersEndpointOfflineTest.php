<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : OrdersEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\API\Endpoints\OrdersEndpoint;
use Orgamax\Entities\Common\ResourceResponse;
use Orgamax\Entities\Orders\{Order, OrderInvoiceResponse, OrderList};
use Orgamax\Enums\{InvoiceState, PriceKind, SalesDocumentState};
use Tests\Contracts\OfflineEndpointTest;

class OrdersEndpointOfflineTest extends OfflineEndpointTest {
    private OrdersEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new OrdersEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        $createBody = json_encode([
            'meta' => [],
            'data' => ['id' => '77'],
        ]);
        $this->assertNotFalse($createBody);
        // Die Spec antwortet auf POST order/ mit 200 (nicht 201).
        $this->mockClient->addResponse('POST', 'order/', 200, $createBody);

        $invoiceBody = json_encode([
            'meta' => [],
            'data' => [
                'invoice' => ['id' => 501, 'number' => 'RE-2021-501', 'state' => 'draft', 'totalNet' => 560, 'totalGross' => 666.4],
                'order' => ['id' => 77, 'number' => 'AB-2021-77', 'state' => 'accept', 'invoiceId' => '501', 'totalNet' => 560, 'totalGross' => 666.4],
            ],
        ]);
        $this->assertNotFalse($invoiceBody);
        $this->mockClient->addResponse('POST', 'order/77/invoice', 200, $invoiceBody);

        // Wildcard wegen optionaler Query-Parameter (type, filename).
        $this->mockClient->addResponse(
            'GET',
            'order/document/77*',
            200,
            '%PDF-1.4 mock-order-document',
            ['Content-Type' => 'application/pdf']
        );

        $getBody = json_encode([
            'meta' => [],
            'data' => [
                'id' => 77,
                'number' => 'AB-2021-77',
                'date' => '2021-04-23',
                'customerId' => 1,
                'priceKind' => 'net',
                'state' => 'open',
                'totalNet' => 560,
                'totalGross' => 666.4,
                'outstandingAmount' => 666.4,
                'cashDiscountTotal' => 0,
                'deliveryConditionId' => '1',
                'sentAt' => '2021-04-25T08:15:00+02:00',
                'isLocked' => false,
                'smallBusiness' => false,
                'invoiceId' => '0',
                'payConditionId' => 1,
                'payConditionData' => ['id' => 1, 'name' => '14 Tage netto', 'dueDays' => 14],
                'positions' => [
                    [
                        'id' => '1',
                        'title' => 'Existing Article',
                        'amount' => 2,
                        'unit' => 'Stk.',
                        'priceNet' => 560,
                        'priceGross' => 666.4,
                        'vatPercent' => 19,
                        'metaData' => ['id' => 39998, 'type' => 'article', 'number' => '309', 'calculationBase' => 'net'],
                    ],
                ],
                'customerData' => [
                    'name' => 'Max Mustermann',
                    'number' => '10001',
                    'kind' => 'person',
                    'city' => 'Berlin',
                ],
            ],
        ]);
        $this->assertNotFalse($getBody);
        $this->mockClient->addResponse('GET', 'order/77', 200, $getBody);

        $listBody = json_encode([
            'meta' => [
                'count' => 2,
                'filter' => ['all' => [], 'open' => [], 'invoiced' => []],
            ],
            'data' => [
                ['id' => 77, 'date' => '2021-04-23', 'customerId' => 1, 'number' => 'AB-2021-77', 'totalNet' => 560, 'totalGross' => 666.4, 'state' => 'open', 'invoiceId' => '0'],
                ['id' => 78, 'date' => '2021-04-24', 'customerId' => 2, 'number' => 'AB-2021-78', 'totalNet' => 100, 'totalGross' => 119, 'state' => 'accept', 'invoiceId' => '502'],
            ],
        ]);
        $this->assertNotFalse($listBody);
        // Query-Parameter werden an die URI angehängt, daher Wildcard — nach
        // den spezifischeren Patterns registrieren.
        $this->mockClient->addResponse('GET', 'order*', 200, $listBody);
    }

    public function test_create_order(): void {
        $order = new Order([
            'customerId' => 1,
            'date' => '2021-04-23',
            'priceKind' => 'net',
            'payConditionId' => 1,
            'positions' => [
                [
                    'amount' => 2,
                    'title' => 'Custom Article',
                    'description' => 'Custom Article description',
                    'showDescription' => true,
                    'unit' => 'Stk.',
                    'priceNet' => 50,
                    'priceGross' => 59.5,
                    'vatPercent' => 19,
                    'metaData' => ['type' => 'custom'],
                ],
            ],
        ]);

        $result = $this->endpoint->create($order);

        $this->assertInstanceOf(ResourceResponse::class, $result);
        $this->assertEquals('77', $result->getData()?->getId());
        $this->assertRequestMade('POST', 'order/');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame(1, $payload['customerId']);
        $this->assertSame('2021-04-23', $payload['date']);
        $this->assertSame('net', $payload['priceKind']);
        $this->assertIsArray($payload['positions']);
        $this->assertCount(1, $payload['positions']);
        $this->assertSame('Custom Article', $payload['positions'][0]['title']);
        $this->assertArrayNotHasKey('id', $payload);
    }

    public function test_create_invalid_order_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->create(new Order(['notes' => 'Ohne Kunde']));
        $this->assertNoRequestsMade();
    }

    public function test_get_order(): void {
        $order = $this->endpoint->get(new ID(77));

        $this->assertInstanceOf(Order::class, $order);
        $this->assertEquals(77, $order->getId());
        $this->assertEquals('AB-2021-77', $order->getNumber());
        $this->assertEquals(1, $order->getCustomerId());
        $this->assertEquals(PriceKind::NET, $order->getPriceKind());
        $this->assertEquals(SalesDocumentState::OPEN, $order->getState());
        $this->assertEquals(666.4, $order->getTotalGross());
        $this->assertEquals('2021-04-25', $order->getSentAt()?->format('Y-m-d'));
        $this->assertEquals('14 Tage netto', $order->getPayConditionData()?->getName());
        $this->assertCount(1, $order->getPositions() ?? []);
        $this->assertEquals('Existing Article', $order->getPositions()?->getValues()[0]->getTitle());
        $this->assertEquals('Max Mustermann', $order->getCustomerData()?->getName());
        $this->assertRequestMade('GET', 'order/77');
    }

    public function test_get_order_without_id_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->get();
    }

    public function test_search_orders(): void {
        $result = $this->endpoint->search(['limit' => 2, 'desc' => 'true']);

        $this->assertInstanceOf(OrderList::class, $result);
        $this->assertEquals(2, $result->getMeta()?->getCount());
        $this->assertCount(2, $result->getValues());
        $this->assertEquals('AB-2021-78', $result->getValues()[1]->getNumber());
        $this->assertEquals(SalesDocumentState::ACCEPT, $result->getValues()[1]->getState());
        $this->assertEquals('502', $result->getValues()[1]->getInvoiceId());
        $this->assertRequestMade('GET', 'order?*');
    }

    public function test_get_order_document(): void {
        $document = $this->endpoint->document(new ID(77), 'original');

        $this->assertSame('%PDF-1.4 mock-order-document', $document);
        $this->assertRequestMade('GET', 'order/document/77*');
    }

    public function test_create_invoice_from_order(): void {
        $result = $this->endpoint->createInvoice(new ID(77));

        $this->assertInstanceOf(OrderInvoiceResponse::class, $result);

        $data = $result->getData();
        $this->assertNotNull($data);

        $invoice = $data->getInvoice();
        $this->assertNotNull($invoice);
        $this->assertSame('RE-2021-501', $invoice->getNumber());
        $this->assertSame(InvoiceState::DRAFT, $invoice->getState());

        $order = $data->getOrder();
        $this->assertNotNull($order);
        $this->assertEquals('AB-2021-77', $order->getNumber());
        $this->assertEquals('501', $order->getInvoiceId());
        $this->assertRequestMade('POST', 'order/77/invoice');
    }
}

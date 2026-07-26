<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ExpensesEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\API\Endpoints\ExpensesEndpoint;
use Orgamax\Entities\Common\ResourceResponse;
use Orgamax\Entities\Expenses\{Expense, ExpenseList};
use Tests\Contracts\OfflineEndpointTest;

class ExpensesEndpointOfflineTest extends OfflineEndpointTest {
    private ExpensesEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new ExpensesEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        // POST /expense/ antwortet mit 200 (nicht 201!)
        $createBody = json_encode([
            'meta' => [],
            'data' => ['id' => '90'],
        ]);
        $this->assertNotFalse($createBody);
        $this->mockClient->addResponse('POST', 'expense/', 200, $createBody);

        $getBody = json_encode([
            'meta' => [],
            'data' => [
                'id' => 90,
                'date' => '2019-10-01',
                'payDate' => '2019-10-01',
                'payKind' => 'bank',
                'payee' => 'Tankstelle Hamburg',
                'description' => 'Fahrt zum Kunden xxxx',
                'priceTotal' => 119,
                'vat' => 19,
                'vatAmount' => 19,
                'receiptNumber' => 'abcd1234',
                'supplierId' => '32',
                'outstandingAmount' => 0,
                'financeApiId' => 'fin-4711',
                'receipts' => [],
                'bookings' => [
                    ['id' => 501],
                ],
                'positions' => [
                    [
                        'id' => 12,
                        'bookkeepingAccountNo' => 4530,
                        'bookkeepingAccountDescriptionShort' => 'Kfz-Kosten',
                        'expenseDescription' => 'Fahrt zum Kunden xxxx',
                        'vat' => 19,
                        'vatPercent' => 19,
                        'totalNet' => 100,
                        'totalGross' => 119,
                    ],
                ],
            ],
        ]);
        $this->assertNotFalse($getBody);
        $this->mockClient->addResponse('GET', 'expense/90', 200, $getBody);

        $listBody = json_encode([
            'meta' => [
                'count' => 2,
                'totalCount' => 17,
                'filter' => [
                    'all' => [],
                    'inProgress' => [],
                    'open' => [],
                    'paid' => [],
                ],
            ],
            'data' => [
                [
                    'id' => 90,
                    'date' => '2019-10-01',
                    'payKind' => 'bank',
                    'payee' => 'Tankstelle Hamburg',
                    'price' => 100,
                    'priceTotal' => 119,
                    'vatPercent' => 19,
                    'outstandingAmount' => 0,
                    'receiptCount' => 1,
                ],
                [
                    'id' => 91,
                    'date' => '2019-10-05',
                    'payKind' => 'open',
                    'payee' => 'Bürobedarf Meyer',
                    'price' => 50,
                    'priceTotal' => 59.5,
                    'vatPercent' => 19,
                    'outstandingAmount' => 59.5,
                    'receiptCount' => 0,
                ],
            ],
        ]);
        $this->assertNotFalse($listBody);
        // Query-Parameter werden an die URI angehängt, daher Wildcard.
        // GET expense/90 ist zuvor registriert und matcht weiterhin zuerst.
        $this->mockClient->addResponse('GET', 'expense*', 200, $listBody);

        // PUT /expense/{id} antwortet mit 204 ohne Body.
        $this->mockClient->addResponse('PUT', 'expense/90', 204, '');

        $this->mockClient->addResponse('DELETE', 'expense/receipt/12', 204, '');
        $this->mockClient->addResponse('DELETE', 'expense/90', 204, '');
    }

    public function test_create_expense(): void {
        $expense = new Expense([
            'date' => '2019-10-01',
            'payee' => 'Tankstelle Hamburg',
            'payDate' => '2019-10-01',
            'description' => 'Fahrt zum Kunden xxxx',
            'priceTotal' => 119,
            'vatPercent' => 19,
            'payKind' => 'bank',
            'supplierId' => '32',
            'receipts' => [],
            'positions' => [
                ['totalNet' => 100, 'totalGross' => 119, 'vatPercent' => 19, 'vat' => 19],
            ],
        ]);

        $result = $this->endpoint->create($expense);

        $this->assertInstanceOf(ResourceResponse::class, $result);
        $this->assertEquals('90', $result->getData()?->getId());
        $this->assertRequestMade('POST', 'expense/');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame('Tankstelle Hamburg', $payload['payee']);
        $this->assertSame('bank', $payload['payKind']);
        $this->assertEquals(119, $payload['priceTotal']);
        $this->assertIsArray($payload['positions']);
        $this->assertEquals(100, $payload['positions'][0]['totalNet']);
        $this->assertArrayNotHasKey('id', $payload);
    }

    public function test_get_expense(): void {
        $expense = $this->endpoint->get(new ID(90));

        $this->assertInstanceOf(Expense::class, $expense);
        $this->assertEquals(90, $expense->getId());
        $this->assertEquals('Tankstelle Hamburg', $expense->getPayee());
        $this->assertEquals('bank', $expense->getPayKind()?->value);
        $this->assertEquals(119.0, $expense->getPriceTotal());
        $this->assertEquals('abcd1234', $expense->getReceiptNumber());
        $this->assertEquals(501, $expense->getBookings()?->getValues()[0]->getId());
        $position = $expense->getPositions()?->getValues()[0] ?? null;
        $this->assertNotNull($position);
        $this->assertEquals(4530.0, $position->getBookkeepingAccountNo());
        $this->assertEquals('Kfz-Kosten', $position->getBookkeepingAccountDescriptionShort());
        $this->assertEquals(100.0, $position->getTotalNet());
        $this->assertRequestMade('GET', 'expense/90');
    }

    public function test_get_expense_without_id_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->get();
    }

    public function test_search_expenses(): void {
        $result = $this->endpoint->search(['limit' => 2, 'payKind' => 'bank']);

        $this->assertInstanceOf(ExpenseList::class, $result);
        $this->assertEquals(2, $result->getMeta()?->getCount());
        $this->assertEquals(17, $result->getMeta()?->getTotalCount());
        $this->assertArrayHasKey('inProgress', $result->getMeta()?->getFilter() ?? []);
        $this->assertCount(2, $result->getValues());
        $this->assertEquals('Bürobedarf Meyer', $result->getValues()[1]->getPayee());
        $this->assertEquals(59.5, $result->getValues()[1]->getOutstandingAmount());
        $this->assertRequestMade('GET', 'expense*');
    }

    public function test_update_expense(): void {
        $expense = new Expense([
            'description' => 'My description',
            'receiptNumber' => 'abcd1234',
            'positions' => [
                ['vat' => 31.93, 'vatPercent' => 19, 'totalNet' => 168.07, 'expenseDescription' => 'test', 'totalGross' => 200],
            ],
        ]);

        $this->assertTrue($this->endpoint->update(new ID(90), $expense));
        $this->assertRequestMade('PUT', 'expense/90');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame('My description', $payload['description']);
        $this->assertSame('abcd1234', $payload['receiptNumber']);
        $this->assertEquals(168.07, $payload['positions'][0]['totalNet']);
    }

    public function test_delete_expense(): void {
        $this->assertTrue($this->endpoint->delete(new ID(90)));
        $this->assertRequestMade('DELETE', 'expense/90');
    }

    public function test_delete_expense_receipt(): void {
        $this->assertTrue($this->endpoint->deleteReceipt(new ID(12)));
        $this->assertRequestMade('DELETE', 'expense/receipt/12');
    }
}

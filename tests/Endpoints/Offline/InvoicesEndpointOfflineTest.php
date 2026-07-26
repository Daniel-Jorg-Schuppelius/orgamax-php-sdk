<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : InvoicesEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\API\Endpoints\InvoicesEndpoint;
use Orgamax\Entities\Invoices\{Invoice, InvoiceList, InvoiceLockInfo, InvoicePayment, InvoicePayments};
use Orgamax\Enums\PaymentType;
use Tests\Contracts\OfflineEndpointTest;

class InvoicesEndpointOfflineTest extends OfflineEndpointTest {
    private const PDF_BODY = "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n%%EOF";

    private InvoicesEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new InvoicesEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        $getBody = json_encode([
            'meta' => [],
            'data' => [
                'id' => 74,
                'number' => 'RE-2020-1001',
                'date' => '2020-11-01',
                'type' => 'invoice',
                'priceKind' => 'net',
                'state' => 'locked',
                'totalNet' => 10.39,
                'totalGross' => 12.37,
                'outstandingAmount' => 12.37,
                'cashDiscountTotal' => 0,
                'dueToDate' => '2020-11-15',
                'customerData' => [
                    'city' => 'Berlin',
                    'companyName' => 'ACME GmbH',
                    'country' => 'Deutschland',
                    'countryIso' => 'DE',
                    'kind' => 'company',
                    'name' => 'Max Mustermann',
                    'number' => '10001',
                    'street' => 'Musterstr. 1',
                    'zip' => '10115',
                ],
                'payConditionId' => 3,
                'payConditionData' => [
                    'id' => 3,
                    'name' => '14 Tage netto',
                    'dueDays' => 14,
                ],
                'positions' => [
                    [
                        'id' => '1',
                        'title' => 'My Article',
                        'amount' => 1,
                        'unit' => 'Stk.',
                        'vatPercent' => 19,
                        'totalNet' => 10.39,
                        'totalGross' => 12.37,
                    ],
                ],
            ],
        ]);
        $this->assertNotFalse($getBody);
        $this->mockClient->addResponse('GET', 'invoice/74', 200, $getBody);

        // document() hängt optionale Query-Parameter an, daher Wildcard.
        $this->mockClient->addResponse('GET', 'invoice/document/74*', 200, self::PDF_BODY, ['Content-Type' => 'application/pdf']);

        $this->mockClient->addResponse('GET', 'invoice/74/download', 200, self::PDF_BODY, ['Content-Type' => 'application/pdf']);

        $paymentBody = json_encode([
            ['id' => 1, 'amount' => 12.37, 'type' => 'payment', 'date' => '2020-11-21'],
        ]);
        $this->assertNotFalse($paymentBody);
        $this->mockClient->addResponse('POST', 'invoice/74/payment', 200, $paymentBody);

        $lockBody = json_encode([
            'data' => [
                'id' => 74,
                'documenthPath' => 'C:\\orgamax\\documents\\RE-2020-1001.pdf',
                'number' => 'RE-2020-1001',
                'state' => 'locked',
            ],
        ]);
        $this->assertNotFalse($lockBody);
        $this->mockClient->addResponse('PUT', 'invoice/74/lock', 200, $lockBody);

        $this->mockClient->addResponse('POST', 'invoice/74/send', 200, 'Ok', ['Content-Type' => 'text/plain']);

        $listBody = json_encode([
            'meta' => [
                'count' => 2,
                'totalCount' => 17,
                'filter' => ['all' => [], 'draft' => [], 'overdue' => [], 'paid' => [], 'locked' => [], 'dunned' => []],
            ],
            'data' => [
                [
                    'id' => 74,
                    'date' => '2020-11-01',
                    'type' => 'invoice',
                    'customerId' => 5,
                    'number' => 'RE-2020-1001',
                    'dueToDate' => '2020-11-15',
                    'totalNet' => 10.39,
                    'totalGross' => 12.37,
                    'outstandingAmount' => 12.37,
                    'cashDiscountTotal' => 0,
                    'state' => 'locked',
                ],
                [
                    'id' => 75,
                    'date' => '2020-11-05',
                    'type' => 'depositInvoice',
                    'customerId' => 8,
                    'number' => 'RE-2020-1002',
                    'dueToDate' => '2020-11-19',
                    'totalNet' => 100.0,
                    'totalGross' => 119.0,
                    'outstandingAmount' => 0,
                    'cashDiscountTotal' => 0,
                    'state' => 'paid',
                    'metaData' => ['nextDunning' => [], 'currentDunning' => [], 'cancellation' => []],
                ],
            ],
        ]);
        $this->assertNotFalse($listBody);
        // Query-Parameter werden an die URI angehängt, daher Wildcard —
        // die spezifischeren GET-Patterns sind zuvor registriert.
        $this->mockClient->addResponse('GET', 'invoice*', 200, $listBody);
    }

    public function test_get_invoice(): void {
        $invoice = $this->endpoint->get(new ID(74));

        $this->assertInstanceOf(Invoice::class, $invoice);
        $this->assertEquals(74, $invoice->getId());
        $this->assertEquals('RE-2020-1001', $invoice->getNumber());
        $this->assertEquals('invoice', $invoice->getType()?->value);
        $this->assertEquals('net', $invoice->getPriceKind()?->value);
        $this->assertEquals('locked', $invoice->getState()?->value);
        $this->assertEquals(12.37, $invoice->getTotalGross());
        $this->assertEquals(0.0, $invoice->getCashDiscountTotal());
        $this->assertEquals('2020-11-15', $invoice->getDueToDate());
        $this->assertEquals('ACME GmbH', $invoice->getCustomerData()?->getCompanyName());
        $this->assertEquals(3, $invoice->getPayConditionId());
        $this->assertEquals('14 Tage netto', $invoice->getPayConditionData()?->getName());
        $this->assertCount(1, $invoice->getPositions() ?? []);
        $this->assertEquals('My Article', $invoice->getPositions()?->getValues()[0]->getTitle());
        $this->assertRequestMade('GET', 'invoice/74');
    }

    public function test_get_invoice_without_id_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->get();
    }

    public function test_search_invoices(): void {
        $result = $this->endpoint->search(['limit' => 2, 'filter' => 'locked']);

        $this->assertInstanceOf(InvoiceList::class, $result);
        $this->assertEquals(2, $result->getMeta()?->getCount());
        $this->assertEquals(17, $result->getMeta()?->getTotalCount());
        $this->assertCount(2, $result->getValues());
        $this->assertEquals('depositInvoice', $result->getValues()[1]->getType()?->value);
        $this->assertEquals('paid', $result->getValues()[1]->getState()?->value);
        $this->assertEquals(8, $result->getValues()[1]->getCustomerId());
        $this->assertNotNull($result->getValues()[1]->getMetaData());
        $this->assertRequestMade('GET', 'invoice*');
    }

    public function test_get_invoice_document(): void {
        $document = $this->endpoint->document(new ID(74));

        $this->assertSame(self::PDF_BODY, $document);
        $this->assertStringStartsWith('%PDF', $document);
        $this->assertRequestMade('GET', 'invoice/document/74');
    }

    public function test_download_invoice_deprecated(): void {
        $document = $this->endpoint->download(new ID(74));

        $this->assertSame(self::PDF_BODY, $document);
        $this->assertRequestMade('GET', 'invoice/74/download');
    }

    public function test_add_payment(): void {
        $payment = new InvoicePayment([
            'amount' => 12.37,
            'type' => 'payment',
            'date' => '2020-11-21',
        ]);

        $result = $this->endpoint->addPayment(new ID(74), $payment);

        $this->assertInstanceOf(InvoicePayments::class, $result);
        $this->assertCount(1, $result);
        $this->assertEquals(12.37, $result->getValues()[0]->getAmount());
        $this->assertEquals(PaymentType::PAYMENT, $result->getValues()[0]->getType());
        $this->assertEquals('2020-11-21', $result->getValues()[0]->getDate());
        $this->assertRequestMade('POST', 'invoice/74/payment');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame(12.37, $payload['amount']);
        $this->assertSame('payment', $payload['type']);
        $this->assertSame('2020-11-21', $payload['date']);
    }

    public function test_add_invalid_payment_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->addPayment(new ID(74), new InvoicePayment(['amount' => 12.37]));
        $this->assertNoRequestsMade();
    }

    public function test_lock_invoice(): void {
        $result = $this->endpoint->lock(new ID(74));

        $this->assertInstanceOf(InvoiceLockInfo::class, $result);
        $this->assertEquals(74, $result->getId());
        $this->assertEquals('RE-2020-1001', $result->getNumber());
        $this->assertEquals('locked', $result->getState());
        $this->assertEquals('C:\\orgamax\\documents\\RE-2020-1001.pdf', $result->getDocumenthPath());
        // getDocumentPath() fällt auf das Spec-Tippfehler-Feld zurück.
        $this->assertEquals('C:\\orgamax\\documents\\RE-2020-1001.pdf', $result->getDocumentPath());
        $this->assertRequestMade('PUT', 'invoice/74/lock');
    }

    public function test_send_invoice(): void {
        $result = $this->endpoint->send(new ID(74), ['email1@mydomain.com', 'email2@mydomain.com'], 'Your invoice', 'Rechnung');

        $this->assertTrue($result);
        $this->assertRequestMade('POST', 'invoice/74/send');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame(['email1@mydomain.com', 'email2@mydomain.com'], $payload['recipients']);
        $this->assertSame('Your invoice', $payload['subject']);
        $this->assertSame('Rechnung', $payload['attachmentName']);
    }

    public function test_send_invoice_without_attachment_name(): void {
        $result = $this->endpoint->send(new ID(74), ['email1@mydomain.com'], 'Your invoice');

        $this->assertTrue($result);

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertArrayNotHasKey('attachmentName', $payload);
    }
}

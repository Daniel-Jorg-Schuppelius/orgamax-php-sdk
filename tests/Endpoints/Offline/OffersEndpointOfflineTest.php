<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : OffersEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\API\Endpoints\OffersEndpoint;
use Orgamax\Entities\Offers\{Offer, OfferList};
use Orgamax\Enums\{PriceKind, SalesDocumentState};
use Tests\Contracts\OfflineEndpointTest;

class OffersEndpointOfflineTest extends OfflineEndpointTest {
    private OffersEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new OffersEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        // Wildcard wegen optionaler Query-Parameter (type, filename).
        $this->mockClient->addResponse(
            'GET',
            'offer/document/15*',
            200,
            '%PDF-1.4 mock-offer-document',
            ['Content-Type' => 'application/pdf']
        );

        $getBody = json_encode([
            'meta' => [],
            'data' => [
                'id' => 15,
                'number' => 'AN-2021-15',
                'date' => '2021-04-23',
                'customerId' => 7,
                'priceKind' => 'net',
                'state' => 'open',
                'totalNet' => 100,
                'totalGross' => 119,
                'outstandingAmount' => 119,
                'cashDiscountTotal' => 0,
                'deliveryConditionId' => '1',
                'letterNumerationId' => '5',
                'letterPaperSettingId' => '2',
                'sentAt' => '2021-04-24T09:30:00+02:00',
                'notes' => 'Bitte zeitnah bestätigen',
                'isLocked' => false,
                'smallBusiness' => false,
                'invoiceId' => '0',
                'payConditionId' => 1,
                'payConditionData' => ['id' => 1, 'name' => '14 Tage netto', 'dueDays' => 14],
                'positions' => [
                    [
                        'id' => '1',
                        'title' => 'My Article',
                        'amount' => 2,
                        'unit' => 'Stk.',
                        'priceNet' => 50,
                        'priceGross' => 59.5,
                        'vatPercent' => 19,
                        'metaData' => ['id' => 39998, 'type' => 'article', 'number' => '309', 'calculationBase' => 'net'],
                    ],
                ],
                'customerData' => [
                    'name' => 'Max Mustermann',
                    'number' => '10001',
                    'kind' => 'person',
                    'street' => 'Musterstraße 1',
                    'zip' => '10115',
                    'city' => 'Berlin',
                    'country' => 'Deutschland',
                    'countryIso' => 'DE',
                ],
            ],
        ]);
        $this->assertNotFalse($getBody);
        $this->mockClient->addResponse('GET', 'offer/15', 200, $getBody);

        $listBody = json_encode([
            'meta' => [
                'count' => 2,
                'filter' => ['all' => [], 'open' => [], 'accept' => []],
            ],
            'data' => [
                ['id' => 15, 'date' => '2021-04-23', 'customerId' => 7, 'number' => 'AN-2021-15', 'totalNet' => 100, 'totalGross' => 119, 'state' => 'open', 'invoiceId' => '0'],
                ['id' => 16, 'date' => '2021-04-24', 'customerId' => 8, 'number' => 'AN-2021-16', 'totalNet' => 250, 'totalGross' => 297.5, 'state' => 'accept', 'invoiceId' => '90'],
            ],
        ]);
        $this->assertNotFalse($listBody);
        // Query-Parameter werden an die URI angehängt, daher Wildcard — nach
        // den spezifischeren Patterns registrieren.
        $this->mockClient->addResponse('GET', 'offer*', 200, $listBody);
    }

    public function test_get_offer(): void {
        $offer = $this->endpoint->get(new ID(15));

        $this->assertInstanceOf(Offer::class, $offer);
        $this->assertEquals(15, $offer->getId());
        $this->assertEquals('AN-2021-15', $offer->getNumber());
        $this->assertEquals('2021-04-23', $offer->getDate());
        $this->assertEquals(7, $offer->getCustomerId());
        $this->assertEquals(PriceKind::NET, $offer->getPriceKind());
        $this->assertEquals(SalesDocumentState::OPEN, $offer->getState());
        $this->assertEquals(119.0, $offer->getTotalGross());
        $this->assertEquals('2021-04-24', $offer->getSentAt()?->format('Y-m-d'));
        $this->assertFalse($offer->isLocked());
        $this->assertEquals('14 Tage netto', $offer->getPayConditionData()?->getName());
        $this->assertCount(1, $offer->getPositions() ?? []);
        $this->assertEquals('My Article', $offer->getPositions()?->getValues()[0]->getTitle());
        $this->assertEquals('Max Mustermann', $offer->getCustomerData()?->getName());
        $this->assertRequestMade('GET', 'offer/15');
    }

    public function test_get_offer_without_id_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->get();
    }

    public function test_search_offers(): void {
        $result = $this->endpoint->search(['limit' => 2, 'orderBy' => 'date']);

        $this->assertInstanceOf(OfferList::class, $result);
        $this->assertEquals(2, $result->getMeta()?->getCount());
        $this->assertCount(2, $result->getValues());
        $this->assertEquals('AN-2021-16', $result->getValues()[1]->getNumber());
        $this->assertEquals(SalesDocumentState::ACCEPT, $result->getValues()[1]->getState());
        $this->assertEquals('90', $result->getValues()[1]->getInvoiceId());
        $this->assertRequestMade('GET', 'offer?*');
    }

    public function test_get_offer_document(): void {
        $document = $this->endpoint->document(new ID(15), 'original');

        $this->assertSame('%PDF-1.4 mock-offer-document', $document);
        $this->assertRequestMade('GET', 'offer/document/15*');
    }
}

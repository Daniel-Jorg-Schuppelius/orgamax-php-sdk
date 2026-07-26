<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : SuppliersEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\API\Endpoints\SuppliersEndpoint;
use Orgamax\Entities\Suppliers\{Supplier, SupplierList, SupplierResponse};
use Tests\Contracts\OfflineEndpointTest;

class SuppliersEndpointOfflineTest extends OfflineEndpointTest {
    private SuppliersEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new SuppliersEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        // POST /supplier (OHNE trailing slash) liefert den vollständigen Lieferanten.
        $createBody = json_encode([
            'meta' => [],
            'data' => [
                'id' => 30,
                'name' => 'PK IT Solutions',
                'number' => 70008,
                'email' => 'pkit@solutions.de',
                'mobile' => '07544 4529',
                'phone1' => '069 59799500',
                'website' => 'www.pkitsolutions.de',
                'notes' => 'best IT solutions',
                'notesAlert' => true,
                'addresses' => [
                    ['city' => 'Detmold', 'companyName' => 'PK IT Solutions', 'kind' => 'company', 'countryIso' => 'DE', 'street' => 'Furstenbergstrasse 1', 'zipCode' => '88677', 'isDefault' => true],
                ],
            ],
        ]);
        $this->assertNotFalse($createBody);
        $this->mockClient->addResponse('POST', 'supplier', 201, $createBody);

        // GET /supplier/{id} liefert laut Spec nur {data: SupplierData} ohne meta.
        $getBody = json_encode([
            'data' => [
                'id' => 30,
                'name' => 'PK IT Solutions',
                'number' => 70008,
                'discount' => 3.5,
                'email' => 'pkit@solutions.de',
                'phone1' => '069 59799500',
                'notesAlert' => false,
                'customerReference' => 'K-1002',
                'addresses' => [
                    ['city' => 'Detmold', 'companyName' => 'PK IT Solutions', 'kind' => 'company', 'countryIso' => 'DE', 'street' => 'Furstenbergstrasse 1', 'zipCode' => '88677', 'isDefault' => true],
                ],
                'contactPersons' => [
                    ['firstName' => 'Isa', 'lastName' => 'Bachmann', 'isMainContact' => true],
                ],
                'aliases' => ['PKIT'],
                'tags' => ['IT'],
                'defaultBookkeepingAccountId' => '5900',
                'accountNo' => 70008,
                'payKindId' => 2,
                'cashDiscountSetting' => ['dueDays' => 30, 'discount' => 2.0, 'discountDays' => 8],
                'discountEnabled' => true,
            ],
        ]);
        $this->assertNotFalse($getBody);
        $this->mockClient->addResponse('GET', 'supplier/30', 200, $getBody);

        // Query-Parameter werden vom Toolkit an die URI angehängt, daher Wildcard.
        // GET supplier/30 ist zuvor registriert und matcht weiterhin zuerst.
        $listBody = json_encode([
            'meta' => ['count' => 2, 'totalCount' => 8],
            'data' => [
                [
                    'id' => 30,
                    'name' => 'PK IT Solutions',
                    'number' => 70008,
                    'countryIso' => 'DE',
                    'email' => 'pkit@solutions.de',
                    'customerReference' => 'K-1002',
                    'mainContact' => ['firstName' => 'Isa', 'lastName' => 'Bachmann'],
                    'address' => ['city' => 'Detmold', 'street' => 'Furstenbergstrasse 1', 'zipCode' => '88677', 'isoCountry' => 'DE'],
                ],
                [
                    'id' => 31,
                    'name' => 'Beier Gebr.',
                    'number' => 70009,
                    'countryIso' => 'DE',
                    'address' => ['city' => 'Berlin', 'street' => 'Bahnhofstr. 22', 'zipCode' => '01234', 'isoCountry' => 'DE'],
                ],
            ],
        ]);
        $this->assertNotFalse($listBody);
        $this->mockClient->addResponse('GET', 'supplier*', 200, $listBody);

        // PUT liefert laut Spec ebenfalls den vollständigen SupplierData-Datensatz.
        $updateBody = json_encode([
            'meta' => ['count' => 1, 'totalCount' => 1],
            'data' => [
                'id' => 30,
                'name' => 'PK IT Solutions',
                'number' => 70008,
                'addresses' => [
                    ['companyName' => 'PK IT Solutions', 'kind' => 'company', 'isDefault' => true],
                ],
            ],
        ]);
        $this->assertNotFalse($updateBody);
        $this->mockClient->addResponse('PUT', 'supplier/30', 200, $updateBody);

        $this->mockClient->addResponse('DELETE', 'supplier/30', 204, '');
    }

    public function test_create_supplier(): void {
        $supplier = new Supplier([
            'email' => 'pkit@solutions.de',
            'mobile' => '07544 4529',
            'phone1' => '069 59799500',
            'website' => 'www.pkitsolutions.de',
            'notes' => 'best IT solutions',
            'notesAlert' => true,
            'addresses' => [
                ['city' => 'Detmold', 'companyName' => 'PK IT Solutions', 'kind' => 'company', 'countryIso' => 'DE', 'street' => 'Furstenbergstrasse 1', 'zipCode' => '88677', 'isDefault' => true],
            ],
        ]);

        $result = $this->endpoint->create($supplier);

        $this->assertInstanceOf(SupplierResponse::class, $result);
        $this->assertEquals(30, $result->getData()?->getId());
        $this->assertEquals('PK IT Solutions', $result->getData()?->getName());
        $this->assertEquals(70008, $result->getData()?->getNumber());
        $this->assertEquals('Detmold', $result->getData()?->getAddresses()?->getValues()[0]->getCity());
        $this->assertRequestMade('POST', 'supplier');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame('pkit@solutions.de', $payload['email']);
        $this->assertTrue($payload['notesAlert']);
        $this->assertSame('PK IT Solutions', $payload['addresses'][0]['companyName'] ?? null);
        $this->assertArrayNotHasKey('id', $payload);
    }

    public function test_get_supplier(): void {
        $supplier = $this->endpoint->get(new ID(30));

        $this->assertInstanceOf(Supplier::class, $supplier);
        $this->assertEquals(30, $supplier->getId());
        $this->assertEquals('PK IT Solutions', $supplier->getName());
        $this->assertEquals(3.5, $supplier->getDiscount());
        $this->assertEquals('K-1002', $supplier->getCustomerReference());
        $this->assertEquals('Detmold', $supplier->getAddresses()?->getValues()[0]->getCity());
        $this->assertEquals('Bachmann', $supplier->getContactPersons()[0]['lastName'] ?? null);
        $this->assertEquals(2, $supplier->getPayKindId());
        $this->assertEquals(30, $supplier->getCashDiscountSetting()?->getDueDays());
        $this->assertEquals(2.0, $supplier->getCashDiscountSetting()?->getDiscount());
        $this->assertEquals(8, $supplier->getCashDiscountSetting()?->getDiscountDays());
        $this->assertTrue($supplier->getDiscountEnabled());
        $this->assertRequestMade('GET', 'supplier/30');
    }

    public function test_get_supplier_without_id_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->get();
    }

    public function test_search_suppliers(): void {
        $result = $this->endpoint->search(['limit' => 2, 'search' => 'PK']);

        $this->assertInstanceOf(SupplierList::class, $result);
        $this->assertEquals(2, $result->getMeta()?->getCount());
        $this->assertEquals(8, $result->getMeta()?->getTotalCount());
        $this->assertCount(2, $result->getValues());
        $this->assertEquals('PK IT Solutions', $result->getValues()[0]->getName());
        $this->assertEquals('DE', $result->getValues()[0]->getCountryIso());
        $this->assertEquals('Detmold', $result->getValues()[0]->getAddress()?->getCity());
        $this->assertEquals('DE', $result->getValues()[0]->getAddress()?->getIsoCountry());
        $this->assertEquals('Beier Gebr.', $result->getValues()[1]->getName());
        $this->assertRequestMade('GET', 'supplier*');
    }

    public function test_update_supplier(): void {
        $supplier = new Supplier([
            'addresses' => [
                ['companyName' => 'PK IT Solutions', 'kind' => 'company', 'isDefault' => true],
            ],
        ]);

        $result = $this->endpoint->update(new ID(30), $supplier);

        $this->assertInstanceOf(SupplierResponse::class, $result);
        $this->assertEquals(30, $result->getData()?->getId());
        $this->assertEquals('PK IT Solutions', $result->getData()?->getName());
        $this->assertRequestMade('PUT', 'supplier/30');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame('PK IT Solutions', $payload['addresses'][0]['companyName'] ?? null);
    }

    public function test_delete_supplier(): void {
        $this->assertTrue($this->endpoint->delete(new ID(30)));
        $this->assertRequestMade('DELETE', 'supplier/30');
    }
}

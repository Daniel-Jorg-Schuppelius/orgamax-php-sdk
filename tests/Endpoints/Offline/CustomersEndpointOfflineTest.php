<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : CustomersEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use APIToolkit\Entities\ID;
use InvalidArgumentException;
use Orgamax\API\Endpoints\CustomersEndpoint;
use Orgamax\Entities\Common\ResourceResponse;
use Orgamax\Entities\Customers\{Customer, CustomerList, CustomerResponse};
use Orgamax\Enums\CustomerKind;
use Tests\Contracts\OfflineEndpointTest;

class CustomersEndpointOfflineTest extends OfflineEndpointTest {
    private CustomersEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new CustomersEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        $createBody = json_encode([
            'meta' => [],
            'data' => [
                'id' => '6507',
                'number' => 10001,
                'kind' => 'person',
                'lastName' => 'Müller',
                'name' => 'Müller',
                'netPriceAsDefault' => false,
                'customerDefaultAddress' => [
                    'billingAddress' => ['kind' => 'person', 'lastName' => 'Müller'],
                ],
            ],
        ]);
        $this->assertNotFalse($createBody);
        $this->mockClient->addResponse('POST', 'customer/', 201, $createBody);

        $getBody = json_encode([
            'meta' => [],
            'data' => [
                'id' => 6507,
                'name' => 'Beier Gebr.',
                'number' => '20100',
                'kind' => 'company',
                'discount' => 10.5,
                'email' => 'info@beier.example',
                'contactPersons' => [
                    ['firstName' => 'Ralf', 'lastName' => 'Schwarz', 'isMainContact' => true],
                ],
                'addresses' => [
                    ['city' => 'Berlin', 'street' => 'Bahnhofstr. 22', 'zipCode' => '01234', 'kind' => 'person'],
                ],
            ],
        ]);
        $this->assertNotFalse($getBody);
        $this->mockClient->addResponse('GET', 'customer/6507', 200, $getBody);

        $listBody = json_encode([
            'meta' => ['count' => 1, 'totalCount' => 12],
            'data' => [
                [
                    'id' => 6507,
                    'name' => 'Beier Gebr.',
                    'kind' => 'company',
                    'address' => ['city' => 'Berlin', 'street' => 'Bahnhofstr. 22', 'zipCode' => '01234', 'isoCountry' => 'DE'],
                ],
            ],
        ]);
        $this->assertNotFalse($listBody);
        $this->mockClient->addResponse('GET', 'customer*', 200, $listBody);

        $updateBody = json_encode([
            'meta' => [],
            'data' => ['id' => '6507'],
        ]);
        $this->assertNotFalse($updateBody);
        $this->mockClient->addResponse('PUT', 'customer/6507', 200, $updateBody);
    }

    public function test_create_customer(): void {
        $customer = new Customer([
            'customerDefaultAddress' => [
                'billingAddress' => ['kind' => 'person', 'lastName' => 'Müller'],
            ],
        ]);

        $result = $this->endpoint->create($customer);

        $this->assertInstanceOf(CustomerResponse::class, $result);
        $this->assertEquals('6507', $result->getData()?->getId());
        $this->assertEquals(CustomerKind::PERSON, $result->getData()?->getKind());
        $this->assertEquals('Müller', $result->getData()?->getCustomerDefaultAddress()?->getBillingAddress()?->getLastName());
        $this->assertRequestMade('POST', 'customer/');
    }

    public function test_get_customer(): void {
        $customer = $this->endpoint->get(new ID(6507));

        $this->assertInstanceOf(Customer::class, $customer);
        $this->assertEquals('6507', $customer->getId());
        $this->assertEquals('Beier Gebr.', $customer->getName());
        $this->assertEquals(CustomerKind::COMPANY, $customer->getKind());
        $this->assertEquals(10.5, $customer->getDiscount());
        $this->assertEquals('Schwarz', $customer->getContactPersons()?->getValues()[0]->getLastName());
        $this->assertEquals('Berlin', $customer->getAddresses()?->getValues()[0]->getCity());
        $this->assertRequestMade('GET', 'customer/6507');
    }

    public function test_get_customer_without_id_throws_exception(): void {
        $this->expectException(InvalidArgumentException::class);
        $this->endpoint->get();
    }

    public function test_search_customers(): void {
        $result = $this->endpoint->search(['search' => 'Beier']);

        $this->assertInstanceOf(CustomerList::class, $result);
        $this->assertEquals(1, $result->getMeta()?->getCount());
        $this->assertEquals(12, $result->getMeta()?->getTotalCount());
        $this->assertEquals('Beier Gebr.', $result->getValues()[0]->getName());
        $this->assertEquals('DE', $result->getValues()[0]->getAddress()?->getIsoCountry());
        $this->assertRequestMade('GET', 'customer*');
    }

    public function test_update_customer(): void {
        $customer = new Customer([
            'customerDefaultAddress' => [
                'billingAddress' => ['kind' => 'company', 'companyName' => 'Müller GMBH'],
            ],
        ]);

        $result = $this->endpoint->update(new ID(6507), $customer);

        $this->assertInstanceOf(ResourceResponse::class, $result);
        $this->assertEquals('6507', $result->getData()?->getId());
        $this->assertRequestMade('PUT', 'customer/6507');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame('Müller GMBH', $payload['customerDefaultAddress']['billingAddress']['companyName'] ?? null);
    }
}

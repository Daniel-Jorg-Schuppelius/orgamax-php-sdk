<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : AccountSettingEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use Orgamax\API\Endpoints\Settings\AccountSettingEndpoint;
use Orgamax\Entities\Settings\AccountSetting;
use Orgamax\Enums\SalesTaxFrequency;
use Tests\Contracts\OfflineEndpointTest;

class AccountSettingEndpointOfflineTest extends OfflineEndpointTest {
    private AccountSettingEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new AccountSettingEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        // GET /setting/account liefert ein Array mit einem AccountSetting.
        $body = json_encode([
            [
                'accountEmail' => 'info@example.org',
                'bankAccountBic' => 'GENODEM1GLS',
                'bankAccountIban' => 'DE02120300000000202051',
                'businessField' => 'Professionelle Dienstleister',
                'businessFieldExtension' => 'Nein',
                'businessSize' => '1-5',
                'companyAddress' => [
                    'city' => 'Meppen',
                    'companyName' => 'Muster GmbH',
                    'country' => 'Deutschland',
                    'countryIso' => 'DE',
                    'firstName' => 'Max',
                    'lastName' => 'Mustermann',
                    'street' => 'Musterstraße 1',
                    'zipCode' => '49716',
                ],
                'companyType' => 'GmbH',
                'isSmallBusiness' => false,
                'isSubjectToImputedTaxation' => true,
                'paypalUserName' => 'paypal@example.org',
                'permanentExtensionOfPaymentDeadline' => false,
                'phone' => '+49 5931 12345',
                'salesTaxFrequency' => 'quarterly',
                'senderEmail' => 'rechnung@example.org',
                'senderEmailName' => 'Muster GmbH',
                'taxNumber' => '61/815/08150',
            ],
        ]);
        $this->assertNotFalse($body);
        $this->mockClient->addResponse('GET', 'setting/account', 200, $body);
    }

    public function test_get_account_setting(): void {
        $setting = $this->endpoint->get();

        $this->assertInstanceOf(AccountSetting::class, $setting);
        $this->assertEquals('info@example.org', $setting->getAccountEmail());
        $this->assertEquals('DE02120300000000202051', $setting->getBankAccountIban());
        $this->assertEquals('GmbH', $setting->getCompanyType());
        $this->assertEquals(SalesTaxFrequency::QUARTERLY, $setting->getSalesTaxFrequency());
        $this->assertEquals('Meppen', $setting->getCompanyAddress()?->getCity());
        $this->assertEquals('Muster GmbH', $setting->getCompanyAddress()?->getCompanyName());
        $this->assertFalse($setting->isSmallBusiness());
        $this->assertTrue($setting->isSubjectToImputedTaxation());
        $this->assertFalse($setting->getPermanentExtensionOfPaymentDeadline());
        $this->assertEquals('61/815/08150', $setting->getTaxNumber());
        $this->assertRequestMade('GET', 'setting/account');
    }

    public function test_get_account_setting_empty_response_returns_null(): void {
        $this->mockClient->clearResponses();
        $this->mockClient->addResponse('GET', 'setting/account', 200, '[]');

        $this->assertNull($this->endpoint->get());
    }
}

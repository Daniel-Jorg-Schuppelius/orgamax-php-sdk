<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : ArticleSettingsEndpointOfflineTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Endpoints\Offline;

use APIToolkit\Exceptions\NotAllowedException;
use Orgamax\API\Endpoints\Settings\ArticleSettingsEndpoint;
use Orgamax\Entities\Settings\ArticleSetting;
use Tests\Contracts\OfflineEndpointTest;

class ArticleSettingsEndpointOfflineTest extends OfflineEndpointTest {
    private ArticleSettingsEndpoint $endpoint;

    protected function setUp(): void {
        parent::setUp();
        $this->endpoint = new ArticleSettingsEndpoint($this->mockClient, $this->logger);
    }

    protected function setupMockResponses(): void {
        // POST /setting/article antwortet mit einem Array von ArticleSetting.
        $body = json_encode([
            [
                'units' => ['Stk.', 'Liter', 'm2', 'm3'],
                'categories' => ['Zubehör', 'My categorie'],
            ],
        ]);
        $this->assertNotFalse($body);
        $this->mockClient->addResponse('POST', 'setting/article', 200, $body);
    }

    public function test_create_article_setting(): void {
        $setting = new ArticleSetting([
            'units' => ['Stk.', 'Liter', 'm2', 'm3'],
            'categories' => ['Zubehör', 'My categorie'],
        ]);

        $result = $this->endpoint->create($setting);

        $this->assertInstanceOf(ArticleSetting::class, $result);
        $this->assertEquals(['Stk.', 'Liter', 'm2', 'm3'], $result->getUnits());
        $this->assertEquals(['Zubehör', 'My categorie'], $result->getCategories());
        $this->assertRequestMade('POST', 'setting/article');

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame(['Stk.', 'Liter', 'm2', 'm3'], $payload['units']);
        $this->assertSame(['Zubehör', 'My categorie'], $payload['categories']);
    }

    public function test_create_article_setting_only_units(): void {
        $setting = new ArticleSetting;
        $setting->setUnits(['Stk.', 'Liter', 'm2', 'm3']);

        $this->endpoint->create($setting);

        $payload = $this->getLastRequestJson();
        $this->assertNotNull($payload);
        $this->assertSame(['Stk.', 'Liter', 'm2', 'm3'], $payload['units']);
        $this->assertArrayNotHasKey('categories', $payload);
    }

    public function test_get_throws_not_allowed_exception(): void {
        $this->expectException(NotAllowedException::class);
        $this->endpoint->get();
        $this->assertNoRequestsMade();
    }
}

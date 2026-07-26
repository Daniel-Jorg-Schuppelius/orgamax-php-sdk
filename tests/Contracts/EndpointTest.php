<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : EndpointTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Contracts;

use Orgamax\API\Client;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Tests\TestAPIClientFactory;
use Throwable;

/**
 * Basisklasse für Live-Tests gegen die echte orgaMAX-API. Übersprungen, wenn
 * ORGAMAX_SKIP_API_TESTS gesetzt ist (Default in phpunit.xml.dist) oder keine
 * Zugangsdaten in .samples/config.json hinterlegt sind.
 */
abstract class EndpointTest extends TestCase {
    protected Client $client;

    protected ?LoggerInterface $logger = null;

    protected function setUp(): void {
        parent::setUp();

        if (getenv('ORGAMAX_SKIP_API_TESTS') === '1') {
            $this->markTestSkipped('Live API tests are disabled (ORGAMAX_SKIP_API_TESTS=1)');
        }

        $this->logger = TestAPIClientFactory::getLogger();

        try {
            $client = TestAPIClientFactory::getClient();
        } catch (Throwable $e) {
            $this->markTestSkipped('Live API client could not be created: ' . $e->getMessage());
        }

        if ($client === null) {
            $this->markTestSkipped('No credentials configured in .samples/config.json');
        }

        $this->client = $client;
    }
}

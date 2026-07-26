<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : TestAPIClientFactory.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests;

use ConfigToolkit\ConfigLoader;
use ERRORToolkit\Factories\ConsoleLoggerFactory;
use ERRORToolkit\LoggerRegistry;
use Orgamax\API\Client;
use Psr\Log\LoggerInterface;

/**
 * Baut den Client für Live-Tests aus .samples/config.json (siehe
 * config.json.sample). Ist ein Token hinterlegt, wird es direkt verwendet,
 * andernfalls wird der komplette Auth-Flow mit API-Key/Secret/ownershipId
 * durchlaufen.
 */
class TestAPIClientFactory {
    private static ?Client $client = null;

    private static ?LoggerInterface $logger = null;

    public static function getLogger(): LoggerInterface {
        if (self::$logger === null) {
            self::$logger = ConsoleLoggerFactory::getLogger();
            LoggerRegistry::setLogger(self::$logger);
        }

        return self::$logger;
    }

    public static function getClient(): ?Client {
        if (self::$client === null) {
            $configFile = __DIR__ . '/../.samples/config.json';
            if (!file_exists($configFile)) {
                return null;
            }

            $logger = self::getLogger();
            $config = ConfigLoader::getInstance($logger);
            $config->loadConfigFile($configFile);

            $baseUrl = $config->get('ORGAMAX-API', 'resourceurl', Client::DEFAULT_BASE_URL);
            $token = $config->get('ORGAMAX-API', 'token', '');

            if (is_string($token) && $token !== '') {
                self::$client = new Client($token, is_string($baseUrl) ? $baseUrl : Client::DEFAULT_BASE_URL, $logger, true);

                return self::$client;
            }

            $apiKey = $config->get('ORGAMAX-API', 'api_key', '');
            $apiSecret = $config->get('ORGAMAX-API', 'api_secret', '');
            $ownershipId = $config->get('ORGAMAX-API', 'ownership_id', '');

            if (!is_string($apiKey) || !is_string($apiSecret) || !is_string($ownershipId)
                || $apiKey === '' || $apiKey === 'your-api-key' || $apiSecret === '' || $ownershipId === '') {
                return null;
            }

            self::$client = Client::fromCredentials(
                $apiKey,
                $apiSecret,
                $ownershipId,
                is_string($baseUrl) ? $baseUrl : Client::DEFAULT_BASE_URL,
                $logger,
                true
            );
        }

        return self::$client;
    }

    public static function reset(): void {
        self::$client = null;
    }
}

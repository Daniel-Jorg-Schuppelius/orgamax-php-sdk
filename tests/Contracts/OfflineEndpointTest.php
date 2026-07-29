<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : OfflineEndpointTest.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Contracts;

use APIToolkit\Testing\MockApiClient;
use ERRORToolkit\Factories\ConsoleLoggerFactory;
use ERRORToolkit\LoggerRegistry;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

abstract class OfflineEndpointTest extends TestCase {
    protected ?LoggerInterface $logger = null;

    protected MockApiClient $mockClient;

    protected function setUp(): void {
        parent::setUp();
        $this->logger = ConsoleLoggerFactory::getLogger();
        LoggerRegistry::setLogger($this->logger);
        $this->mockClient = new MockApiClient;
        $this->mockClient->clearRequestLog();
        $this->mockClient->clearResponses();
        $this->setupMockResponses();
    }

    /**
     * Override this method to set up mock responses for your endpoint tests
     */
    protected function setupMockResponses(): void {
        // Override in subclasses
    }

    /**
     * Assert that a request was made with the given method and URI
     */
    protected function assertRequestMade(string $method, string $uriPattern): void {
        $found = false;
        foreach ($this->mockClient->getRequestLog() as $request) {
            if ($request['method'] === $method && $this->matchUri($request['uri'], $uriPattern)) {
                $found = true;
                break;
            }
        }
        $this->assertTrue($found, "Expected {$method} request to {$uriPattern} was not made");
    }

    /**
     * Assert that no requests were made
     */
    protected function assertNoRequestsMade(): void {
        $this->assertEmpty($this->mockClient->getRequestLog(), 'Expected no requests to be made');
    }

    /**
     * Get the body from the last request. Die orgaMAX-Endpoints senden
     * JSON-Bodies über die Guzzle-json-Option; ein roher body wird bevorzugt.
     */
    protected function getLastRequestBody(): ?string {
        $lastRequest = $this->mockClient->getLastRequest();
        if ($lastRequest === null) {
            return null;
        }
        if (isset($lastRequest['options']['body']) && is_string($lastRequest['options']['body'])) {
            return $lastRequest['options']['body'];
        }
        if (isset($lastRequest['options']['json'])) {
            $encoded = json_encode($lastRequest['options']['json']);

            return $encoded === false ? null : $encoded;
        }

        return null;
    }

    /**
     * Get the decoded JSON payload from the last request
     *
     * @return array<string, mixed>|null
     */
    protected function getLastRequestJson(): ?array {
        $lastRequest = $this->mockClient->getLastRequest();
        if ($lastRequest === null) {
            return null;
        }
        if (isset($lastRequest['options']['json']) && is_array($lastRequest['options']['json'])) {
            return $lastRequest['options']['json'];
        }
        if (isset($lastRequest['options']['body']) && is_string($lastRequest['options']['body'])) {
            $decoded = json_decode($lastRequest['options']['body'], true);

            return is_array($decoded) ? $decoded : null;
        }

        return null;
    }

    private function matchUri(string $uri, string $pattern): bool {
        if ($uri === $pattern) {
            return true;
        }
        $regex = str_replace(['/', '*'], ['\/', '.*'], $pattern);

        return (bool) preg_match('/^' . $regex . '$/', $uri);
    }
}

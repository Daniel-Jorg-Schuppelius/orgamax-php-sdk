<?php
/*
 * Created on   : Sun Jul 26 2026
 * Author       : Daniel Jörg Schuppelius
 * Author Uri   : https://schuppelius.org
 * Filename     : MockApiClient.php
 * License      : MIT License
 * License Uri  : https://opensource.org/license/mit
 */

declare(strict_types=1);

namespace Tests\Mocks;

use APIToolkit\Contracts\Interfaces\API\ApiClientInterface;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class MockApiClient implements ApiClientInterface {
    /** @var array<string, array{statusCode: int, body: string, headers: array<string, mixed>}> */
    private array $responses = [];

    /** @var array<array{method: string, uri: string, options: array<string, mixed>}> */
    private array $requestLog = [];

    private int $defaultStatusCode = 200;
    private string $defaultBody = '{}';

    /**
     * Register a mock response for a specific method and URI pattern
     * @param string $method HTTP method (GET, POST, PUT, DELETE, PATCH) or '*' for any method
     * @param string $uriPattern URI pattern with optional wildcards (*)
     * @param int $statusCode HTTP status code
     * @param string $body Response body
     * @param array<string, mixed> $headers Response headers
     */
    public function addResponse(string $method, string $uriPattern, int $statusCode, string $body, array $headers = []): self {
        $key = strtoupper($method) . ':' . $uriPattern;
        $this->responses[$key] = [
            'statusCode' => $statusCode,
            'body' => $body,
            'headers' => array_merge(['Content-Type' => 'application/json'], $headers),
        ];
        return $this;
    }

    /**
     * Set default response for unmatched URIs
     */
    public function setDefaultResponse(int $statusCode, string $body): self {
        $this->defaultStatusCode = $statusCode;
        $this->defaultBody = $body;
        return $this;
    }

    /**
     * Get all logged requests
     * @return array<array{method: string, uri: string, options: array<string, mixed>}>
     */
    public function getRequestLog(): array {
        return $this->requestLog;
    }

    /**
     * Get the last logged request
     * @return array{method: string, uri: string, options: array<string, mixed>}|null
     */
    public function getLastRequest(): ?array {
        return $this->requestLog[count($this->requestLog) - 1] ?? null;
    }

    /**
     * Clear request log
     */
    public function clearRequestLog(): void {
        $this->requestLog = [];
    }

    /**
     * Clear all registered responses
     */
    public function clearResponses(): void {
        $this->responses = [];
    }

    public function get(string $uri, array $options = []): ResponseInterface {
        return $this->request('GET', $uri, $options);
    }

    public function post(string $uri, array $options = []): ResponseInterface {
        return $this->request('POST', $uri, $options);
    }

    public function put(string $uri, array $options = []): ResponseInterface {
        return $this->request('PUT', $uri, $options);
    }

    public function patch(string $uri, array $options = []): ResponseInterface {
        return $this->request('PATCH', $uri, $options);
    }

    public function delete(string $uri, array $options = []): ResponseInterface {
        return $this->request('DELETE', $uri, $options);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function request(string $method, string $uri, array $options): ResponseInterface {
        $this->requestLog[] = [
            'method' => $method,
            'uri' => $uri,
            'options' => $options,
        ];

        $method = strtoupper($method);

        // First try to match with specific method
        foreach ($this->responses as $pattern => $response) {
            [$patternMethod, $patternUri] = explode(':', $pattern, 2);
            if ($patternMethod === $method && $this->matchUri($uri, $patternUri)) {
                return new Response(
                    $response['statusCode'],
                    $response['headers'],
                    $response['body']
                );
            }
        }

        // Then try to match with wildcard method
        foreach ($this->responses as $pattern => $response) {
            [$patternMethod, $patternUri] = explode(':', $pattern, 2);
            if ($patternMethod === '*' && $this->matchUri($uri, $patternUri)) {
                return new Response(
                    $response['statusCode'],
                    $response['headers'],
                    $response['body']
                );
            }
        }

        return new Response(
            $this->defaultStatusCode,
            ['Content-Type' => 'application/json'],
            $this->defaultBody
        );
    }

    private function matchUri(string $uri, string $pattern): bool {
        // Exact match
        if ($uri === $pattern) {
            return true;
        }

        // Pattern with wildcards (*)
        $regex = str_replace(['/', '*'], ['\/', '.*'], $pattern);
        return (bool) preg_match('/^' . $regex . '$/', $uri);
    }
}

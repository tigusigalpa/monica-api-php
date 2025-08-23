<?php

declare(strict_types = 1);

/**
 * HTTP Client for Monica API
 *
 * This file contains the HttpClient class responsible for making HTTP requests
 * to the Monica API Platform endpoints.
 *
 * @package   Tigusigalpa\MonicaApi\Http
 * @author    Igor Sazonov <sovletig@gmail.com>
 * @copyright 2025 Igor Sazonov
 * @license   MIT License
 * @link      https://github.com/tigusigalpa/monica-api-php
 */

namespace Tigusigalpa\MonicaApi\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;
use Tigusigalpa\MonicaApi\Exceptions\MonicaApiException;

/**
 * HTTP Client for Monica API
 *
 * Handles HTTP communication with Monica API Platform endpoints.
 * Provides methods for making authenticated requests and handling responses.
 *
 * @package Tigusigalpa\MonicaApi\Http
 * @author  Igor Sazonov <sovletig@gmail.com>
 * @since   1.0.0
 */
class HttpClient
{
    /**
     * Default request timeout in seconds
     */
    private const DEFAULT_TIMEOUT = 0;

    /**
     * Default connection timeout in seconds
     */
    private const DEFAULT_CONNECT_TIMEOUT = 10;

    /**
     * Guzzle HTTP client instance
     */
    private Client $client;

    /**
     * Monica API base URL
     */
    private string $baseUrl;

    /**
     * Monica API key for authentication
     */
    private string $apiKey;

    /**
     * Constructor
     *
     * @param  string  $baseUrl  Monica API base URL
     * @param  string  $apiKey  Monica API key for authentication
     * @param  int  $timeout  Request timeout in seconds (default: 30)
     */
    public function __construct(string $baseUrl, string $apiKey, int $timeout = self::DEFAULT_TIMEOUT)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
        $this->apiKey = $apiKey;

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => $timeout,
            'connect_timeout' => self::DEFAULT_CONNECT_TIMEOUT,
            'headers' => [
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
                'User-Agent' => 'monica-api-php/1.0.0',
            ],
        ]);
    }

    /**
     * Make a POST request to the API
     *
     * @param  string  $endpoint  API endpoint (e.g., '/v1/chat/completions')
     * @param  array<string, mixed>  $data  Request payload data
     *
     * @return array<string, mixed> Decoded JSON response
     *
     * @throws MonicaApiException If the request fails or returns an error
     */
    public function post(string $endpoint, array $data): array
    {
        try {
            $response = $this->client->post($endpoint, [
                'json' => $data,
            ]);

            return $this->handleResponse($response);
        } catch (RequestException $e) {
            $this->handleRequestException($e);
        } catch (GuzzleException $e) {
            throw new MonicaApiException(
                'HTTP request failed: '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Handle HTTP response and decode JSON
     *
     * @param  ResponseInterface  $response  HTTP response object
     *
     * @return array<string, mixed> Decoded JSON response
     *
     * @throws MonicaApiException If response cannot be decoded or contains errors
     */
    private function handleResponse(ResponseInterface $response): array
    {
        $body = $response->getBody()->getContents();
        $data = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new MonicaApiException(
                'Invalid JSON response: '.json_last_error_msg(),
                $response->getStatusCode()
            );
        }

        // Check for API errors in response
        if (isset($data['error'])) {
            $errorMessage = $data['error']['message'] ?? 'Unknown API error';
            $errorCode = $data['error']['code'] ?? 'unknown_error';

            throw new MonicaApiException(
                "Monica API error [{$errorCode}]: {$errorMessage}",
                $response->getStatusCode()
            );
        }

        return $data;
    }

    /**
     * Handle Guzzle request exceptions
     *
     * @param  RequestException  $exception  Request exception to handle
     *
     * @throws MonicaApiException Always throws with appropriate error message
     */
    private function handleRequestException(RequestException $exception): void
    {
        $response = $exception->getResponse();

        if ($response === null) {
            throw new MonicaApiException(
                'Network error: '.$exception->getMessage(),
                0,
                $exception
            );
        }

        $statusCode = $response->getStatusCode();
        $body = $response->getBody()->getContents();

        // Try to decode error response
        $errorData = json_decode($body, true);

        if ($errorData && isset($errorData['error'])) {
            $errorMessage = $errorData['error']['message'] ?? 'Unknown API error';
            $errorCode = $errorData['error']['code'] ?? 'unknown_error';

            throw new MonicaApiException(
                "Monica API error [{$errorCode}]: {$errorMessage}",
                $statusCode,
                $exception
            );
        }

        // Fallback error message based on status code
        $errorMessage = match ($statusCode) {
            401 => 'Unauthorized: Invalid API key',
            403 => 'Forbidden: Access denied',
            404 => 'Not Found: Endpoint does not exist',
            429 => 'Rate limit exceeded',
            500 => 'Internal server error',
            502 => 'Bad gateway',
            503 => 'Service unavailable',
            504 => 'Gateway timeout',
            default => "HTTP error {$statusCode}: {$exception->getMessage()}",
        };

        throw new MonicaApiException($errorMessage, $statusCode, $exception);
    }

    /**
     * Make a GET request to the API
     *
     * @param  string  $endpoint  API endpoint
     * @param  array<string, mixed>  $query  Query parameters
     *
     * @return array<string, mixed> Decoded JSON response
     *
     * @throws MonicaApiException If the request fails or returns an error
     */
    public function get(string $endpoint, array $query = []): array
    {
        try {
            $response = $this->client->get($endpoint, [
                'query' => $query,
            ]);

            return $this->handleResponse($response);
        } catch (RequestException $e) {
            $this->handleRequestException($e);
        } catch (GuzzleException $e) {
            throw new MonicaApiException(
                'HTTP request failed: '.$e->getMessage(),
                $e->getCode(),
                $e
            );
        }
    }

    /**
     * Get the current API key
     *
     * @return string Current API key
     */
    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    /**
     * Get the base URL
     *
     * @return string Current base URL
     */
    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }
}

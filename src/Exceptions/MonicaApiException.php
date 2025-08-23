<?php

declare(strict_types=1);

/**
 * Monica API Exception
 *
 * This file contains the MonicaApiException class for handling
 * Monica API related errors and exceptions.
 *
 * @package   Tigusigalpa\MonicaApi\Exceptions
 * @author    Igor Sazonov <sovletig@gmail.com>
 * @copyright 2025 Igor Sazonov
 * @license   MIT License
 * @link      https://github.com/tigusigalpa/monica-api-php
 */

namespace Tigusigalpa\MonicaApi\Exceptions;

use Exception;
use Throwable;

/**
 * Monica API Exception
 *
 * Exception thrown when Monica API requests fail or return errors.
 * This includes HTTP errors, API errors, network issues, and other
 * communication problems with the Monica API Platform.
 *
 * @package Tigusigalpa\MonicaApi\Exceptions
 * @author  Igor Sazonov <sovletig@gmail.com>
 * @since   1.0.0
 */
class MonicaApiException extends Exception
{
    /**
     * HTTP status code associated with the error (if applicable)
     */
    private ?int $httpStatusCode;

    /**
     * API error code returned by Monica API (if applicable)
     */
    private ?string $apiErrorCode;

    /**
     * Raw response data from the API (if available)
     *
     * @var array<string, mixed>|null
     */
    private ?array $responseData;

    /**
     * Constructor
     *
     * @param string                    $message         Exception message
     * @param int                       $code            Exception code (default: 0)
     * @param Throwable|null            $previous        Previous exception for chaining
     * @param int|null                  $httpStatusCode  HTTP status code (if applicable)
     * @param string|null               $apiErrorCode    API error code (if applicable)
     * @param array<string, mixed>|null $responseData    Raw response data (if available)
     */
    public function __construct(
        string $message = '',
        int $code = 0,
        ?Throwable $previous = null,
        ?int $httpStatusCode = null,
        ?string $apiErrorCode = null,
        ?array $responseData = null
    ) {
        parent::__construct($message, $code, $previous);
        
        $this->httpStatusCode = $httpStatusCode;
        $this->apiErrorCode = $apiErrorCode;
        $this->responseData = $responseData;
    }

    /**
     * Get the HTTP status code associated with this exception
     *
     * @return int|null HTTP status code or null if not applicable
     */
    public function getHttpStatusCode(): ?int
    {
        return $this->httpStatusCode;
    }

    /**
     * Get the API error code returned by Monica API
     *
     * @return string|null API error code or null if not available
     */
    public function getApiErrorCode(): ?string
    {
        return $this->apiErrorCode;
    }

    /**
     * Get the raw response data from the API
     *
     * @return array<string, mixed>|null Raw response data or null if not available
     */
    public function getResponseData(): ?array
    {
        return $this->responseData;
    }

    /**
     * Check if this exception is related to authentication issues
     *
     * @return bool True if this is an authentication error
     */
    public function isAuthenticationError(): bool
    {
        return $this->httpStatusCode === 401 || 
               $this->apiErrorCode === 'invalid_api_key' ||
               $this->apiErrorCode === 'authentication_failed';
    }

    /**
     * Check if this exception is related to rate limiting
     *
     * @return bool True if this is a rate limit error
     */
    public function isRateLimitError(): bool
    {
        return $this->httpStatusCode === 429 || 
               $this->apiErrorCode === 'rate_limit_exceeded';
    }

    /**
     * Check if this exception is related to quota/billing issues
     *
     * @return bool True if this is a quota/billing error
     */
    public function isQuotaError(): bool
    {
        return $this->apiErrorCode === 'insufficient_quota' ||
               $this->apiErrorCode === 'quota_exceeded' ||
               $this->apiErrorCode === 'billing_hard_limit_reached';
    }

    /**
     * Check if this exception is related to server errors (5xx)
     *
     * @return bool True if this is a server error
     */
    public function isServerError(): bool
    {
        return $this->httpStatusCode !== null && 
               $this->httpStatusCode >= 500 && 
               $this->httpStatusCode < 600;
    }

    /**
     * Check if this exception is related to client errors (4xx)
     *
     * @return bool True if this is a client error
     */
    public function isClientError(): bool
    {
        return $this->httpStatusCode !== null && 
               $this->httpStatusCode >= 400 && 
               $this->httpStatusCode < 500;
    }

    /**
     * Get a user-friendly error message based on the error type
     *
     * @return string User-friendly error message
     */
    public function getUserFriendlyMessage(): string
    {
        if ($this->isAuthenticationError()) {
            return 'Authentication failed. Please check your API key.';
        }

        if ($this->isRateLimitError()) {
            return 'Rate limit exceeded. Please wait before making more requests.';
        }

        if ($this->isQuotaError()) {
            return 'API quota exceeded. Please check your billing and usage limits.';
        }

        if ($this->isServerError()) {
            return 'Monica API server error. Please try again later.';
        }

        if ($this->isClientError()) {
            return 'Invalid request. Please check your request parameters.';
        }

        return $this->getMessage() ?: 'An unknown error occurred while communicating with Monica API.';
    }

    /**
     * Convert exception to array for logging or debugging
     *
     * @return array<string, mixed> Exception data as array
     */
    public function toArray(): array
    {
        return [
            'message' => $this->getMessage(),
            'code' => $this->getCode(),
            'http_status_code' => $this->httpStatusCode,
            'api_error_code' => $this->apiErrorCode,
            'response_data' => $this->responseData,
            'file' => $this->getFile(),
            'line' => $this->getLine(),
            'trace' => $this->getTraceAsString(),
        ];
    }
}

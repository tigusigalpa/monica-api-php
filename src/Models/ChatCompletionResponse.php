<?php

declare(strict_types=1);

/**
 * Chat Completion Response Model
 *
 * This file contains the ChatCompletionResponse class representing a response
 * from Monica API Platform chat completion endpoint.
 *
 * @package   Tigusigalpa\MonicaApi\Models
 * @author    Igor Sazonov <sovletig@gmail.com>
 * @copyright 2025 Igor Sazonov
 * @license   MIT License
 * @link      https://github.com/tigusigalpa/monica-api-php
 */

namespace Tigusigalpa\MonicaApi\Models;

use Tigusigalpa\MonicaApi\Models\ChatMessage;

/**
 * Chat Completion Response Model
 *
 * Represents a response from Monica API Platform chat completion endpoint.
 * Contains the AI-generated response content and metadata.
 *
 * @package Tigusigalpa\MonicaApi\Models
 * @author  Igor Sazonov <sovletig@gmail.com>
 * @since   1.0.0
 */
class ChatCompletionResponse
{
    /**
     * Response ID from the API
     */
    private string $id;

    /**
     * Object type (usually 'chat.completion')
     */
    private string $object;

    /**
     * Unix timestamp of when the response was created
     */
    private int $created;

    /**
     * Model used for the completion
     */
    private string $model;

    /**
     * Array of choice objects containing the AI responses
     *
     * @var array<string, mixed>[]
     */
    private array $choices;

    /**
     * Usage statistics for the request
     *
     * @var array<string, mixed>
     */
    private array $usage;

    /**
     * Constructor
     *
     * @param string                     $id      Response ID
     * @param string                     $object  Object type
     * @param int                        $created Creation timestamp
     * @param string                     $model   Model used
     * @param array<string, mixed>[]     $choices Array of choices
     * @param array<string, mixed>       $usage   Usage statistics
     */
    public function __construct(
        string $id,
        string $object,
        int $created,
        string $model,
        array $choices,
        array $usage
    ) {
        $this->id = $id;
        $this->object = $object;
        $this->created = $created;
        $this->model = $model;
        $this->choices = $choices;
        $this->usage = $usage;
    }

    /**
     * Create ChatCompletionResponse from API response array
     *
     * @param array<string, mixed> $data API response data
     *
     * @return self New ChatCompletionResponse instance
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['id'] ?? '',
            $data['object'] ?? 'chat.completion',
            $data['created'] ?? time(),
            $data['model'] ?? '',
            $data['choices'] ?? [],
            $data['usage'] ?? []
        );
    }

    /**
     * Get response ID
     *
     * @return string Response ID
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * Get object type
     *
     * @return string Object type
     */
    public function getObject(): string
    {
        return $this->object;
    }

    /**
     * Get creation timestamp
     *
     * @return int Unix timestamp
     */
    public function getCreated(): int
    {
        return $this->created;
    }

    /**
     * Get model used for completion
     *
     * @return string Model identifier
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Get all choices
     *
     * @return array<string, mixed>[] Array of choice objects
     */
    public function getChoices(): array
    {
        return $this->choices;
    }

    /**
     * Get the first choice (most common use case)
     *
     * @return array<string, mixed>|null First choice or null if no choices
     */
    public function getFirstChoice(): ?array
    {
        return $this->choices[0] ?? null;
    }

    /**
     * Get the content of the first choice
     *
     * @return string Content of the first choice or empty string if not available
     */
    public function getContent(): string
    {
        $firstChoice = $this->getFirstChoice();
        
        if ($firstChoice === null) {
            return '';
        }

        return $firstChoice['message']['content'] ?? '';
    }

    /**
     * Get the role of the first choice
     *
     * @return string Role of the first choice (usually 'assistant')
     */
    public function getRole(): string
    {
        $firstChoice = $this->getFirstChoice();
        
        if ($firstChoice === null) {
            return '';
        }

        return $firstChoice['message']['role'] ?? 'assistant';
    }

    /**
     * Get the finish reason of the first choice
     *
     * @return string|null Finish reason ('stop', 'length', 'content_filter', etc.)
     */
    public function getFinishReason(): ?string
    {
        $firstChoice = $this->getFirstChoice();
        
        if ($firstChoice === null) {
            return null;
        }

        return $firstChoice['finish_reason'] ?? null;
    }

    /**
     * Get usage statistics
     *
     * @return array<string, mixed> Usage statistics
     */
    public function getUsage(): array
    {
        return $this->usage;
    }

    /**
     * Get prompt tokens used
     *
     * @return int Number of tokens in the prompt
     */
    public function getPromptTokens(): int
    {
        return $this->usage['prompt_tokens'] ?? 0;
    }

    /**
     * Get completion tokens used
     *
     * @return int Number of tokens in the completion
     */
    public function getCompletionTokens(): int
    {
        return $this->usage['completion_tokens'] ?? 0;
    }

    /**
     * Get total tokens used
     *
     * @return int Total number of tokens used
     */
    public function getTotalTokens(): int
    {
        return $this->usage['total_tokens'] ?? 0;
    }

    /**
     * Check if the response was truncated due to length
     *
     * @return bool True if the response was truncated
     */
    public function wasTruncated(): bool
    {
        return $this->getFinishReason() === 'length';
    }

    /**
     * Check if the response was filtered due to content policy
     *
     * @return bool True if the response was filtered
     */
    public function wasFiltered(): bool
    {
        return $this->getFinishReason() === 'content_filter';
    }

    /**
     * Check if the response completed normally
     *
     * @return bool True if the response completed normally
     */
    public function isComplete(): bool
    {
        return $this->getFinishReason() === 'stop';
    }

    /**
     * Get the first choice as a ChatMessage object
     *
     * @return ChatMessage|null ChatMessage object or null if no choices
     */
    public function getFirstChoiceAsMessage(): ?ChatMessage
    {
        $firstChoice = $this->getFirstChoice();
        
        if ($firstChoice === null || !isset($firstChoice['message'])) {
            return null;
        }

        $message = $firstChoice['message'];
        
        return new ChatMessage(
            $message['role'] ?? 'assistant',
            $message['content'] ?? '',
            $message['name'] ?? null
        );
    }

    /**
     * Convert response to array
     *
     * @return array<string, mixed> Response data as array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'object' => $this->object,
            'created' => $this->created,
            'model' => $this->model,
            'choices' => $this->choices,
            'usage' => $this->usage,
        ];
    }

    /**
     * Get string representation of the response (returns content)
     *
     * @return string Response content
     */
    public function __toString(): string
    {
        return $this->getContent();
    }
}

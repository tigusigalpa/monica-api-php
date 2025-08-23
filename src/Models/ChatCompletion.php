<?php

declare(strict_types=1);

/**
 * Chat Completion Model
 *
 * This file contains the ChatCompletion class representing a chat completion
 * request to Monica API Platform.
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
 * Chat Completion Model
 *
 * Represents a chat completion request with all necessary parameters
 * for sending to Monica API Platform.
 *
 * @package Tigusigalpa\MonicaApi\Models
 * @author  Igor Sazonov <sovletig@gmail.com>
 * @since   1.0.0
 */
class ChatCompletion
{
    /**
     * AI model to use for the completion
     */
    private string $model;

    /**
     * Array of chat messages
     *
     * @var ChatMessage[]
     */
    private array $messages;

    /**
     * Maximum number of tokens to generate
     */
    private ?int $maxTokens;

    /**
     * Sampling temperature (0.0 to 2.0)
     */
    private ?float $temperature;

    /**
     * Nucleus sampling parameter (0.0 to 1.0)
     */
    private ?float $topP;

    /**
     * Frequency penalty (-2.0 to 2.0)
     */
    private ?float $frequencyPenalty;

    /**
     * Presence penalty (-2.0 to 2.0)
     */
    private ?float $presencePenalty;

    /**
     * Whether to stream the response
     */
    private bool $stream;

    /**
     * Constructor
     *
     * @param string               $model   AI model to use
     * @param string               $message User message (will be converted to ChatMessage)
     * @param array<string, mixed> $options Additional options
     */
    public function __construct(string $model, string $message = '', array $options = [])
    {
        $this->model = $model;
        $this->messages = [];
        $this->maxTokens = null;
        $this->temperature = null;
        $this->topP = null;
        $this->frequencyPenalty = null;
        $this->presencePenalty = null;
        $this->stream = false;

        // Add system message if provided
        if (isset($options['system']) && !empty($options['system'])) {
            $this->addMessage(ChatMessage::system($options['system']));
        }

        // Add user message if provided
        if (!empty($message)) {
            $this->addMessage(ChatMessage::user($message));
        }

        // Set options
        $this->setOptions($options);
    }

    /**
     * Set options from array
     *
     * @param array<string, mixed> $options Options array
     */
    private function setOptions(array $options): void
    {
        if (isset($options['max_tokens'])) {
            $this->setMaxTokens((int) $options['max_tokens']);
        }

        if (isset($options['temperature'])) {
            $this->setTemperature((float) $options['temperature']);
        }

        if (isset($options['top_p'])) {
            $this->setTopP((float) $options['top_p']);
        }

        if (isset($options['frequency_penalty'])) {
            $this->setFrequencyPenalty((float) $options['frequency_penalty']);
        }

        if (isset($options['presence_penalty'])) {
            $this->setPresencePenalty((float) $options['presence_penalty']);
        }

        if (isset($options['stream'])) {
            $this->setStream((bool) $options['stream']);
        }
    }

    /**
     * Get the AI model
     *
     * @return string AI model identifier
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Set the AI model
     *
     * @param string $model AI model identifier
     */
    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    /**
     * Get all messages
     *
     * @return ChatMessage[] Array of chat messages
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * Set messages array
     *
     * @param ChatMessage[] $messages Array of chat messages
     */
    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }

    /**
     * Add a message to the conversation
     *
     * @param ChatMessage $message Message to add
     */
    public function addMessage(ChatMessage $message): void
    {
        $this->messages[] = $message;
    }

    /**
     * Add a user message
     *
     * @param string      $content Message content
     * @param string|null $name    Optional user name
     */
    public function addUserMessage(string $content, ?string $name = null): void
    {
        $this->addMessage(ChatMessage::user($content, $name));
    }

    /**
     * Add an assistant message
     *
     * @param string      $content Message content
     * @param string|null $name    Optional assistant name
     */
    public function addAssistantMessage(string $content, ?string $name = null): void
    {
        $this->addMessage(ChatMessage::assistant($content, $name));
    }

    /**
     * Add a system message
     *
     * @param string      $content Message content
     * @param string|null $name    Optional system name
     */
    public function addSystemMessage(string $content, ?string $name = null): void
    {
        $this->addMessage(ChatMessage::system($content, $name));
    }

    /**
     * Get maximum tokens
     *
     * @return int|null Maximum tokens or null if not set
     */
    public function getMaxTokens(): ?int
    {
        return $this->maxTokens;
    }

    /**
     * Set maximum tokens
     *
     * @param int|null $maxTokens Maximum tokens (null to unset)
     */
    public function setMaxTokens(?int $maxTokens): void
    {
        $this->maxTokens = $maxTokens;
    }

    /**
     * Get temperature
     *
     * @return float|null Temperature or null if not set
     */
    public function getTemperature(): ?float
    {
        return $this->temperature;
    }

    /**
     * Set temperature
     *
     * @param float|null $temperature Temperature between 0.0 and 2.0 (null to unset)
     */
    public function setTemperature(?float $temperature): void
    {
        $this->temperature = $temperature;
    }

    /**
     * Get top P
     *
     * @return float|null Top P or null if not set
     */
    public function getTopP(): ?float
    {
        return $this->topP;
    }

    /**
     * Set top P
     *
     * @param float|null $topP Top P between 0.0 and 1.0 (null to unset)
     */
    public function setTopP(?float $topP): void
    {
        $this->topP = $topP;
    }

    /**
     * Get frequency penalty
     *
     * @return float|null Frequency penalty or null if not set
     */
    public function getFrequencyPenalty(): ?float
    {
        return $this->frequencyPenalty;
    }

    /**
     * Set frequency penalty
     *
     * @param float|null $frequencyPenalty Frequency penalty between -2.0 and 2.0 (null to unset)
     */
    public function setFrequencyPenalty(?float $frequencyPenalty): void
    {
        $this->frequencyPenalty = $frequencyPenalty;
    }

    /**
     * Get presence penalty
     *
     * @return float|null Presence penalty or null if not set
     */
    public function getPresencePenalty(): ?float
    {
        return $this->presencePenalty;
    }

    /**
     * Set presence penalty
     *
     * @param float|null $presencePenalty Presence penalty between -2.0 and 2.0 (null to unset)
     */
    public function setPresencePenalty(?float $presencePenalty): void
    {
        $this->presencePenalty = $presencePenalty;
    }

    /**
     * Get stream setting
     *
     * @return bool True if streaming is enabled
     */
    public function isStream(): bool
    {
        return $this->stream;
    }

    /**
     * Set stream setting
     *
     * @param bool $stream Whether to stream the response
     */
    public function setStream(bool $stream): void
    {
        $this->stream = $stream;
    }

    /**
     * Convert to array format for API requests
     *
     * @return array<string, mixed> Request data as array
     */
    public function toArray(): array
    {
        $data = [
            'model' => $this->model,
            'messages' => array_map(fn(ChatMessage $message) => $message->toArray(), $this->messages),
        ];

        if ($this->maxTokens !== null) {
            $data['max_tokens'] = $this->maxTokens;
        }

        if ($this->temperature !== null) {
            $data['temperature'] = $this->temperature;
        }

        if ($this->topP !== null) {
            $data['top_p'] = $this->topP;
        }

        if ($this->frequencyPenalty !== null) {
            $data['frequency_penalty'] = $this->frequencyPenalty;
        }

        if ($this->presencePenalty !== null) {
            $data['presence_penalty'] = $this->presencePenalty;
        }

        if ($this->stream) {
            $data['stream'] = $this->stream;
        }

        return $data;
    }
}

<?php

declare(strict_types = 1);

/**
 * Chat Message Model
 *
 * This file contains the ChatMessage class representing a single message
 * in a chat conversation with Monica API Platform.
 *
 * @package   Tigusigalpa\MonicaApi\Models
 * @author    Igor Sazonov <sovletig@gmail.com>
 * @copyright 2025 Igor Sazonov
 * @license   MIT License
 * @link      https://github.com/tigusigalpa/monica-api-php
 */

namespace Tigusigalpa\MonicaApi\Models;

use InvalidArgumentException;

/**
 * Chat Message Model
 *
 * Represents a single message in a chat conversation.
 * Supports different message roles (system, user, assistant) and content types.
 *
 * @package Tigusigalpa\MonicaApi\Models
 * @author  Igor Sazonov <sovletig@gmail.com>
 * @since   1.0.0
 */
class ChatMessage
{
    /**
     * System message role - used for setting context and instructions
     */
    public const ROLE_SYSTEM = 'system';

    /**
     * User message role - messages from the human user
     */
    public const ROLE_USER = 'user';

    /**
     * Assistant message role - responses from the AI assistant
     */
    public const ROLE_ASSISTANT = 'assistant';

    /**
     * Valid message roles
     */
    private const VALID_ROLES = [
        self::ROLE_SYSTEM,
        self::ROLE_USER,
        self::ROLE_ASSISTANT,
    ];

    /**
     * Message role (system, user, or assistant)
     */
    private string $role;

    /**
     * Message content/text or multimodal content array
     */
    private string|array $content;

    /**
     * Optional message name (for identifying specific users or assistants)
     */
    private ?string $name;

    /**
     * Array of image attachments
     *
     * @var array<int, array<string, mixed>>
     */
    private array $images = [];

    /**
     * Constructor
     *
     * @param  string  $role  Message role (system, user, or assistant)
     * @param  string|array  $content  Message content/text or multimodal content array
     * @param  string|null  $name  Optional message name
     *
     * @throws InvalidArgumentException If role is invalid
     */
    public function __construct(string $role, string|array $content, ?string $name = null)
    {
        $this->setRole($role);
        $this->content = $content;
        $this->name = $name;
    }

    /**
     * Create a system message
     *
     * System messages are used to set the behavior and context for the AI assistant.
     *
     * @param  string  $content  System message content
     * @param  string|null  $name  Optional message name
     *
     * @return self New system message instance
     */
    public static function system(string $content, ?string $name = null): self
    {
        return new self(self::ROLE_SYSTEM, $content, $name);
    }

    /**
     * Create a user message
     *
     * User messages represent input from the human user.
     *
     * @param  string  $content  User message content
     * @param  string|null  $name  Optional user name
     *
     * @return self New user message instance
     */
    public static function user(string $content, ?string $name = null): self
    {
        return new self(self::ROLE_USER, $content, $name);
    }

    /**
     * Create a user message with image
     *
     * Creates a multimodal user message with text and image content.
     * Supports vision-capable models like GPT-4o.
     *
     * @param  string  $text  Text content
     * @param  string  $imageUrl  Image URL or base64 data URL
     * @param  string|null  $name  Optional user name
     *
     * @return self New user message instance with image
     */
    public static function userWithImage(string $text, string $imageUrl, ?string $name = null): self
    {
        $message = new self(self::ROLE_USER, $text, $name);
        $message->addImage($imageUrl);
        return $message;
    }

    /**
     * Add an image to the message
     *
     * @param  string  $imageUrl  Image URL or base64 data URL
     * @param  string  $detail  Image detail level ("low", "high", "auto")
     *
     * @return self
     */
    public function addImage(string $imageUrl, string $detail = 'auto'): self
    {
        $this->images[] = [
            'type' => 'image_url',
            'image_url' => [
                'url' => $imageUrl,
                'detail' => $detail,
            ],
        ];

        $this->buildMultimodalContent();
        return $this;
    }

    /**
     * Build multimodal content array from text and images
     */
    private function buildMultimodalContent(): void
    {
        if (empty($this->images)) {
            // No images, keep content as string if it was originally string
            if (is_array($this->content) && count($this->content) === 1 &&
                isset($this->content[0]['type']) && $this->content[0]['type'] === 'text') {
                $this->content = $this->content[0]['text'] ?? '';
            }
            return;
        }

        $textContent = is_string($this->content) ? $this->content : $this->getTextContent();

        $multimodalContent = [];

        // Add text content first
        if (!empty($textContent)) {
            $multimodalContent[] = [
                'type' => 'text',
                'text' => $textContent,
            ];
        }

        // Add all images
        foreach ($this->images as $image) {
            $multimodalContent[] = $image;
        }

        $this->content = $multimodalContent;
    }

    /**
     * Get the text content only
     *
     * @return string Text content (extracts from multimodal if needed)
     */
    public function getTextContent(): string
    {
        if (is_string($this->content)) {
            return $this->content;
        }

        // Extract text from multimodal content
        foreach ($this->content as $item) {
            if (isset($item['type']) && $item['type'] === 'text') {
                return $item['text'] ?? '';
            }
        }

        return '';
    }

    /**
     * Create a user message with multiple images
     *
     * Creates a multimodal user message with text and multiple images.
     *
     * @param  string  $text  Text content
     * @param  array<string>  $imageUrls  Array of image URLs or base64 data URLs
     * @param  string|null  $name  Optional user name
     *
     * @return self New user message instance with images
     */
    public static function userWithImages(string $text, array $imageUrls, ?string $name = null): self
    {
        $message = new self(self::ROLE_USER, $text, $name);
        foreach ($imageUrls as $imageUrl) {
            $message->addImage($imageUrl);
        }
        return $message;
    }

    /**
     * Create an assistant message
     *
     * Assistant messages represent responses from the AI assistant.
     *
     * @param  string  $content  Assistant message content
     * @param  string|null  $name  Optional assistant name
     *
     * @return self New assistant message instance
     */
    public static function assistant(string $content, ?string $name = null): self
    {
        return new self(self::ROLE_ASSISTANT, $content, $name);
    }

    /**
     * Create ChatMessage instance from array data
     *
     * @param  array<string, mixed>  $data  Message data array
     *
     * @return self New ChatMessage instance
     *
     * @throws InvalidArgumentException If required fields are missing or invalid
     */
    public static function fromArray(array $data): self
    {
        if (!isset($data['role'])) {
            throw new InvalidArgumentException('Message role is required');
        }

        if (!isset($data['content'])) {
            throw new InvalidArgumentException('Message content is required');
        }

        $message = new self(
            $data['role'],
            $data['content'],
            $data['name'] ?? null
        );

        // Handle multimodal content with images
        if (is_array($data['content'])) {
            foreach ($data['content'] as $item) {
                if (isset($item['type']) && $item['type'] === 'image_url') {
                    $message->images[] = $item;
                }
            }
        }

        return $message;
    }

    /**
     * Get all valid message roles
     *
     * @return string[] Array of valid message roles
     */
    public static function getValidRoles(): array
    {
        return self::VALID_ROLES;
    }

    /**
     * Get the message role
     *
     * @return string Message role (system, user, or assistant)
     */
    public function getRole(): string
    {
        return $this->role;
    }

    /**
     * Set the message role
     *
     * @param  string  $role  Message role (system, user, or assistant)
     *
     * @throws InvalidArgumentException If role is invalid
     */
    public function setRole(string $role): void
    {
        if (!in_array($role, self::VALID_ROLES, true)) {
            throw new InvalidArgumentException(
                "Invalid role '{$role}'. Valid roles are: ".implode(', ', self::VALID_ROLES)
            );
        }

        $this->role = $role;
    }

    /**
     * Get the message content
     *
     * @return string|array Message content/text or multimodal content array
     */
    public function getContent(): string|array
    {
        return $this->content;
    }

    /**
     * Set the message content
     *
     * @param  string|array  $content  Message content/text or multimodal content array
     */
    public function setContent(string|array $content): void
    {
        $this->content = $content;
    }

    /**
     * Get the message name
     *
     * @return string|null Message name or null if not set
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set the message name
     *
     * @param  string|null  $name  Message name or null to unset
     */
    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    /**
     * Check if this is a system message
     *
     * @return bool True if this is a system message
     */
    public function isSystem(): bool
    {
        return $this->role === self::ROLE_SYSTEM;
    }

    /**
     * Check if this is a user message
     *
     * @return bool True if this is a user message
     */
    public function isUser(): bool
    {
        return $this->role === self::ROLE_USER;
    }

    /**
     * Check if this is an assistant message
     *
     * @return bool True if this is an assistant message
     */
    public function isAssistant(): bool
    {
        return $this->role === self::ROLE_ASSISTANT;
    }

    /**
     * Add image from file path
     *
     * @param  string  $filePath  Path to image file
     * @param  string  $detail  Image detail level ("low", "high", "auto")
     *
     * @return self
     *
     * @throws InvalidArgumentException If file doesn't exist or is not readable
     */
    public function addImageFromFile(string $filePath, string $detail = 'auto'): self
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            throw new InvalidArgumentException("Image file '{$filePath}' does not exist or is not readable");
        }

        $imageData = file_get_contents($filePath);
        if ($imageData === false) {
            throw new InvalidArgumentException("Failed to read image file '{$filePath}'");
        }

        $mimeType = mime_content_type($filePath) ?: 'image/jpeg';
        $base64Data = base64_encode($imageData);
        $dataUrl = "data:{$mimeType};base64,{$base64Data}";

        return $this->addImage($dataUrl, $detail);
    }

    /**
     * Add image from base64 string
     *
     * @param  string  $base64Data  Base64 encoded image data
     * @param  string  $mimeType  MIME type of the image (e.g., 'image/jpeg', 'image/png')
     * @param  string  $detail  Image detail level ("low", "high", "auto")
     *
     * @return self
     */
    public function addImageFromBase64(string $base64Data, string $mimeType = 'image/jpeg', string $detail = 'auto'): self
    {
        $dataUrl = "data:{$mimeType};base64,{$base64Data}";
        return $this->addImage($dataUrl, $detail);
    }

    /**
     * Get all images attached to this message
     *
     * @return array<int, array<string, mixed>>
     */
    public function getImages(): array
    {
        return $this->images;
    }

    /**
     * Check if message has images
     *
     * @return bool
     */
    public function hasImages(): bool
    {
        return !empty($this->images);
    }

    /**
     * Remove all images from the message
     *
     * @return self
     */
    public function clearImages(): self
    {
        $this->images = [];
        $this->buildMultimodalContent();
        return $this;
    }

    /**
     * Convert message to array format for API requests
     *
     * @return array<string, mixed> Message data as array
     */
    public function toArray(): array
    {
        $data = [
            'role' => $this->role,
            'content' => $this->content,
        ];

        if ($this->name !== null) {
            $data['name'] = $this->name;
        }

        return $data;
    }

    /**
     * Get string representation of the message
     *
     * @return string String representation
     */
    public function __toString(): string
    {
        $namePrefix = $this->name ? "[{$this->name}] " : '';
        $textContent = $this->getTextContent();
        $imageCount = count($this->images);

        $contentStr = $textContent;
        if ($imageCount > 0) {
            $contentStr .= " [+{$imageCount} image".($imageCount > 1 ? 's' : '').']';
        }

        return "{$namePrefix}{$this->role}: {$contentStr}";
    }
}

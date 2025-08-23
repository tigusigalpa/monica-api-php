<?php

declare(strict_types = 1);

/**
 * Image Generation Response Model
 *
 * This file contains the ImageGenerationResponse class for handling image generation responses
 * from Monica API Platform.
 *
 * @package   Tigusigalpa\MonicaApi\Models
 * @author    Igor Sazonov <sovletig@gmail.com>
 * @copyright 2025 Igor Sazonov
 * @license   MIT License
 * @link      https://github.com/tigusigalpa/monica-api-php
 */

namespace Tigusigalpa\MonicaApi\Models;

/**
 * Image Generation Response Model
 *
 * Represents a response from the Monica API image generation endpoints.
 * Contains the generated image URLs and metadata.
 *
 * @package Tigusigalpa\MonicaApi\Models
 * @author  Igor Sazonov <sovletig@gmail.com>
 * @since   1.1.0
 */
class ImageGenerationResponse
{
    /**
     * Array of generated image data
     *
     * @var array<int, array<string, mixed>>
     */
    private array $data;

    /**
     * Raw response data from the API
     *
     * @var array<string, mixed>
     */
    private array $rawResponse;

    /**
     * Constructor
     *
     * @param array<string, mixed> $response Raw response from Monica API
     */
    public function __construct(array $response)
    {
        $this->rawResponse = $response;
        $this->data = $response['data'] ?? [];
    }

    /**
     * Get all generated image URLs
     *
     * @return array<int, string> Array of image URLs
     */
    public function getImageUrls(): array
    {
        $urls = [];
        foreach ($this->data as $item) {
            if (isset($item['url'])) {
                $urls[] = $item['url'];
            }
        }
        return $urls;
    }

    /**
     * Get the first generated image URL
     *
     * @return string|null First image URL or null if no images generated
     */
    public function getFirstImageUrl(): ?string
    {
        $urls = $this->getImageUrls();
        return $urls[0] ?? null;
    }

    /**
     * Get the number of generated images
     *
     * @return int Number of generated images
     */
    public function getImageCount(): int
    {
        return count($this->data);
    }

    /**
     * Get image data by index
     *
     * @param int $index Index of the image (0-based)
     * @return array<string, mixed>|null Image data or null if index doesn't exist
     */
    public function getImageData(int $index): ?array
    {
        return $this->data[$index] ?? null;
    }

    /**
     * Get all image data
     *
     * @return array<int, array<string, mixed>> All image data
     */
    public function getAllImageData(): array
    {
        return $this->data;
    }

    /**
     * Check if the response contains any images
     *
     * @return bool True if images were generated, false otherwise
     */
    public function hasImages(): bool
    {
        return !empty($this->data);
    }

    /**
     * Get the raw response data from the API
     *
     * @return array<string, mixed> Raw response data
     */
    public function getRawResponse(): array
    {
        return $this->rawResponse;
    }

    /**
     * Download image content from URL
     *
     * @param string $url Image URL to download
     * @return string|false Image content as binary string or false on failure
     */
    public function downloadImage(string $url): string|false
    {
        $context = stream_context_create([
            'http' => [
                'timeout' => 30,
                'user_agent' => 'Monica-API-PHP-Client/1.1.0',
            ],
        ]);

        return file_get_contents($url, false, $context);
    }

    /**
     * Download the first image
     *
     * @return string|false Image content as binary string or false on failure
     */
    public function downloadFirstImage(): string|false
    {
        $url = $this->getFirstImageUrl();
        if ($url === null) {
            return false;
        }

        return $this->downloadImage($url);
    }

    /**
     * Save image to file
     *
     * @param string $url Image URL to save
     * @param string $filepath Local file path to save the image
     * @return bool True on success, false on failure
     */
    public function saveImage(string $url, string $filepath): bool
    {
        $imageContent = $this->downloadImage($url);
        if ($imageContent === false) {
            return false;
        }

        return file_put_contents($filepath, $imageContent) !== false;
    }

    /**
     * Save the first image to file
     *
     * @param string $filepath Local file path to save the image
     * @return bool True on success, false on failure
     */
    public function saveFirstImage(string $filepath): bool
    {
        $url = $this->getFirstImageUrl();
        if ($url === null) {
            return false;
        }

        return $this->saveImage($url, $filepath);
    }

    /**
     * Save all images to directory
     *
     * @param string $directory Directory to save images to
     * @param string $prefix Filename prefix for saved images
     * @param string $extension File extension (without dot)
     * @return array<int, string> Array of saved file paths
     */
    public function saveAllImages(string $directory, string $prefix = 'image_', string $extension = 'png'): array
    {
        $savedFiles = [];
        $urls = $this->getImageUrls();

        // Ensure directory exists
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        foreach ($urls as $index => $url) {
            $filename = $prefix . ($index + 1) . '.' . $extension;
            $filepath = rtrim($directory, '/\\') . DIRECTORY_SEPARATOR . $filename;

            if ($this->saveImage($url, $filepath)) {
                $savedFiles[] = $filepath;
            }
        }

        return $savedFiles;
    }

    /**
     * Convert response to array
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'image_count' => $this->getImageCount(),
            'image_urls' => $this->getImageUrls(),
            'data' => $this->data,
        ];
    }

    /**
     * Convert response to JSON string
     *
     * @param int $flags JSON encode flags
     * @return string JSON representation
     */
    public function toJson(int $flags = 0): string
    {
        return json_encode($this->toArray(), $flags);
    }

    /**
     * String representation of the response
     *
     * @return string
     */
    public function __toString(): string
    {
        $count = $this->getImageCount();
        $urls = $this->getImageUrls();
        
        if ($count === 0) {
            return 'ImageGenerationResponse: No images generated';
        }

        if ($count === 1) {
            return "ImageGenerationResponse: 1 image generated - {$urls[0]}";
        }

        return "ImageGenerationResponse: {$count} images generated";
    }
}

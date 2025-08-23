<?php

declare(strict_types = 1);

/**
 * Invalid Model Exception
 *
 * This file contains the InvalidModelException class for handling
 * errors related to unsupported or invalid AI models.
 *
 * @package   Tigusigalpa\MonicaApi\Exceptions
 * @author    Igor Sazonov <sovletig@gmail.com>
 */

namespace Tigusigalpa\MonicaApi\Exceptions;

use InvalidArgumentException;

/**
 * Invalid Model Exception
 *
 * Exception thrown when an unsupported or invalid AI model is specified.
 * This exception is thrown during client initialization or when setting
 * a model that is not supported by the Monica API Platform.
 *
 * @package Tigusigalpa\MonicaApi\Exceptions
 * @author  Igor Sazonov <sovletig@gmail.com>
 * @since   1.0.0
 */
class InvalidModelException extends InvalidArgumentException
{
    /**
     * The invalid model identifier that caused the exception
     */
    private string $invalidModel;

    /**
     * List of supported models (if available)
     *
     * @var string[]
     */
    private array $supportedModels;

    /**
     * Constructor
     *
     * @param  string  $invalidModel  The invalid model identifier
     * @param  string[]  $supportedModels  List of supported models (optional)
     * @param  string  $message  Custom exception message (optional)
     * @param  int  $code  Exception code (default: 0)
     */
    public function __construct(
        string $invalidModel,
        array $supportedModels = [],
        string $message = '',
        int $code = 0
    ) {
        $this->invalidModel = $invalidModel;
        $this->supportedModels = $supportedModels;

        if (empty($message)) {
            $message = "Invalid model '{$invalidModel}' is not supported";

            if (!empty($supportedModels)) {
                $message .= '. Supported models: '.implode(', ', $supportedModels);
            }
        }

        parent::__construct($message, $code);
    }

    /**
     * Get the invalid model identifier that caused this exception
     *
     * @return string Invalid model identifier
     */
    public function getInvalidModel(): string
    {
        return $this->invalidModel;
    }

    /**
     * Get the list of supported models
     *
     * @return string[] Array of supported model identifiers
     */
    public function getSupportedModels(): array
    {
        return $this->supportedModels;
    }

    /**
     * Get a user-friendly error message with suggestions
     *
     * @return string User-friendly error message
     */
    public function getUserFriendlyMessage(): string
    {
        $message = "The model '{$this->invalidModel}' is not supported.";

        $suggestions = $this->getSuggestions();
        if (!empty($suggestions)) {
            $message .= ' Did you mean: '.implode(', ', $suggestions).'?';
        } elseif ($this->hasSupportedModels()) {
            $message .= ' Please use one of the supported models.';
        }

        return $message;
    }

    /**
     * Get suggestions for similar model names
     *
     * Uses simple string similarity to suggest possible alternatives
     * from the supported models list.
     *
     * @param  int  $maxSuggestions  Maximum number of suggestions to return (default: 3)
     *
     * @return string[] Array of suggested model names
     */
    public function getSuggestions(int $maxSuggestions = 3): array
    {
        if (empty($this->supportedModels)) {
            return [];
        }

        $suggestions = [];
        $invalidModel = strtolower($this->invalidModel);

        foreach ($this->supportedModels as $supportedModel) {
            $similarity = 0;
            similar_text($invalidModel, strtolower($supportedModel), $similarity);

            if ($similarity > 50) { // Only suggest if similarity is above 50%
                $suggestions[] = [
                    'model' => $supportedModel,
                    'similarity' => $similarity,
                ];
            }
        }

        // Sort by similarity (highest first)
        usort($suggestions, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        // Return only the model names, limited by maxSuggestions
        return array_slice(
            array_column($suggestions, 'model'),
            0,
            $maxSuggestions
        );
    }

    /**
     * Check if supported models list is available
     *
     * @return bool True if supported models list is available
     */
    public function hasSupportedModels(): bool
    {
        return !empty($this->supportedModels);
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
            'invalid_model' => $this->invalidModel,
            'supported_models' => $this->supportedModels,
            'suggestions' => $this->getSuggestions(),
            'file' => $this->getFile(),
            'line' => $this->getLine(),
        ];
    }
}

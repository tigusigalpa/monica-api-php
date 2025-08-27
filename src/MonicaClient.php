<?php

declare(strict_types = 1);

/**
 * Monica API PHP Client
 *
 * This file contains the main MonicaClient class for interacting with Monica API Platform.
 * Monica API Platform is an aggregator/intermediary for working with third-party AI providers,
 * allowing unified access to multiple AI models through a single API.
 *
 * @package   MonicaApi
 * @author    Igor Sazonov <sovletig@gmail.com>
 * @copyright 2025 Igor Sazonov
 * @license   MIT License
 * @link      https://github.com/tigusigalpa/monica-api-php
 * @link      https://platform.monica.im/docs/en/overview Monica API Documentation
 */

namespace Tigusigalpa\MonicaApi;

use Tigusigalpa\MonicaApi\Exceptions\InvalidModelException;
use Tigusigalpa\MonicaApi\Exceptions\MonicaApiException;
use Tigusigalpa\MonicaApi\Http\HttpClient;
use Tigusigalpa\MonicaApi\Models\ChatCompletion;
use Tigusigalpa\MonicaApi\Models\ChatCompletionResponse;
use Tigusigalpa\MonicaApi\Models\ChatMessage;
use Tigusigalpa\MonicaApi\Models\ImageGeneration;
use Tigusigalpa\MonicaApi\Models\ImageGenerationResponse;

/**
 * Monica API Client
 *
 * Main client class for interacting with Monica API Platform.
 * Provides unified access to multiple AI models from various providers
 * including OpenAI, Anthropic, Google, DeepSeek, Meta, Grok, NVIDIA, and Mistral.
 *
 * @package Tigusigalpa\MonicaApi
 * @author  Igor Sazonov <sovletig@gmail.com>
 * @since   1.0.0
 *
 * @example
 * ```php
 * $client = new MonicaClient('your-api-key', 'gpt-4.1');
 * $response = $client->chat('Hello, how are you?');
 * echo $response->getContent();
 * ```
 */
class MonicaClient
{
    /**
     * Monica API base URL
     */
    private const API_BASE_URL = 'https://openapi.monica.im';

    /**
     * Monica API default version
     */
    private const API_DEFAULT_VERSION = 'v1';

    /**
     * Monica API default model
     */
    private const API_DEFAULT_MODEL = 'gpt-4.1';

    /**
     * Supported AI models grouped by provider
     *
     * @var array<string, array<string, string>>
     */
    private const SUPPORTED_MODELS = [
        'OpenAI' => [
            'gpt-5' => 'GPT-5',
            'gpt-4.1' => 'GPT-4.1',
            'gpt-4.1-mini' => 'GPT-4.1 Mini',
            'gpt-4.1-nano' => 'GPT-4.1 Nano',
            'gpt-4o' => 'GPT-4o',
            'gpt-4o-mini' => 'GPT-4o Mini',
        ],
        'Anthropic' => [
            'claude-sonnet-4-20250514' => 'Claude Sonnet 4',
            'claude-opus-4-20250514' => 'Claude Opus 4',
            'claude-3-7-sonnet-latest' => 'Claude 3.7 Sonnet',
            'claude-3-5-sonnet-latest' => 'Claude Sonnet 3.5',
            'claude-3-5-haiku-latest' => 'Claude Haiku 3.5',
        ],
        'Google' => [
            'gemini-2.5-pro' => 'Gemini 2.5 Pro Preview',
            'gemini-2.5-flash' => 'Gemini 2.5 Flash',
        ],
        'DeepSeek' => [
            'deepseek-reasoner' => 'DeepSeek V3 Reasoner',
            'deepseek-chat' => 'DeepSeek V3 Chat',
        ],
        'Meta' => [
            'meta-llama/llama-3-8b-instruct' => 'Meta: Llama 3 8B Instruct',
            'meta-llama/llama-3.1-8b-instruct' => 'Meta: Llama 3.1 8B Instruct',
        ],
        'Grok' => [
            'x-ai/grok-3-beta' => 'Grok 3 Beta',
        ],
        'NVIDIA' => [
            'nvidia/llama-3.1-nemotron-70b-instruct' => 'NVIDIA: Llama 3.1 Nemotron 70B',
        ],
        'Mistral' => [
            'mistralai/mistral-7b-instruct' => 'Mistral: Mistral 7B Instruct',
        ],
    ];

    /**
     * HTTP client instance
     */
    private HttpClient $httpClient;

    /**
     * Monica API key
     */
    private string $apiKey;

    /**
     * Monica API version
     */
    private string $apiVersion;

    /**
     * AI model to use for requests
     */
    private string $model;

    /**
     * Default maximum tokens for responses
     */
    private ?int $defaultMaxTokens;

    /**
     * Default temperature for responses
     */
    private ?float $defaultTemperature;

    /**
     * Constructor
     *
     * @param  string  $apiKey  Monica API key
     * @param  string  $model  AI model to use (e.g., 'gpt-4.1', 'claude-3-haiku'). Defaults to 'gpt-4.1'
     * @param  string  $apiVersion  Monica API version. Defaults to 'v1'
     * @param  int|null  $defaultMaxTokens  Default maximum tokens for responses. Null means no default limit
     * @param  float|null  $defaultTemperature  Default temperature (0.0-2.0). Null means no default temperature
     */
    public function __construct(
        string $apiKey,
        string $model = self::API_DEFAULT_MODEL,
        string $apiVersion = self::API_DEFAULT_VERSION,
        ?int $defaultMaxTokens = null,
        ?float $defaultTemperature = null
    ) {
        $this->apiKey = $apiKey;
        $this->apiVersion = $apiVersion;
        $this->defaultMaxTokens = $defaultMaxTokens;
        $this->defaultTemperature = $defaultTemperature;
        $this->setModel($model);
        $this->httpClient = new HttpClient(self::API_BASE_URL, $this->apiKey);
    }

    /**
     * Get supported models for a specific provider
     *
     * @param  string  $provider  Provider name (e.g., 'OpenAI', 'Anthropic')
     *
     * @return array<string, string> Models for the specified provider
     */
    public static function getModelsByProvider(string $provider): array
    {
        return self::SUPPORTED_MODELS[$provider] ?? [];
    }

    /**
     * Get all supported model identifiers as a flat array
     *
     * @return string[] Array of all supported model identifiers
     */
    public static function getAllModelIds(): array
    {
        $modelIds = [];

        foreach (self::SUPPORTED_MODELS as $provider => $models) {
            $modelIds = array_merge($modelIds, array_keys($models));
        }

        return $modelIds;
    }

    /**
     * Get all supported image generation models
     *
     * @return array<string, string> Supported image generation models
     */
    public static function getSupportedImageModels(): array
    {
        return ImageGeneration::getSupportedModels();
    }

    /**
     * Get all supported models grouped by provider
     *
     * @return array<string, array<string, string>> Supported models grouped by provider
     */
    public static function getSupportedModels(): array
    {
        return self::SUPPORTED_MODELS;
    }

    /**
     * Get human-readable name for a model by its key
     *
     * @param  string  $modelKey  Model key to get human name for
     *
     * @return string|null Human-readable model name or null if not found
     */
    public static function getModelHumanName(string $modelKey): ?string
    {
        foreach (self::SUPPORTED_MODELS as $provider => $models) {
            if (isset($models[$modelKey])) {
                return $models[$modelKey];
            }
        }

        return null;
    }

    /**
     * Send a chat message and get AI response
     *
     * @param  string  $message  User message to send
     * @param  array<string, mixed>  $options  Additional options for the request
     *                                     - system: System message (string)
     *                                     - temperature: Response randomness 0.0-2.0 (float)
     *                                     - max_tokens: Maximum tokens in response (int)
     *                                     - top_p: Nucleus sampling parameter (float)
     *                                     - frequency_penalty: Frequency penalty -2.0 to 2.0 (float)
     *                                     - presence_penalty: Presence penalty -2.0 to 2.0 (float)
     *
     * @return ChatCompletionResponse The AI response
     *
     * @throws MonicaApiException If the API request fails
     * @throws InvalidModelException If the model is not supported
     */
    public function chat(string $message, array $options = []): ChatCompletionResponse
    {
        // Apply default values if not provided in options
        $options = $this->applyDefaultChatOptions($options);

        $chatCompletion = new ChatCompletion($this->model, $message, $options);

        $response = $this->httpClient->post('/'.$this->apiVersion.'/chat/completions', $chatCompletion->toArray());

        return ChatCompletionResponse::fromArray($response);
    }

    /**
     * Apply default chat options if not provided in the options array
     *
     * @param  array<string, mixed>  $options  Current options array
     *
     * @return array<string, mixed> Options with defaults applied
     */
    private function applyDefaultChatOptions(array $options): array
    {
        // Apply default max_tokens if not set and we have a default
        if (!isset($options['max_tokens']) && $this->defaultMaxTokens !== null) {
            $options['max_tokens'] = $this->defaultMaxTokens;
        }

        // Apply default temperature if not set and we have a default
        if (!isset($options['temperature']) && $this->defaultTemperature !== null) {
            $options['temperature'] = $this->defaultTemperature;
        }

        return $options;
    }

    /**
     * Send multiple messages with full control over message structure
     *
     * @param  ChatMessage[]  $messages  Array of chat messages
     * @param  array<string, mixed>  $options  Additional options for the request
     *
     * @return ChatCompletionResponse The AI response
     *
     * @throws MonicaApiException If the API request fails
     */
    public function chatWithMessages(array $messages, array $options = []): ChatCompletionResponse
    {
        // Apply default values if not provided in options
        $options = $this->applyDefaultChatOptions($options);

        $chatCompletion = new ChatCompletion($this->model, '', $options);
        $chatCompletion->setMessages($messages);

        $response = $this->httpClient->post('/'.$this->apiVersion.'/chat/completions', $chatCompletion->toArray());

        return ChatCompletionResponse::fromArray($response);
    }

    /**
     * Generate a single image with simple parameters
     *
     * @param  string  $model  AI model to use for image generation
     * @param  string  $prompt  Text prompt describing the desired image
     * @param  array<string, mixed>  $options  Additional options
     *
     * @return ImageGenerationResponse The generated images response
     *
     * @throws MonicaApiException If the API request fails
     * @throws InvalidModelException If the model is not supported
     */
    public function generateImageSimple(string $model, string $prompt, array $options = []): ImageGenerationResponse
    {
        $imageGeneration = new ImageGeneration($model, $prompt);

        // Apply options using fluent interface
        if (isset($options['negative_prompt'])) {
            $imageGeneration->setNegativePrompt($options['negative_prompt']);
        }
        if (isset($options['num_outputs'])) {
            $imageGeneration->setNumOutputs($options['num_outputs']);
        }
        if (isset($options['size'])) {
            $imageGeneration->setSize($options['size']);
        }
        if (isset($options['seed'])) {
            $imageGeneration->setSeed($options['seed']);
        }
        if (isset($options['steps'])) {
            $imageGeneration->setSteps($options['steps']);
        }
        if (isset($options['guidance'])) {
            $imageGeneration->setGuidance($options['guidance']);
        }
        if (isset($options['cfg_scale'])) {
            $imageGeneration->setCfgScale($options['cfg_scale']);
        }
        if (isset($options['quality'])) {
            $imageGeneration->setQuality($options['quality']);
        }
        if (isset($options['style'])) {
            $imageGeneration->setStyle($options['style']);
        }
        if (isset($options['aspect_ratio'])) {
            $imageGeneration->setAspectRatio($options['aspect_ratio']);
        }
        if (isset($options['magic_prompt_option'])) {
            $imageGeneration->setMagicPromptOption($options['magic_prompt_option']);
        }
        if (isset($options['style_type'])) {
            $imageGeneration->setStyleType($options['style_type']);
        }
        if (isset($options['safety_tolerance'])) {
            $imageGeneration->setSafetyTolerance($options['safety_tolerance']);
        }

        return $this->generateImage($imageGeneration);
    }

    /**
     * Generate images using AI models
     *
     * @param  ImageGeneration  $imageGeneration  Image generation request
     *
     * @return ImageGenerationResponse The generated images response
     *
     * @throws MonicaApiException If the API request fails
     * @throws InvalidModelException If the model is not supported for image generation
     */
    public function generateImage(ImageGeneration $imageGeneration): ImageGenerationResponse
    {
        $model = $imageGeneration->getModel();

        if (!ImageGeneration::isModelSupported($model)) {
            throw new InvalidModelException("Image generation model '{$model}' is not supported");
        }

        $endpoint = $this->getImageGenerationEndpoint($model);
        $response = $this->httpClient->post($endpoint, $imageGeneration->toArray());

        return new ImageGenerationResponse($response);
    }

    /**
     * Get the current AI model
     *
     * @return string Current AI model identifier
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Set the AI model to use
     *
     * @param  string  $model  AI model identifier
     *
     * @throws InvalidModelException If the model is not supported
     */
    public function setModel(string $model): void
    {
        if (!$this->isModelSupported($model)) {
            throw new InvalidModelException("Model '{$model}' is not supported");
        }

        $this->model = $model;
    }

    /**
     * Check if a model is supported
     *
     * @param  string  $model  Model identifier to check
     *
     * @return bool True if the model is supported, false otherwise
     */
    public function isModelSupported(string $model): bool
    {
        foreach (self::SUPPORTED_MODELS as $provider => $models) {
            if (array_key_exists($model, $models)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the appropriate API endpoint for the image generation model
     *
     * @param  string  $model  The image generation model
     *
     * @return string API endpoint path
     */
    private function getImageGenerationEndpoint(string $model): string
    {
        // FLUX models
        if (str_starts_with($model, 'flux_')) {
            return '/v1/image/gen/flux';
        }

        // Stable Diffusion models
        if (in_array($model, ['sdxl_1_0', 'sd3', 'sd3_5'])) {
            return '/v1/image/gen/sd';
        }

        // DALL·E models
        if ($model === 'dall-e-3') {
            return '/v1/image/gen/dalle';
        }

        // Playground models
        if ($model === 'playground-v2-5') {
            return '/v1/image/gen/playground';
        }

        // Ideogram models
        if ($model === 'V_2') {
            return '/v1/image/gen/ideogram';
        }

        throw new InvalidModelException("Unknown image generation model: {$model}");
    }

    /**
     * Check if a model is supported for image generation
     *
     * @param  string  $model  Model identifier to check
     *
     * @return bool True if the model is supported for image generation
     */
    public function isImageModelSupported(string $model): bool
    {
        return ImageGeneration::isModelSupported($model);
    }

    /**
     * Get all supported models with their human-readable names
     *
     * @return array<string, string> Array of model keys => human names
     */
    public function getAllModelsWithHumanNames(): array
    {
        $allModels = [];

        foreach (self::SUPPORTED_MODELS as $provider => $models) {
            $allModels = array_merge($allModels, $models);
        }

        return $allModels;
    }

    /**
     * Get the default maximum tokens setting
     *
     * @return int|null Default max tokens or null if not set
     */
    public function getDefaultMaxTokens(): ?int
    {
        return $this->defaultMaxTokens;
    }

    /**
     * Set the default maximum tokens for future requests
     *
     * @param  int|null  $maxTokens  Maximum tokens or null to unset
     *
     * @return self For method chaining
     */
    public function setDefaultMaxTokens(?int $maxTokens): self
    {
        $this->defaultMaxTokens = $maxTokens;
        return $this;
    }

    /**
     * Get the default temperature setting
     *
     * @return float|null Default temperature or null if not set
     */
    public function getDefaultTemperature(): ?float
    {
        return $this->defaultTemperature;
    }

    /**
     * Set the default temperature for future requests
     *
     * @param  float|null  $temperature  Temperature (0.0-2.0) or null to unset
     *
     * @return self For method chaining
     */
    public function setDefaultTemperature(?float $temperature): self
    {
        $this->defaultTemperature = $temperature;
        return $this;
    }
}

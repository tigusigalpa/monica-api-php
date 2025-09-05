# Monica AI API PHP Client/SDK

[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)](https://www.php.net/)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
[![Latest Version](https://img.shields.io/github/v/release/tigusigalpa/monica-api-php)](https://github.com/tigusigalpa/monica-api-php/releases)

<div align="center">
  <img src="https://github.com/user-attachments/assets/0ca1fc1f-0e64-415f-9fd1-ecdca727499a" alt="Monica AI PHP SDK" style="max-width: 100%; height: auto;">
</div>

A powerful and elegant PHP client (SDK) library for [Monica API Platform](https://platform.monica.im/) - your unified gateway
to multiple AI models from leading providers.

## 🚀 Features

- **Unified API Access**: Single interface to multiple AI providers (OpenAI, Anthropic, Google, DeepSeek, Meta, Grok,
  NVIDIA, Mistral)
- **Latest AI Models**: Support for cutting-edge models including GPT-5, Claude 4, and Gemini 2.5
- **Image Generation**: Support for FLUX, Stable Diffusion, DALL·E, Playground, and Ideogram models
- **Type Safety**: Full PHP 8.1+ type declarations and strict typing
- **Rich Documentation**: Comprehensive PHPDoc comments and examples
- **Error Handling**: Robust exception handling with detailed error information
- **Flexible Configuration**: Support for all major AI model parameters
- **Laravel Ready**: Perfect integration with Laravel applications
- **PSR-4 Autoloading**: Modern PHP standards compliance

## 📋 Supported AI Models

| Provider      | Model                                    | Description                    | Image Input Support |
|---------------|------------------------------------------|--------------------------------|---------------------|
| **OpenAI**    | `gpt-5`                                  | GPT-5 (Latest flagship model)  | ✅ Yes               |
|               | `gpt-4.1`                                | GPT-4.1 (Main model)           | ✅ Yes               |
|               | `gpt-4.1-mini`                           | GPT-4.1 Mini (Lightweight)     | ✅ Yes               |
|               | `gpt-4.1-nano`                           | GPT-4.1 Nano (Ultra-light)     | ✅ Yes               |
|               | `gpt-4o`                                 | GPT-4o (With image support)    | ✅ Yes               |
|               | `gpt-4o-mini`                            | GPT-4o Mini (Lightweight)      | ✅ Yes               |
| **Anthropic** | `claude-sonnet-4-20250514`               | Claude 4 Sonnet                | ✅ Yes               |
|               | `claude-opus-4-20250514`                 | Claude 4 Opus                  | ✅ Yes               |
|               | `claude-3-7-sonnet-latest`               | Claude 3.7 Sonnet              | ✅ Yes               |
|               | `claude-3-5-sonnet-latest`               | Claude 3.5 Sonnet              | ✅ Yes               |
|               | `claude-3-5-haiku-latest`                | Claude 3.5 Haiku               | ❌ No                |
| **Google**    | `gemini-2.5-pro`                         | Gemini 2.5 Pro Preview         | ✅ Yes               |
|               | `gemini-2.5-flash`                       | Gemini 2.5 Flash               | ✅ Yes               |
| **DeepSeek**  | `deepseek-reasoner`                      | DeepSeek V3 Reasoner           | ❌ No                |
|               | `deepseek-chat`                          | DeepSeek V3 Chat               | ❌ No                |
| **Meta**      | `meta-llama/llama-3-8b-instruct`         | Meta: Llama 3 8B Instruct      | ❌ No                |
|               | `meta-llama/llama-3.1-8b-instruct`       | Meta: Llama 3.1 8B Instruct    | ❌ No                |
| **Grok**      | `x-ai/grok-3-beta`                       | Grok 3 Beta                    | ❌ No                |
| **NVIDIA**    | `nvidia/llama-3.1-nemotron-70b-instruct` | NVIDIA: Llama 3.1 Nemotron 70B | ❌ No                |
| **Mistral**   | `mistralai/mistral-7b-instruct`          | Mistral: Mistral 7B Instruct   | ❌ No                |

> **📝 Note**: Only OpenAI, Anthropic (Claude), and Google (Gemini) models support image input in chat requests. Other
> providers will return an error if images are included in the request.

## 🎨 Supported Image Generation Models

### FLUX Models

- **FLUX.1 Schnell**: Entry-level model optimized for speed and efficiency
- **FLUX.1 Dev**: Developer-focused variant with enhanced customization options
- **FLUX.1 Pro**: Professional-grade model with highest quality output

### Stable Diffusion Models

- **Stable Diffusion XL 1.0**: Efficient image generation with good quality
- **Stable Diffusion 3**: Advanced model with better prompting and higher quality
- **Stable Diffusion 3.5 Large**: Latest model with exceptional detail and realism

### DALL·E Models

- **DALL·E 3**: Highly detailed and photorealistic images with superior understanding

### Playground Models

- **Playground V2.5**: Cost-effective solution with strong artistic style interpretation

### Ideogram Models

- **Ideogram V2**: Exceptional text rendering capabilities, ideal for logos and typography

## 🛠 Installation

Install via Composer:

```bash
composer require tigusigalpa/-api-php
```

## 🔧 Requirements

- PHP 8.1 or higher
- Guzzle HTTP 7.0+
- Monica AI API key ([Get yours here](https://platform.monica.im/))

## 🚀 Quick Start

```php
<?php

require_once 'vendor/autoload.php';

use Tigusigalpa\MonicaApi\MonicaClient;
use Tigusigalpa\MonicaApi\Exceptions\MonicaApiException;
use Tigusigalpa\MonicaApi\Exceptions\InvalidModelException;

// Initialize the client with GPT-5 (latest flagship model)
$client = new MonicaClient('your-monica-api-key', 'gpt-5');

try {
    // Simple chat completion
    $response = $client->chat('Hello! How are you today?');
    echo $response->getContent();
    
} catch (MonicaApiException $e) {
    echo "API Error: " . $e->getMessage();
} catch (InvalidModelException $e) {
    echo "Invalid Model: " . $e->getMessage();
}
```

## 📖 Usage Examples

### Basic Chat Completion

```php
use Tigusigalpa\MonicaApi\MonicaClient;

// Use GPT-5 for the most advanced AI capabilities
$client = new MonicaClient('your-api-key', 'gpt-5');

$response = $client->chat('Explain quantum computing in simple terms');
echo $response->getContent();
```

### Advanced Configuration

```php
$response = $client->chat('Write a creative story', [
    'system' => 'You are a creative storyteller',
    'temperature' => 0.8,
    'max_tokens' => 500,
    'top_p' => 0.9
]);
```

### Conversation with Multiple Messages

```php
use Tigusigalpa\MonicaApi\Models\ChatMessage;

$messages = [
    ChatMessage::system('You are a helpful programming assistant'),
    ChatMessage::user('How do I create a PHP class?'),
    ChatMessage::assistant('To create a PHP class, use the `class` keyword...'),
    ChatMessage::user('Can you show me an example?')
];

$response = $client->chatWithMessages($messages, [
    'temperature' => 0.3,
    'max_tokens' => 1000
]);
```

### Understanding Chat Methods: `chat()` vs `chatWithMessages()`

MonicaAPI provides two main methods for chat interactions, each designed for different use cases:

#### Method Comparison

| Feature               | `chat()`                    | `chatWithMessages()`                   |
|-----------------------|-----------------------------|----------------------------------------|
| **Input Type**        | `string` (simple text)      | `ChatMessage[]` (array of messages)    |
| **Use Case**          | Single message              | Multiple messages in one request       |
| **Message Structure** | ✅ Auto-creates user message | ✅ Full control over message roles      |
| **Image Support**     | ❌ Text only                 | ✅ Multimodal (text + images)           |
| **System Messages**   | ✅ Via options parameter     | ✅ As separate ChatMessage objects      |
| **Complexity**        | 🟢 Simple and quick         | 🟡 More setup required                 |
| **Flexibility**       | 🟡 Limited customization    | 🟢 Full control over message structure |
| **Best For**          | Quick queries, testing      | Complex messages, image analysis       |

#### When to Use `chat()`

Perfect for simple, standalone interactions:

```php
// Quick questions
$response = $client->chat('What is the capital of France?');

// Simple tasks with system context
$response = $client->chat('Translate this to Spanish: Hello world', [
    'system' => 'You are a professional translator',
    'temperature' => 0.3
]);

// Testing and prototyping
$response = $client->chat('Explain quantum physics in simple terms');
```

#### When to Use `chatWithMessages()`

Essential for complex message structures:

```php
// Multiple messages with different roles
$messages = [
    ChatMessage::system('You are a helpful coding assistant'),
    ChatMessage::user('How do I create a REST API in PHP?'),
    ChatMessage::assistant('To create a REST API in PHP, you can use...'),
    ChatMessage::user('Can you show me a complete example?')
];

$response = $client->chatWithMessages($messages);

// Image analysis (requires chatWithMessages)
$message = ChatMessage::user('What do you see in this image?');
$message->addImageFromFile('photo.jpg');

$response = $client->chatWithMessages([$message]);

// Complex multimodal messages
$messageData = [
    'role' => 'user',
    'content' => [
        ['type' => 'text', 'text' => 'Analyze this diagram:'],
        ['type' => 'image_url', 'image_url' => ['url' => 'data:image/jpeg;base64,...']]
    ]
];

$message = ChatMessage::fromArray($messageData);
$response = $client->chatWithMessages([$message]);
```

#### Migration Guide

If you need to upgrade from `chat()` to `chatWithMessages()`:

```php
// Before: Using chat()
$response = $client->chat('Hello, how are you?', [
    'system' => 'You are a friendly assistant'
]);

// After: Using chatWithMessages()
$messages = [
    ChatMessage::system('You are a friendly assistant'),
    ChatMessage::user('Hello, how are you?')
];

$response = $client->chatWithMessages($messages);
```

### Chat with Images (Vision Models)

Vision-capable models like GPT-5 and GPT-4o can analyze and discuss images. Here are examples of how to upload images to
chat:

#### Upload Image from File

```php
<?php

use Tigusigalpa\MonicaApi\MonicaClient;
use Tigusigalpa\MonicaApi\Models\ChatMessage;

// GPT-5 provides the most advanced image analysis capabilities
$client = new MonicaClient('your-api-key', 'gpt-5');

// Create a message with image from file
$message = ChatMessage::userWithImage(
    'What do you see in this image?',
    'path/to/your/image.jpg'
);

// Alternative: Add image to existing message
$message = ChatMessage::user('Analyze this image for me');
$message->addImageFromFile('path/to/your/image.jpg', 'high'); // detail level: low, high, auto

$response = $client->chatWithMessages([$message]);
echo $response->getContent();
```

#### Upload Image from URL

```php
// Create message with image from URL
$message = ChatMessage::userWithImage(
    'Describe what you see in this photo',
    'https://example.com/image.jpg'
);

$response = $client->chatWithMessages([$message]);
echo $response->getContent();
```

#### Upload Multiple Images

```php
// Upload multiple images at once
$imageUrls = [
    'https://example.com/image1.jpg',
    'https://example.com/image2.jpg',
    'path/to/local/image3.png'
];

$message = ChatMessage::userWithImages(
    'Compare these images and tell me the differences',
    $imageUrls
);

$response = $client->chatWithMessages([$message]);
echo $response->getContent();
```

#### Upload Image from Base64

```php
// Upload image from base64 data
$base64ImageData = base64_encode(file_get_contents('image.jpg'));

$message = ChatMessage::user('What breed is this dog?');
$message->addImageFromBase64($base64ImageData, 'image/jpeg', 'high');

$response = $client->chatWithMessages([$message]);
echo $response->getContent();
```

#### Advanced Image Chat Example

```php
use Tigusigalpa\MonicaApi\Models\ChatMessage;

$messages = [
    ChatMessage::system('You are an expert art critic and historian.'),
    ChatMessage::userWithImage(
        'Please analyze this painting in detail',
        'path/to/painting.jpg'
    )
];

$response = $client->chatWithMessages($messages, [
    'temperature' => 0.7,
    'max_tokens' => 1500
]);

echo "Art Analysis: " . $response->getContent();

// Continue the conversation with follow-up questions
$messages[] = ChatMessage::assistant($response->getContent());
$messages[] = ChatMessage::user('What art movement does this belong to?');

$followUp = $client->chatWithMessages($messages);
echo "Art Movement: " . $followUp->getContent();
```

#### Working with Image Details

```php
// Control image processing detail level
$message = ChatMessage::user('Examine this image closely');
$message->addImageFromFile('detailed_image.jpg', 'high'); // More detailed analysis
// or
$message->addImageFromFile('simple_image.jpg', 'low');   // Faster, less detailed

// Check if message has images
if ($message->hasImages()) {
    echo "Message contains " . count($message->getImages()) . " images";
}

// Get image information
$images = $message->getImages();
foreach ($images as $image) {
    echo "Image URL: " . $image['image_url']['url'] . "\n";
    echo "Detail level: " . $image['image_url']['detail'] . "\n";
}
```

#### Supported Vision Models

The following models support image analysis:

- **GPT-5**: Most advanced image analysis and understanding capabilities
- **GPT-4o**: Excellent for detailed image analysis and understanding
- **GPT-4o Mini**: Faster, cost-effective option for basic image tasks

### Creating Messages from Array Data

You can create `ChatMessage` instances directly from array data using the `fromArray()` method. This is particularly
useful when working with pre-structured message data or when integrating with existing systems that use
OpenAI-compatible message formats.

#### Basic Usage

```php
// Create a simple text message from array
$messageData = [
    'role' => 'user',
    'content' => 'Hello, how are you today?'
];

$message = ChatMessage::fromArray($messageData);
$response = $client->chatWithMessages([$message]);
```

#### Multimodal Messages with Mixed Content

```php
// Create a complex multimodal message with text and images
$messageData = [
    'role' => 'user',
    'content' => [
        [
            'type' => 'text',
            'text' => 'Please solve this equation step by step:'
        ],
        [
            'type' => 'image_url',
            'image_url' => [
                'url' => 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAA...',
                'detail' => 'high'
            ]
        ],
        [
            'type' => 'text',
            'text' => 'Show all intermediate steps in your solution.'
        ],
        [
            'type' => 'image_url',
            'image_url' => [
                'url' => 'https://example.com/reference-image.png',
                'detail' => 'auto'
            ]
        ]
    ]
];

$message = ChatMessage::fromArray($messageData);
$response = $client->chatWithMessages([$message]);
echo $response->getContent();
```

#### Working with Conversation Arrays

```php
// Create multiple messages from array data
$conversationData = [
    [
        'role' => 'system',
        'content' => 'You are a helpful math tutor.'
    ],
    [
        'role' => 'user',
        'content' => [
            [
                'type' => 'text',
                'text' => 'Help me understand this problem:'
            ],
            [
                'type' => 'image_url',
                'image_url' => [
                    'url' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAA...',
                    'detail' => 'high'
                ]
            ]
        ]
    ]
];

// Convert array data to ChatMessage objects
$messages = array_map(function($messageData) {
    return ChatMessage::fromArray($messageData);
}, $conversationData);

$response = $client->chatWithMessages($messages);
echo $response->getContent();
```

#### Helper Function for Batch Processing

```php
/**
 * Convert an array of message data to ChatMessage objects
 */
function createMessagesFromArray(array $messagesData): array
{
    return array_map(function($messageData) {
        return ChatMessage::fromArray($messageData);
    }, $messagesData);
}

// Usage
$messagesData = [
    ['role' => 'system', 'content' => 'You are an expert assistant.'],
    ['role' => 'user', 'content' => 'What is the capital of France?'],
    ['role' => 'assistant', 'content' => 'The capital of France is Paris.'],
    ['role' => 'user', 'content' => 'Tell me more about it.']
];

$messages = createMessagesFromArray($messagesData);
$response = $client->chatWithMessages($messages);
```

#### Supported Array Structure

The `fromArray()` method supports the following message structure:

```php
[
    'role' => 'user|assistant|system',           // Required: Message role
    'content' => 'string' | [                   // Required: Message content
        [
            'type' => 'text',
            'text' => 'Text content here'
        ],
        [
            'type' => 'image_url',
            'image_url' => [
                'url' => 'https://... or data:image/...',  // Image URL or base64 data URL
                'detail' => 'low|high|auto'                // Optional: Detail level
            ]
        ]
    ],
    'name' => 'optional_message_name'           // Optional: Message name
]
```

## 🎨 Image Generation Usage

### Simple Image Generation

```php
<?php

use Tigusigalpa\MonicaApi\MonicaClient;

$client = new MonicaClient('your-api-key', 'gpt-5');

// Generate a single image with FLUX
$response = $client->generateImageSimple(
    'flux_dev',
    'A beautiful sunset over mountains, digital art style',
    [
        'size' => '1024x1024',
        'steps' => 25,
        'guidance' => 3.5,
        'seed' => 42
    ]
);

echo "Generated image URL: " . $response->getFirstImageUrl();

// Save the image
$response->saveFirstImage('sunset.png');
```

### Advanced Image Generation

```php
<?php

use Tigusigalpa\MonicaApi\Models\ImageGeneration;

// Create detailed image generation request
$imageGen = new ImageGeneration('sd3_5', 'A majestic dragon flying over a medieval castle');
$imageGen->setNegativePrompt('blurry, low quality, distorted')
         ->setSize('1024x1024')
         ->setSteps(30)
         ->setCfgScale(7.5)
         ->setSeed(123);

$response = $client->generateImage($imageGen);

// Work with multiple images
foreach ($response->getImageUrls() as $index => $url) {
    echo "Image " . ($index + 1) . ": {$url}\n";
}

// Save all images to directory
$savedFiles = $response->saveAllImages('./images/', 'dragon_', 'png');
```

### Model-Specific Examples

#### DALL·E 3 with Quality Options

```php
$response = $client->generateImageSimple(
    'dall-e-3',
    'A cute robot playing with colorful balloons in a park',
    [
        'size' => '1024x1024',
        'quality' => 'hd',
        'style' => 'vivid'
    ]
);
```

#### Ideogram V2 for Text and Logos

```php
$imageGen = new ImageGeneration('V_2', 'Logo design for "TECH STARTUP" with modern typography');
$imageGen->setAspectRatio('ASPECT_16_9')
         ->setMagicPromptOption('AUTO')
         ->setStyleType('AUTO');

$response = $client->generateImage($imageGen);
```

#### Playground V2.5 with Multiple Outputs

```php
$response = $client->generateImageSimple(
    'playground-v2-5',
    'Abstract geometric patterns in vibrant colors',
    [
        'count' => 3,
        'size' => '1024x1024',
        'step' => 30,
        'cfg_scale' => 7.0
    ]
);
```

### Model Management

```php
// Check if a model is supported
if ($client->isModelSupported('gpt-5')) {
    $client->setModel('gpt-5');
}

// Get all supported models
$models = MonicaClient::getSupportedModels();
foreach ($models as $provider => $providerModels) {
    echo "Provider: $provider\n";
    foreach ($providerModels as $modelId => $modelName) {
        echo "  - $modelId: $modelName\n";
    }
}

// Get models by specific provider
$openaiModels = MonicaClient::getModelsByProvider('OpenAI');
```

### Error Handling

```php
try {
    $response = $client->chat('Hello world');
} catch (InvalidModelException $e) {
    // Handle invalid model errors
    echo "Model error: " . $e->getUserFriendlyMessage();
    
    // Get suggestions for similar models
    $suggestions = $e->getSuggestions();
    if (!empty($suggestions)) {
        echo "Did you mean: " . implode(', ', $suggestions);
    }
    
} catch (MonicaApiException $e) {
    // Handle API errors
    if ($e->isAuthenticationError()) {
        echo "Please check your API key";
    } elseif ($e->isRateLimitError()) {
        echo "Rate limit exceeded, please wait";
    } elseif ($e->isQuotaError()) {
        echo "API quota exceeded";
    } else {
        echo "API Error: " . $e->getUserFriendlyMessage();
    }
}
```

### Working with Response Data

```php
$response = $client->chat('Tell me a joke');

// Get response content
echo $response->getContent();

// Get usage statistics
echo "Tokens used: " . $response->getTotalTokens() . "\n";
echo "Prompt tokens: " . $response->getPromptTokens() . "\n";
echo "Completion tokens: " . $response->getCompletionTokens() . "\n";

// Check completion status
if ($response->isComplete()) {
    echo "Response completed normally";
} elseif ($response->wasTruncated()) {
    echo "Response was truncated due to length limit";
} elseif ($response->wasFiltered()) {
    echo "Response was filtered due to content policy";
}

// Get response as ChatMessage object
$message = $response->getFirstChoiceAsMessage();
if ($message) {
    echo $message->getRole() . ": " . $message->getContent();
}
```

## 🔧 Configuration Options

### Chat Completion Parameters

| Parameter           | Type   | Description                           | Range           |
|---------------------|--------|---------------------------------------|-----------------|
| `system`            | string | System message to set AI behavior     | -               |
| `temperature`       | float  | Controls randomness in responses      | 0.0 - 2.0       |
| `max_tokens`        | int    | Maximum tokens in response            | 1 - model limit |
| `top_p`             | float  | Nucleus sampling parameter            | 0.0 - 1.0       |
| `frequency_penalty` | float  | Reduces repetition of frequent tokens | -2.0 - 2.0      |
| `presence_penalty`  | float  | Reduces repetition of any tokens      | -2.0 - 2.0      |

### Example with All Parameters

```php
$response = $client->chat('Write a poem about nature', [
    'system' => 'You are a poetic AI that writes beautiful verses',
    'temperature' => 0.7,
    'max_tokens' => 300,
    'top_p' => 0.9,
    'frequency_penalty' => 0.1,
    'presence_penalty' => 0.1
]);
```

## 🏗 Laravel Integration

### Service Provider Registration

```php
// config/app.php
'providers' => [
    // ...
    App\Providers\MonicaServiceProvider::class,
],
```

### Service Provider Example

```php
<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Tigusigalpa\MonicaApi\MonicaClient;

class MonicaServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(MonicaClient::class, function ($app) {
            return new MonicaClient(
                config('services.monica.api_key'),
                config('services.monica.default_model', 'gpt-5')
            );
        });
    }
}
```

### Configuration

```php
// config/services.php
'monica' => [
    'api_key' => env('MONICA_API_KEY'),
    'default_model' => env('MONICA_DEFAULT_MODEL', 'gpt-5'),
],
```

### Environment Variables

```bash
# .env
MONICA_API_KEY=your-monica-api-key-here
MONICA_DEFAULT_MODEL=gpt-5
```

### Controller Example

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Tigusigalpa\MonicaApi\MonicaClient;
use Tigusigalpa\MonicaApi\Exceptions\MonicaApiException;

class ChatController extends Controller
{
    public function __construct(
        private MonicaClient $monica
    ) {}

    public function chat(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:4000',
            'model' => 'sometimes|string',
        ]);

        try {
            if ($request->has('model')) {
                $this->monica->setModel($request->model);
            }

            $response = $this->monica->chat($request->message);

            return response()->json([
                'success' => true,
                'response' => $response->getContent(),
                'model' => $this->monica->getModel(),
                'tokens_used' => $response->getTotalTokens(),
            ]);

        } catch (MonicaApiException $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getUserFriendlyMessage(),
            ], 500);
        }
    }
}
```

## 🧪 Testing

Run the test suite:

```bash
composer test
```

Run with coverage:

```bash
composer test:coverage
```

## 📚 API Reference

### MonicaClient

#### Constructor

```php
new MonicaClient(string $apiKey, string $model)
```

#### Methods

- `chat(string $message, array $options = []): ChatCompletionResponse`
- `chatWithMessages(ChatMessage[] $messages, array $options = []): ChatCompletionResponse`
- `generateImage(ImageGeneration $imageGeneration): ImageGenerationResponse`
- `generateImageSimple(string $model, string $prompt, array $options = []): ImageGenerationResponse`
- `setModel(string $model): void`
- `getModel(): string`
- `isModelSupported(string $model): bool`
- `static getSupportedModels(): array`
- `static getModelsByProvider(string $provider): array`
- `static getAllModelIds(): array`
- `static getSupportedImageModels(): array`

### ChatMessage

#### Static Constructors

- `ChatMessage::system(string $content, ?string $name = null): ChatMessage`
- `ChatMessage::user(string $content, ?string $name = null): ChatMessage`
- `ChatMessage::assistant(string $content, ?string $name = null): ChatMessage`
- `ChatMessage::userWithImage(string $content, string $imageUrl): ChatMessage`
- `ChatMessage::userWithImages(string $content, array $imageUrls): ChatMessage`
- `ChatMessage::fromArray(array $data): ChatMessage`

#### Methods

- `getRole(): string`
- `getContent(): string`
- `getName(): ?string`
- `isSystem(): bool`
- `isUser(): bool`
- `isAssistant(): bool`
- `hasImages(): bool`
- `getImages(): array`
- `addImageFromFile(string $filePath, string $detail = 'auto'): self`
- `addImageFromUrl(string $url, string $detail = 'auto'): self`
- `addImageFromBase64(string $base64Data, string $mimeType, string $detail = 'auto'): self`
- `toArray(): array`

### ChatCompletionResponse

#### Methods

- `getContent(): string`
- `getRole(): string`
- `getFinishReason(): ?string`
- `getTotalTokens(): int`
- `getPromptTokens(): int`
- `getCompletionTokens(): int`
- `isComplete(): bool`
- `wasTruncated(): bool`
- `wasFiltered(): bool`
- `getFirstChoice(): ?array`
- `getFirstChoiceAsMessage(): ?ChatMessage`
- `getAllChoices(): array`

### ImageGeneration

#### Constructor

```php
new ImageGeneration(string $model, string $prompt)
```

#### Methods

- `getModel(): string`
- `getPrompt(): string`
- `setNegativePrompt(string $negativePrompt): self`
- `setNumOutputs(int $numOutputs): self`
- `setSize(string $size): self`
- `setSeed(int $seed): self`
- `setSteps(int $steps): self`
- `setGuidance(float $guidance): self`
- `setCfgScale(float $cfgScale): self`
- `setQuality(string $quality): self`
- `setStyle(string $style): self`
- `setAspectRatio(string $aspectRatio): self`
- `setMagicPromptOption(string $option): self`
- `setStyleType(string $styleType): self`
- `setSafetyTolerance(int $tolerance): self`
- `static isModelSupported(string $model): bool`
- `static getSupportedModels(): array`
- `toArray(): array`

### ImageGenerationResponse

#### Methods

- `getImageUrls(): array`
- `getFirstImageUrl(): ?string`
- `saveFirstImage(string $filePath): bool`
- `saveAllImages(string $directory, string $prefix = 'image_', string $extension = 'png'): array`
- `getImageCount(): int`

## 📝 Changelog

### [Unreleased]

#### Added

- **GPT-5 Support**: Added support for OpenAI's latest flagship model `gpt-5`
    - Full chat completion capabilities with advanced reasoning
    - Enhanced image analysis and multimodal understanding
    - Updated default model examples to showcase GPT-5
    - Added GPT-5 to supported vision models list

#### Changed

- Updated Quick Start example to use GPT-5 as the default model
- Enhanced Laravel integration examples with GPT-5 configuration
- Updated model comparison tables to highlight GPT-5 capabilities

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request. For major changes, please open an issue first to
discuss what you would like to change.

### Development Setup

1. Clone the repository
2. Install dependencies: `composer install`
3. Run tests: `composer test`
4. Check code style: `composer cs-check`
5. Fix code style: `composer cs-fix`

## 📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🔗 Links

- [Monica API Platform](https://platform.monica.im/)
- [API Documentation](https://platform.monica.im/docs/en/overview)
- [GitHub Repository](https://github.com/tigusigalpa/monica-api-php)
- [Packagist](https://packagist.org/packages/tigusigalpa/monica-api-php)

## 💬 Support

If you have any questions or need help, please:

1. Check the [documentation](https://platform.monica.im/docs/en/overview)
2. Search existing [GitHub issues](https://github.com/tigusigalpa/monica-api-php/issues)
3. Create a new issue if needed

## 🙏 Acknowledgments

- [Monica API Platform](https://platform.monica.im/) for providing the unified AI API
- All the AI providers (OpenAI, Anthropic, Google, etc.) for their amazing models
- The PHP community for excellent tools and libraries

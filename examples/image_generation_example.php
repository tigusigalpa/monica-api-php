<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Tigusigalpa\MonicaApi\MonicaClient;
use Tigusigalpa\MonicaApi\Models\ImageGeneration;
use Tigusigalpa\MonicaApi\Exceptions\MonicaApiException;
use Tigusigalpa\MonicaApi\Exceptions\InvalidModelException;

/**
 * Monica API Image Generation Examples
 *
 * This file demonstrates how to use the Monica API PHP client
 * for image generation with different AI models.
 *
 * @author Igor Sazonov <sovletig@gmail.com>
 */

// Initialize the client with your API key
$apiKey = 'your-monica-api-key-here';
$client = new MonicaClient($apiKey, 'gpt-4.1'); // Chat model for client initialization

try {
    echo "=== Monica API Image Generation Examples ===\n\n";

    // Example 1: Simple FLUX image generation
    echo "1. Generating image with FLUX.1 Dev model...\n";
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

    echo "Generated {$response->getImageCount()} image(s)\n";
    echo "First image URL: {$response->getFirstImageUrl()}\n";
    
    // Save the image
    if ($response->saveFirstImage(__DIR__ . '/flux_example.png')) {
        echo "Image saved to flux_example.png\n";
    }
    echo "\n";

    // Example 2: Stable Diffusion with negative prompt
    echo "2. Generating image with Stable Diffusion 3.5...\n";
    $imageGen = new ImageGeneration('sd3_5', 'A majestic dragon flying over a medieval castle');
    $imageGen->setNegativePrompt('blurry, low quality, distorted')
             ->setSize('1024x1024')
             ->setSteps(30)
             ->setCfgScale(7.5)
             ->setSeed(123);

    $response = $client->generateImage($imageGen);
    echo "Generated image URL: {$response->getFirstImageUrl()}\n";
    echo "\n";

    // Example 3: DALL·E 3 with quality and style options
    echo "3. Generating image with DALL·E 3...\n";
    $response = $client->generateImageSimple(
        'dall-e-3',
        'A cute robot playing with colorful balloons in a park',
        [
            'size' => '1024x1024',
            'quality' => 'hd',
            'style' => 'vivid'
        ]
    );

    echo "DALL·E generated image: {$response->getFirstImageUrl()}\n";
    echo "\n";

    // Example 4: Playground V2.5 with multiple images
    echo "4. Generating multiple images with Playground V2.5...\n";
    $response = $client->generateImageSimple(
        'playground-v2-5',
        'Abstract geometric patterns in vibrant colors',
        [
            'count' => 2,
            'size' => '1024x1024',
            'step' => 30,
            'cfg_scale' => 7.0
        ]
    );

    echo "Generated {$response->getImageCount()} images:\n";
    foreach ($response->getImageUrls() as $index => $url) {
        echo "  Image " . ($index + 1) . ": {$url}\n";
    }
    echo "\n";

    // Example 5: Ideogram V2 with aspect ratio and magic prompt
    echo "5. Generating image with Ideogram V2...\n";
    $imageGen = new ImageGeneration('V_2', 'Logo design for "TECH STARTUP" with modern typography');
    $imageGen->setAspectRatio('ASPECT_16_9')
             ->setMagicPromptOption('AUTO')
             ->setStyleType('AUTO')
             ->setSeed(456);

    $response = $client->generateImage($imageGen);
    echo "Ideogram generated image: {$response->getFirstImageUrl()}\n";
    echo "\n";

    // Example 6: Batch generation with different models
    echo "6. Batch generation with different models...\n";
    $models = [
        'flux_schnell' => 'A serene lake at dawn',
        'sdxl_1_0' => 'Cyberpunk cityscape at night',
        'dall-e-3' => 'A friendly AI assistant as a cartoon character'
    ];

    foreach ($models as $model => $prompt) {
        echo "Generating with {$model}...\n";
        try {
            $response = $client->generateImageSimple($model, $prompt, ['size' => '1024x1024']);
            echo "  Success: {$response->getFirstImageUrl()}\n";
        } catch (Exception $e) {
            echo "  Error: {$e->getMessage()}\n";
        }
    }
    echo "\n";

    // Example 7: Download and save images
    echo "7. Downloading and saving images...\n";
    $response = $client->generateImageSimple(
        'flux_dev',
        'A magical forest with glowing mushrooms',
        ['size' => '1024x1024', 'seed' => 789]
    );

    // Save to specific directory
    $savedFiles = $response->saveAllImages(__DIR__ . '/generated_images/', 'magical_forest_', 'png');
    echo "Saved " . count($savedFiles) . " images:\n";
    foreach ($savedFiles as $file) {
        echo "  {$file}\n";
    }
    echo "\n";

    // Example 8: Working with response data
    echo "8. Working with response data...\n";
    $response = $client->generateImageSimple('flux_dev', 'A space station orbiting Earth');
    
    echo "Response details:\n";
    echo "  Image count: {$response->getImageCount()}\n";
    echo "  Has images: " . ($response->hasImages() ? 'Yes' : 'No') . "\n";
    echo "  First image data: " . json_encode($response->getImageData(0)) . "\n";
    echo "  JSON representation: {$response->toJson()}\n";
    echo "\n";

    // Display all supported image models
    echo "=== Supported Image Generation Models ===\n";
    $supportedModels = MonicaClient::getSupportedImageModels();
    foreach ($supportedModels as $model => $description) {
        echo "  {$model}: {$description}\n";
    }

} catch (InvalidModelException $e) {
    echo "Invalid model error: {$e->getMessage()}\n";
} catch (MonicaApiException $e) {
    echo "API error: {$e->getMessage()}\n";
} catch (Exception $e) {
    echo "General error: {$e->getMessage()}\n";
}

echo "\n=== Examples completed ===\n";

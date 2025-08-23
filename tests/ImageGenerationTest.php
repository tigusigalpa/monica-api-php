<?php

declare(strict_types = 1);

/**
 * Image Generation Tests
 *
 * This file contains unit tests for the image generation functionality
 * of the Monica API PHP client.
 *
 * @package   Tigusigalpa\MonicaApi\Tests
 * @author    Igor Sazonov <sovletig@gmail.com>
 * @copyright 2025 Igor Sazonov
 * @license   MIT License
 */

namespace Tigusigalpa\MonicaApi\Tests;

use PHPUnit\Framework\TestCase;
use Tigusigalpa\MonicaApi\Models\ImageGeneration;
use Tigusigalpa\MonicaApi\Models\ImageGenerationResponse;
use Tigusigalpa\MonicaApi\MonicaClient;
use Tigusigalpa\MonicaApi\Exceptions\InvalidModelException;

/**
 * Image Generation Test Class
 *
 * Tests the image generation functionality including model validation,
 * request building, and response handling.
 *
 * @package Tigusigalpa\MonicaApi\Tests
 * @author  Igor Sazonov <sovletig@gmail.com>
 * @since   1.1.0
 */
class ImageGenerationTest extends TestCase
{
    /**
     * Test ImageGeneration model creation and basic functionality
     */
    public function testImageGenerationCreation(): void
    {
        $imageGen = new ImageGeneration('flux_dev', 'A beautiful landscape');
        
        $this->assertEquals('flux_dev', $imageGen->getModel());
        $this->assertEquals('A beautiful landscape', $imageGen->getPrompt());
    }

    /**
     * Test ImageGeneration fluent interface
     */
    public function testImageGenerationFluentInterface(): void
    {
        $imageGen = new ImageGeneration('sd3_5', 'A dragon in the sky');
        
        $result = $imageGen->setNegativePrompt('blurry')
                          ->setSize('1024x1024')
                          ->setSteps(30)
                          ->setSeed(42);
        
        $this->assertSame($imageGen, $result);
        
        $array = $imageGen->toArray();
        $this->assertEquals('sd3_5', $array['model']);
        $this->assertEquals('A dragon in the sky', $array['prompt']);
        $this->assertEquals('blurry', $array['negative_prompt']);
        $this->assertEquals('1024x1024', $array['size']);
        $this->assertEquals(30, $array['steps']);
        $this->assertEquals('42', $array['seed']);
    }

    /**
     * Test FLUX model parameter mapping
     */
    public function testFluxModelParameters(): void
    {
        $imageGen = new ImageGeneration('flux_dev', 'Test prompt');
        $imageGen->setSize('1024x1024')
                 ->setSteps(25)
                 ->setGuidance(3.5)
                 ->setSafetyTolerance(2)
                 ->setSeed(123);

        $array = $imageGen->toArray();
        
        $this->assertEquals('flux_dev', $array['model']);
        $this->assertEquals('Test prompt', $array['prompt']);
        $this->assertEquals(1, $array['num_outputs']);
        $this->assertEquals('1024x1024', $array['size']);
        $this->assertEquals(123, $array['seed']);
        $this->assertEquals(25, $array['steps']);
        $this->assertEquals('3.5', $array['guidance']);
        $this->assertEquals(2, $array['safety_tolerance']);
        $this->assertEquals(2, $array['interval']);
    }

    /**
     * Test Stable Diffusion model parameter mapping
     */
    public function testStableDiffusionParameters(): void
    {
        $imageGen = new ImageGeneration('sd3_5', 'Test prompt');
        $imageGen->setNegativePrompt('bad quality')
                 ->setSize('1024x1024')
                 ->setSteps(28)
                 ->setCfgScale(7.5)
                 ->setSeed(456);

        $array = $imageGen->toArray();
        
        $this->assertEquals('sd3_5', $array['model']);
        $this->assertEquals('Test prompt', $array['prompt']);
        $this->assertEquals('bad quality', $array['negative_prompt']);
        $this->assertEquals(1, $array['num_outputs']);
        $this->assertEquals('1024x1024', $array['size']);
        $this->assertEquals('456', $array['seed']);
        $this->assertEquals(28, $array['steps']);
        $this->assertEquals('7.5', $array['cfg_scale']);
        $this->assertEquals(90, $array['output_quality']);
        $this->assertEquals('K_EULER', $array['scheduler']);
        $this->assertEquals(28, $array['num_inference_steps']);
    }

    /**
     * Test DALL·E model parameter mapping
     */
    public function testDalleParameters(): void
    {
        $imageGen = new ImageGeneration('dall-e-3', 'Test prompt');
        $imageGen->setSize('1024x1024')
                 ->setQuality('hd')
                 ->setStyle('vivid');

        $array = $imageGen->toArray();
        
        $this->assertEquals('dall-e-3', $array['model']);
        $this->assertEquals('Test prompt', $array['prompt']);
        $this->assertEquals(1, $array['n']);
        $this->assertEquals('1024x1024', $array['size']);
        $this->assertEquals('hd', $array['quality']);
        $this->assertEquals('vivid', $array['style']);
    }

    /**
     * Test Ideogram model parameter mapping
     */
    public function testIdeogramParameters(): void
    {
        $imageGen = new ImageGeneration('V_2', 'Logo design');
        $imageGen->setAspectRatio('ASPECT_16_9')
                 ->setMagicPromptOption('AUTO')
                 ->setStyleType('AUTO')
                 ->setSeed(789);

        $array = $imageGen->toArray();
        
        $this->assertEquals('V_2', $array['model']);
        $this->assertEquals('Logo design', $array['prompt']);
        $this->assertEquals('ASPECT_16_9', $array['aspect_ratio']);
        $this->assertEquals('AUTO', $array['magic_prompt_option']);
        $this->assertEquals('AUTO', $array['style_type']);
        $this->assertEquals(789, $array['seed']);
    }

    /**
     * Test supported models validation
     */
    public function testSupportedModels(): void
    {
        $supportedModels = ImageGeneration::getSupportedModels();
        
        $this->assertIsArray($supportedModels);
        $this->assertArrayHasKey('flux_dev', $supportedModels);
        $this->assertArrayHasKey('sd3_5', $supportedModels);
        $this->assertArrayHasKey('dall-e-3', $supportedModels);
        $this->assertArrayHasKey('playground-v2-5', $supportedModels);
        $this->assertArrayHasKey('V_2', $supportedModels);
        
        $this->assertTrue(ImageGeneration::isModelSupported('flux_dev'));
        $this->assertTrue(ImageGeneration::isModelSupported('sd3_5'));
        $this->assertFalse(ImageGeneration::isModelSupported('invalid-model'));
    }

    /**
     * Test ImageGenerationResponse with mock data
     */
    public function testImageGenerationResponse(): void
    {
        $mockResponse = [
            'data' => [
                ['url' => 'https://example.com/image1.png'],
                ['url' => 'https://example.com/image2.png']
            ]
        ];

        $response = new ImageGenerationResponse($mockResponse);
        
        $this->assertTrue($response->hasImages());
        $this->assertEquals(2, $response->getImageCount());
        $this->assertEquals('https://example.com/image1.png', $response->getFirstImageUrl());
        
        $urls = $response->getImageUrls();
        $this->assertCount(2, $urls);
        $this->assertEquals('https://example.com/image1.png', $urls[0]);
        $this->assertEquals('https://example.com/image2.png', $urls[1]);
        
        $this->assertEquals($mockResponse, $response->getRawResponse());
    }

    /**
     * Test empty ImageGenerationResponse
     */
    public function testEmptyImageGenerationResponse(): void
    {
        $emptyResponse = ['data' => []];
        $response = new ImageGenerationResponse($emptyResponse);
        
        $this->assertFalse($response->hasImages());
        $this->assertEquals(0, $response->getImageCount());
        $this->assertNull($response->getFirstImageUrl());
        $this->assertEmpty($response->getImageUrls());
    }

    /**
     * Test MonicaClient image generation method signatures
     */
    public function testMonicaClientImageMethods(): void
    {
        // Test that the methods exist and have correct signatures
        $this->assertTrue(method_exists(MonicaClient::class, 'generateImage'));
        $this->assertTrue(method_exists(MonicaClient::class, 'generateImageSimple'));
        $this->assertTrue(method_exists(MonicaClient::class, 'getSupportedImageModels'));
        $this->assertTrue(method_exists(MonicaClient::class, 'isImageModelSupported'));
        
        // Test static methods
        $supportedModels = MonicaClient::getSupportedImageModels();
        $this->assertIsArray($supportedModels);
        $this->assertNotEmpty($supportedModels);
    }

    /**
     * Test parameter validation and limits
     */
    public function testParameterValidation(): void
    {
        $imageGen = new ImageGeneration('flux_dev', 'Test');
        
        // Test num_outputs limits (1-4)
        $imageGen->setNumOutputs(0);
        $array = $imageGen->toArray();
        $this->assertEquals(1, $array['num_outputs']); // Should be clamped to 1
        
        $imageGen->setNumOutputs(10);
        $array = $imageGen->toArray();
        $this->assertEquals(4, $array['num_outputs']); // Should be clamped to 4
        
        // Test safety tolerance limits (1-5)
        $imageGen->setSafetyTolerance(0);
        $array = $imageGen->toArray();
        $this->assertEquals(1, $array['safety_tolerance']); // Should be clamped to 1
        
        $imageGen->setSafetyTolerance(10);
        $array = $imageGen->toArray();
        $this->assertEquals(5, $array['safety_tolerance']); // Should be clamped to 5
    }

    /**
     * Test response string representation
     */
    public function testResponseStringRepresentation(): void
    {
        // Test with no images
        $emptyResponse = new ImageGenerationResponse(['data' => []]);
        $this->assertEquals('ImageGenerationResponse: No images generated', (string)$emptyResponse);
        
        // Test with one image
        $singleResponse = new ImageGenerationResponse([
            'data' => [['url' => 'https://example.com/image.png']]
        ]);
        $this->assertEquals('ImageGenerationResponse: 1 image generated - https://example.com/image.png', (string)$singleResponse);
        
        // Test with multiple images
        $multiResponse = new ImageGenerationResponse([
            'data' => [
                ['url' => 'https://example.com/image1.png'],
                ['url' => 'https://example.com/image2.png']
            ]
        ]);
        $this->assertEquals('ImageGenerationResponse: 2 images generated', (string)$multiResponse);
    }
}

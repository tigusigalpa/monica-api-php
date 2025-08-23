<?php

declare(strict_types=1);

/**
 * Monica Client Test
 *
 * This file contains unit tests for the MonicaClient class.
 *
 * @package   MonicaApi\Tests
 * @author    Igor Sazonov <sovletig@gmail.com>
 * @copyright 2025 Igor Sazonov
 * @license   MIT License
 * @link      https://github.com/tigusigalpa/monica-api-php
 */

namespace MonicaApi\Tests;

use MonicaApi\Exceptions\InvalidModelException;
use MonicaApi\MonicaClient;
use PHPUnit\Framework\TestCase;

/**
 * Monica Client Test Class
 *
 * Unit tests for the MonicaClient class functionality.
 *
 * @package MonicaApi\Tests
 * @author  Igor Sazonov <sovletig@gmail.com>
 * @since   1.0.0
 */
class MonicaClientTest extends TestCase
{
    /**
     * Test that MonicaClient can be instantiated with valid parameters
     */
    public function testCanInstantiateWithValidModel(): void
    {
        $client = new MonicaClient('test-api-key', 'gpt-4.1');
        
        $this->assertInstanceOf(MonicaClient::class, $client);
        $this->assertEquals('gpt-4.1', $client->getModel());
    }

    /**
     * Test that MonicaClient throws exception with invalid model
     */
    public function testThrowsExceptionWithInvalidModel(): void
    {
        $this->expectException(InvalidModelException::class);
        $this->expectExceptionMessage("Model 'invalid-model' is not supported");
        
        new MonicaClient('test-api-key', 'invalid-model');
    }

    /**
     * Test that model can be changed after instantiation
     */
    public function testCanChangeModel(): void
    {
        $client = new MonicaClient('test-api-key', 'gpt-4.1');
        
        $client->setModel('claude-3-haiku');
        $this->assertEquals('claude-3-haiku', $client->getModel());
    }

    /**
     * Test that changing to invalid model throws exception
     */
    public function testThrowsExceptionWhenChangingToInvalidModel(): void
    {
        $client = new MonicaClient('test-api-key', 'gpt-4.1');
        
        $this->expectException(InvalidModelException::class);
        $client->setModel('invalid-model');
    }

    /**
     * Test that isModelSupported works correctly
     */
    public function testIsModelSupportedWorksCorrectly(): void
    {
        $client = new MonicaClient('test-api-key', 'gpt-4.1');
        
        $this->assertTrue($client->isModelSupported('gpt-4.1'));
        $this->assertTrue($client->isModelSupported('claude-3-haiku'));
        $this->assertFalse($client->isModelSupported('invalid-model'));
    }

    /**
     * Test that getSupportedModels returns expected structure
     */
    public function testGetSupportedModelsReturnsExpectedStructure(): void
    {
        $models = MonicaClient::getSupportedModels();
        
        $this->assertIsArray($models);
        $this->assertArrayHasKey('OpenAI', $models);
        $this->assertArrayHasKey('Anthropic', $models);
        $this->assertArrayHasKey('Google', $models);
        
        // Test that OpenAI models contain expected entries
        $this->assertArrayHasKey('gpt-4.1', $models['OpenAI']);
        $this->assertArrayHasKey('gpt-4o', $models['OpenAI']);
    }

    /**
     * Test that getModelsByProvider works correctly
     */
    public function testGetModelsByProviderWorksCorrectly(): void
    {
        $openaiModels = MonicaClient::getModelsByProvider('OpenAI');
        
        $this->assertIsArray($openaiModels);
        $this->assertArrayHasKey('gpt-4.1', $openaiModels);
        $this->assertEquals('GPT-4.1 (Main model)', $openaiModels['gpt-4.1']);
        
        // Test non-existent provider
        $nonExistentModels = MonicaClient::getModelsByProvider('NonExistent');
        $this->assertEmpty($nonExistentModels);
    }

    /**
     * Test that getAllModelIds returns flat array of model IDs
     */
    public function testGetAllModelIdsReturnsFlatArray(): void
    {
        $modelIds = MonicaClient::getAllModelIds();
        
        $this->assertIsArray($modelIds);
        $this->assertContains('gpt-4.1', $modelIds);
        $this->assertContains('claude-3-haiku', $modelIds);
        $this->assertContains('gemini-2.5-pro', $modelIds);
        
        // Ensure all values are strings (model IDs)
        foreach ($modelIds as $modelId) {
            $this->assertIsString($modelId);
        }
    }
}

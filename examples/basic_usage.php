<?php

/**
 * Monica API PHP Client - Basic Usage Examples
 *
 * This file demonstrates basic usage of the Monica API PHP Client.
 * Make sure to install dependencies with: composer install
 *
 * @package   MonicaApi\Examples
 * @author    Igor Sazonov <sovletig@gmail.com>
 * @copyright 2025 Igor Sazonov
 * @license   MIT License
 * @link      https://github.com/tigusigalpa/monica-api-php
 */

require_once __DIR__ . '/../vendor/autoload.php';

use MonicaApi\MonicaClient;
use MonicaApi\Models\ChatMessage;
use MonicaApi\Exceptions\MonicaApiException;
use MonicaApi\Exceptions\InvalidModelException;

// Replace with your actual Monica API key
$apiKey = 'your-monica-api-key-here';

echo "=== Monica API PHP Client Examples ===\n\n";

try {
    // Example 1: Basic Chat Completion
    echo "1. Basic Chat Completion\n";
    echo "------------------------\n";
    
    $client = new MonicaClient($apiKey, 'gpt-4.1');
    $response = $client->chat('Hello! Can you explain what Monica API is?');
    
    echo "Response: " . $response->getContent() . "\n";
    echo "Tokens used: " . $response->getTotalTokens() . "\n\n";

    // Example 2: Chat with System Message and Parameters
    echo "2. Chat with System Message and Parameters\n";
    echo "------------------------------------------\n";
    
    $response = $client->chat('Write a short poem about programming', [
        'system' => 'You are a creative poet who loves technology',
        'temperature' => 0.8,
        'max_tokens' => 200
    ]);
    
    echo "Response: " . $response->getContent() . "\n";
    echo "Finish reason: " . $response->getFinishReason() . "\n\n";

    // Example 3: Conversation with History
    echo "3. Conversation with History\n";
    echo "----------------------------\n";
    
    $messages = [
        ChatMessage::system('You are a helpful programming assistant'),
        ChatMessage::user('What is PHP?'),
        ChatMessage::assistant('PHP is a popular server-side scripting language designed for web development.'),
        ChatMessage::user('Can you show me a simple PHP function?')
    ];
    
    $response = $client->chatWithHistory($messages, [
        'temperature' => 0.3,
        'max_tokens' => 300
    ]);
    
    echo "Response: " . $response->getContent() . "\n\n";

    // Example 4: Working with Different Models
    echo "4. Working with Different Models\n";
    echo "--------------------------------\n";
    
    // List all supported models
    $models = MonicaClient::getSupportedModels();
    echo "Available providers:\n";
    foreach ($models as $provider => $providerModels) {
        echo "- {$provider}: " . count($providerModels) . " models\n";
    }
    echo "\n";
    
    // Switch to a different model
    if ($client->isModelSupported('claude-3-haiku')) {
        $client->setModel('claude-3-haiku');
        echo "Switched to model: " . $client->getModel() . "\n";
        
        $response = $client->chat('Tell me a fun fact about AI');
        echo "Response: " . $response->getContent() . "\n\n";
    }

    // Example 5: Response Analysis
    echo "5. Response Analysis\n";
    echo "--------------------\n";
    
    $response = $client->chat('Explain quantum computing in one sentence');
    
    echo "Content: " . $response->getContent() . "\n";
    echo "Model used: " . $response->getModel() . "\n";
    echo "Prompt tokens: " . $response->getPromptTokens() . "\n";
    echo "Completion tokens: " . $response->getCompletionTokens() . "\n";
    echo "Total tokens: " . $response->getTotalTokens() . "\n";
    echo "Is complete: " . ($response->isComplete() ? 'Yes' : 'No') . "\n";
    echo "Was truncated: " . ($response->wasTruncated() ? 'Yes' : 'No') . "\n\n";

} catch (InvalidModelException $e) {
    echo "Model Error: " . $e->getUserFriendlyMessage() . "\n";
    
    $suggestions = $e->getSuggestions();
    if (!empty($suggestions)) {
        echo "Suggestions: " . implode(', ', $suggestions) . "\n";
    }
    
} catch (MonicaApiException $e) {
    echo "API Error: " . $e->getUserFriendlyMessage() . "\n";
    
    if ($e->isAuthenticationError()) {
        echo "Please check your API key configuration.\n";
    } elseif ($e->isRateLimitError()) {
        echo "Please wait before making more requests.\n";
    } elseif ($e->isQuotaError()) {
        echo "Please check your billing and usage limits.\n";
    }
    
} catch (Exception $e) {
    echo "Unexpected Error: " . $e->getMessage() . "\n";
}

echo "=== Examples completed ===\n";

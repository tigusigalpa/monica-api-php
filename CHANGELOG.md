# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.1.0] - 2025-01-16

### Added

- **Image Generation Support**: Complete integration with Monica API image generation endpoints
- Support for FLUX models (Schnell, Dev, Pro) with customizable parameters
- Support for Stable Diffusion models (XL 1.0, SD3, SD3.5 Large) with advanced controls
- Support for DALL·E 3 with quality and style options
- Support for Playground V2.5 with artistic style interpretation
- Support for Ideogram V2 with exceptional text rendering capabilities
- `ImageGeneration` model class with fluent interface for request building
- `ImageGenerationResponse` model class with image download and save functionality
- Comprehensive image generation examples and documentation
- Unit tests for all image generation functionality

### Enhanced

- Extended `MonicaClient` with `generateImage()` and `generateImageSimple()` methods
- Added model-specific parameter mapping and validation
- Updated README with detailed image generation usage examples
- Added API reference documentation for new classes

## [1.0.0] - 2025-01-16

### Added

- Initial release of Monica API PHP Client
- Support for multiple AI providers (OpenAI, Anthropic, Google, DeepSeek, Meta, Grok, NVIDIA, Mistral)
- Comprehensive PHPDoc documentation
- Type-safe PHP 8.1+ implementation
- Robust error handling with detailed exceptions
- Laravel integration support
- Unit tests with PHPUnit
- Complete README with examples and API reference

### Features

- `MonicaClient` - Main client class for API interactions
- `ChatMessage` - Message model with role-based creation methods
- `ChatCompletion` - Request model with parameter validation
- `ChatCompletionResponse` - Response model with usage statistics
- `MonicaApiException` - Comprehensive API error handling
- `InvalidModelException` - Model validation with suggestions
- `HttpClient` - HTTP communication layer with Guzzle

### Supported Models

- **OpenAI**: GPT-4.1 (Mini/Nano), GPT-4o (Mini), o4-mini
- **Anthropic**: Claude 4 (Opus/Sonnet), Claude 3 Haiku
- **Google**: Gemini 2.5 (Pro/Flash/Flash-Lite), Gemini 2.0 Flash
- **DeepSeek**: V3 Reasoner, V3 Chat
- **Meta**: Llama 3/3.1 8B Instruct
- **Grok**: Grok 3 Beta, Grok Beta
- **NVIDIA**: Llama 3.1 Nemotron 70B
- **Mistral**: Mistral 7B Instruct

## [1.0.0] - 2025-01-16

### Added

- Initial stable release
- Full Monica API Platform integration
- Complete documentation and examples
- Production-ready codebase with comprehensive error handling

<?php

declare(strict_types = 1);

/**
 * Image Generation Request Model
 *
 * This file contains the ImageGeneration class for handling image generation requests
 * across different AI image models through Monica API Platform.
 *
 * @package   Tigusigalpa\MonicaApi\Models
 * @author    Igor Sazonov <sovletig@gmail.com>
 * @copyright 2025 Igor Sazonov
 * @license   MIT License
 * @link      https://github.com/tigusigalpa/monica-api-php
 */

namespace Tigusigalpa\MonicaApi\Models;

/**
 * Image Generation Request Model
 *
 * Represents an image generation request with unified parameters
 * that work across different image generation models.
 *
 * @package Tigusigalpa\MonicaApi\Models
 * @author  Igor Sazonov <sovletig@gmail.com>
 * @since   1.1.0
 */
class ImageGeneration
{
    /**
     * Supported image generation models
     */
    public const SUPPORTED_MODELS = [
        // FLUX models
        'flux_schnell' => 'FLUX.1 Schnell',
        'flux_dev' => 'FLUX.1 Dev',
        'flux_pro' => 'FLUX.1 Pro',
        
        // Stable Diffusion models
        'sdxl_1_0' => 'Stable Diffusion XL 1.0',
        'sd3' => 'Stable Diffusion 3',
        'sd3_5' => 'Stable Diffusion 3.5 Large',
        
        // DALL·E models
        'dall-e-3' => 'DALL·E 3',
        
        // Playground models
        'playground-v2-5' => 'Playground V2.5',
        
        // Ideogram models
        'V_2' => 'Ideogram V2',
    ];

    /**
     * Supported image sizes
     */
    public const SUPPORTED_SIZES = [
        '256x256', '512x512', '1024x1024', '1024x1792', '1792x1024'
    ];

    /**
     * Supported aspect ratios for Ideogram
     */
    public const IDEOGRAM_ASPECT_RATIOS = [
        'ASPECT_10_16', 'ASPECT_16_10', 'ASPECT_9_16', 'ASPECT_16_9',
        'ASPECT_4_3', 'ASPECT_3_4', 'ASPECT_1_1', 'ASPECT_3_2', 'ASPECT_2_3'
    ];

    /**
     * AI model to use for image generation
     */
    private string $model;

    /**
     * Text prompt describing the desired image
     */
    private string $prompt;

    /**
     * Negative prompt (what to avoid in the image)
     */
    private ?string $negativePrompt = null;

    /**
     * Number of images to generate
     */
    private int $numOutputs = 1;

    /**
     * Image size (width x height)
     */
    private string $size = '1024x1024';

    /**
     * Random seed for reproducible results
     */
    private ?int $seed = null;

    /**
     * Number of inference steps
     */
    private ?int $steps = null;

    /**
     * Guidance scale for generation
     */
    private ?float $guidance = null;

    /**
     * CFG scale for Stable Diffusion
     */
    private ?float $cfgScale = null;

    /**
     * Image quality (for DALL·E)
     */
    private ?string $quality = null;

    /**
     * Image style (for DALL·E)
     */
    private ?string $style = null;

    /**
     * Aspect ratio (for Ideogram)
     */
    private ?string $aspectRatio = null;

    /**
     * Magic prompt option (for Ideogram)
     */
    private ?string $magicPromptOption = null;

    /**
     * Style type (for Ideogram)
     */
    private ?string $styleType = null;

    /**
     * Safety tolerance (for FLUX)
     */
    private ?int $safetyTolerance = null;

    /**
     * Constructor
     *
     * @param string $model AI model to use for image generation
     * @param string $prompt Text prompt describing the desired image
     */
    public function __construct(string $model, string $prompt)
    {
        $this->model = $model;
        $this->prompt = $prompt;
    }

    /**
     * Set negative prompt
     *
     * @param string $negativePrompt What to avoid in the image
     * @return self
     */
    public function setNegativePrompt(string $negativePrompt): self
    {
        $this->negativePrompt = $negativePrompt;
        return $this;
    }

    /**
     * Set number of images to generate
     *
     * @param int $numOutputs Number of images (1-4)
     * @return self
     */
    public function setNumOutputs(int $numOutputs): self
    {
        $this->numOutputs = max(1, min(4, $numOutputs));
        return $this;
    }

    /**
     * Set image size
     *
     * @param string $size Image size in format "widthxheight"
     * @return self
     */
    public function setSize(string $size): self
    {
        $this->size = $size;
        return $this;
    }

    /**
     * Set random seed
     *
     * @param int $seed Random seed for reproducible results
     * @return self
     */
    public function setSeed(int $seed): self
    {
        $this->seed = $seed;
        return $this;
    }

    /**
     * Set number of inference steps
     *
     * @param int $steps Number of steps (more steps = higher quality, slower)
     * @return self
     */
    public function setSteps(int $steps): self
    {
        $this->steps = $steps;
        return $this;
    }

    /**
     * Set guidance scale
     *
     * @param float $guidance How closely to follow the prompt (1.0-20.0)
     * @return self
     */
    public function setGuidance(float $guidance): self
    {
        $this->guidance = $guidance;
        return $this;
    }

    /**
     * Set CFG scale (for Stable Diffusion)
     *
     * @param float $cfgScale Classifier-free guidance scale
     * @return self
     */
    public function setCfgScale(float $cfgScale): self
    {
        $this->cfgScale = $cfgScale;
        return $this;
    }

    /**
     * Set image quality (for DALL·E)
     *
     * @param string $quality "standard" or "hd"
     * @return self
     */
    public function setQuality(string $quality): self
    {
        $this->quality = $quality;
        return $this;
    }

    /**
     * Set image style (for DALL·E)
     *
     * @param string $style "vivid" or "natural"
     * @return self
     */
    public function setStyle(string $style): self
    {
        $this->style = $style;
        return $this;
    }

    /**
     * Set aspect ratio (for Ideogram)
     *
     * @param string $aspectRatio One of the IDEOGRAM_ASPECT_RATIOS constants
     * @return self
     */
    public function setAspectRatio(string $aspectRatio): self
    {
        $this->aspectRatio = $aspectRatio;
        return $this;
    }

    /**
     * Set magic prompt option (for Ideogram)
     *
     * @param string $magicPromptOption "AUTO", "ON", or "OFF"
     * @return self
     */
    public function setMagicPromptOption(string $magicPromptOption): self
    {
        $this->magicPromptOption = $magicPromptOption;
        return $this;
    }

    /**
     * Set style type (for Ideogram)
     *
     * @param string $styleType Style type for the generated image
     * @return self
     */
    public function setStyleType(string $styleType): self
    {
        $this->styleType = $styleType;
        return $this;
    }

    /**
     * Set safety tolerance (for FLUX)
     *
     * @param int $safetyTolerance Safety tolerance level (1-5)
     * @return self
     */
    public function setSafetyTolerance(int $safetyTolerance): self
    {
        $this->safetyTolerance = max(1, min(5, $safetyTolerance));
        return $this;
    }

    /**
     * Get the model
     *
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Get the prompt
     *
     * @return string
     */
    public function getPrompt(): string
    {
        return $this->prompt;
    }

    /**
     * Convert to array for API request
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        $data = [
            'model' => $this->model,
            'prompt' => $this->prompt,
        ];

        // Add optional parameters based on model type
        if ($this->negativePrompt !== null) {
            $data['negative_prompt'] = $this->negativePrompt;
        }

        // Model-specific parameter mapping
        if (str_starts_with($this->model, 'flux_')) {
            // FLUX models
            $data['num_outputs'] = $this->numOutputs;
            $data['size'] = $this->size;
            if ($this->seed !== null) $data['seed'] = $this->seed;
            if ($this->steps !== null) $data['steps'] = $this->steps;
            if ($this->guidance !== null) $data['guidance'] = (string)$this->guidance;
            if ($this->safetyTolerance !== null) $data['safety_tolerance'] = $this->safetyTolerance;
            $data['interval'] = 2; // Default interval for FLUX
        } elseif (in_array($this->model, ['sdxl_1_0', 'sd3', 'sd3_5'])) {
            // Stable Diffusion models
            $data['num_outputs'] = $this->numOutputs;
            $data['size'] = $this->size;
            if ($this->seed !== null) $data['seed'] = (string)$this->seed;
            if ($this->steps !== null) $data['steps'] = $this->steps;
            if ($this->cfgScale !== null) $data['cfg_scale'] = (string)$this->cfgScale;
            $data['output_quality'] = 90; // Default quality
            $data['scheduler'] = 'K_EULER'; // Default scheduler
            $data['num_inference_steps'] = $this->steps ?? 50;
        } elseif ($this->model === 'dall-e-3') {
            // DALL·E models
            $data['n'] = $this->numOutputs;
            $data['size'] = $this->size;
            if ($this->quality !== null) $data['quality'] = $this->quality;
            if ($this->style !== null) $data['style'] = $this->style;
        } elseif ($this->model === 'playground-v2-5') {
            // Playground models
            $data['count'] = $this->numOutputs;
            $data['size'] = $this->size;
            if ($this->steps !== null) $data['step'] = $this->steps;
            if ($this->seed !== null) $data['seed'] = (string)$this->seed;
            if ($this->cfgScale !== null) $data['cfg_scale'] = (string)$this->cfgScale;
            $data['safety_check'] = false; // Default safety check
        } elseif ($this->model === 'V_2') {
            // Ideogram models
            if ($this->aspectRatio !== null) $data['aspect_ratio'] = $this->aspectRatio;
            if ($this->magicPromptOption !== null) $data['magic_prompt_option'] = $this->magicPromptOption;
            if ($this->seed !== null) $data['seed'] = $this->seed;
            if ($this->styleType !== null) $data['style_type'] = $this->styleType;
        }

        return $data;
    }

    /**
     * Get supported models
     *
     * @return array<string, string>
     */
    public static function getSupportedModels(): array
    {
        return self::SUPPORTED_MODELS;
    }

    /**
     * Check if model is supported
     *
     * @param string $model Model to check
     * @return bool
     */
    public static function isModelSupported(string $model): bool
    {
        return array_key_exists($model, self::SUPPORTED_MODELS);
    }
}

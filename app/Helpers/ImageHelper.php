<?php

namespace App\Helpers;

use App\Services\ImageProcessingService;

class ImageHelper
{
    protected static $imageService;

    /**
     * Get image processing service instance
     */
    protected static function getImageService(): ImageProcessingService
    {
        if (!self::$imageService) {
            self::$imageService = app(ImageProcessingService::class);
        }
        return self::$imageService;
    }

    /**
     * Generate optimized image URL with lazy loading attributes
     */
    public static function image($path, $alt = '', $attributes = [], $size = 'original', $webp = true)
    {
        $defaultAttributes = [
            'loading' => 'lazy',
            'decoding' => 'async',
            'alt' => $alt,
        ];

        $attributes = array_merge($defaultAttributes, $attributes);

        // Get optimized image URL
        $imageUrl = self::getImageService()->getImageUrl($path, $size, $webp);

        return [
            'src' => $imageUrl,
            'attributes' => $attributes,
        ];
    }

    /**
     * Generate image tag with lazy loading
     */
    public static function imageTag($path, $alt = '', $class = '', $attributes = [])
    {
        $image = self::image($path, $alt, $attributes);
        
        $attrString = '';
        foreach ($image['attributes'] as $key => $value) {
            $attrString .= " {$key}=\"" . htmlspecialchars($value) . "\"";
        }
        
        if ($class) {
            $attrString .= " class=\"" . htmlspecialchars($class) . "\"";
        }

        return "<img src=\"{$image['src']}\"{$attrString}>";
    }

    /**
     * Get image with fallback
     */
    public static function getImage($path, $fallback = 'images/default-news.jpg')
    {
        if ($path && file_exists(storage_path('app/public/' . $path))) {
            return asset('storage/' . $path);
        }

        return asset($fallback);
    }

    /**
     * Generate responsive image srcset with multiple sizes
     */
    public static function srcset($path, $sizes = null, $webp = true)
    {
        if ($sizes === null) {
            $sizes = ['thumbnail', 'medium', 'large'];
        }

        $srcset = [];
        $service = self::getImageService();

        foreach ($sizes as $size) {
            $url = $service->getImageUrl($path, $size, $webp);
            
            // Get width based on size name
            $width = match($size) {
                'thumbnail' => 300,
                'medium' => 800,
                'large' => 1200,
                default => 1200,
            };
            
            $srcset[] = $url . " {$width}w";
        }

        return implode(', ', $srcset);
    }

    /**
     * Process uploaded image (generate sizes + WebP)
     */
    public static function processImage($path, $generateSizes = true, $generateWebP = true)
    {
        return self::getImageService()->processUploadedImage($path, $generateSizes, $generateWebP);
    }

    /**
     * Delete image and all variants
     */
    public static function deleteImageVariants($path)
    {
        return self::getImageService()->deleteImageVariants($path);
    }
}


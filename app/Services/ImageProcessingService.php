<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class ImageProcessingService
{
    protected $driver;

    public function __construct()
    {
        // Detect available image library
        if (extension_loaded('imagick')) {
            $this->driver = 'imagick';
        } elseif (extension_loaded('gd')) {
            $this->driver = 'gd';
        } else {
            $this->driver = null;
            Log::warning('No image processing library available (GD or Imagick)');
        }
    }

    /**
     * Generate multiple image sizes using native PHP
     */
    public function generateSizes(string $imagePath, array $sizes = null): array
    {
        if (!$this->driver) {
            Log::warning('Cannot generate image sizes: No image library available');
            return [];
        }

        if ($sizes === null) {
            $sizes = [
                'thumbnail' => [300, 300],
                'medium' => [800, 600],
                'large' => [1200, 900],
            ];
        }

        $generated = [];
        $fullPath = storage_path('app/public/' . $imagePath);

        if (!file_exists($fullPath)) {
            Log::warning("Image not found: {$fullPath}");
            return $generated;
        }

        try {
            $pathInfo = pathinfo($imagePath);
            $directory = $pathInfo['dirname'];
            $filename = $pathInfo['filename'];
            $extension = strtolower($pathInfo['extension'] ?? 'jpg');

            // Load original image
            $originalImage = $this->loadImage($fullPath, $extension);
            if (!$originalImage) {
                return $generated;
            }

            $originalWidth = imagesx($originalImage);
            $originalHeight = imagesy($originalImage);

            foreach ($sizes as $sizeName => $dimensions) {
                [$targetWidth, $targetHeight] = $dimensions;
                
                // Calculate dimensions maintaining aspect ratio
                $ratio = min($targetWidth / $originalWidth, $targetHeight / $originalHeight);
                $newWidth = (int)($originalWidth * $ratio);
                $newHeight = (int)($originalHeight * $ratio);

                // Create resized image
                $resized = imagecreatetruecolor($targetWidth, $targetHeight);
                
                // Preserve transparency for PNG
                if ($extension === 'png') {
                    imagealphablending($resized, false);
                    imagesavealpha($resized, true);
                    $transparent = imagecolorallocatealpha($resized, 255, 255, 255, 127);
                    imagefilledrectangle($resized, 0, 0, $targetWidth, $targetHeight, $transparent);
                }

                // Resize and crop to center
                $x = ($targetWidth - $newWidth) / 2;
                $y = ($targetHeight - $newHeight) / 2;
                
                imagecopyresampled(
                    $resized, $originalImage,
                    $x, $y, 0, 0,
                    $newWidth, $newHeight,
                    $originalWidth, $originalHeight
                );

                // Generate new filename
                $newFilename = "{$filename}_{$sizeName}.{$extension}";
                $newPath = $directory . '/' . $newFilename;
                $newFullPath = storage_path('app/public/' . $newPath);

                // Ensure directory exists
                $newDirectory = dirname($newFullPath);
                if (!is_dir($newDirectory)) {
                    mkdir($newDirectory, 0755, true);
                }

                // Save resized image
                $this->saveImage($resized, $newFullPath, $extension, 85);
                $generated[$sizeName] = $newPath;

                imagedestroy($resized);
            }

            imagedestroy($originalImage);
            return $generated;
        } catch (\Exception $e) {
            Log::error("Failed to generate image sizes: " . $e->getMessage(), [
                'image_path' => $imagePath,
                'exception' => $e
            ]);
            return $generated;
        }
    }

    /**
     * Convert image to WebP format
     */
    public function convertToWebP(string $imagePath, int $quality = 80): ?string
    {
        if (!$this->driver) {
            return null;
        }

        if ($this->driver === 'gd' && !function_exists('imagewebp')) {
            Log::warning('WebP conversion not supported (GD without WebP support)');
            return null;
        }

        $fullPath = storage_path('app/public/' . $imagePath);

        if (!file_exists($fullPath)) {
            Log::warning("Image not found: {$fullPath}");
            return null;
        }

        try {
            $pathInfo = pathinfo($imagePath);
            $extension = strtolower($pathInfo['extension'] ?? 'jpg');
            
            // Load original image
            $image = $this->loadImage($fullPath, $extension);
            if (!$image) {
                return null;
            }

            $webpPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
            $webpFullPath = storage_path('app/public/' . $webpPath);

            // Ensure directory exists
            $directory = dirname($webpFullPath);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            // Save as WebP
            if ($this->driver === 'imagick') {
                $imagick = new \Imagick($fullPath);
                $imagick->setImageFormat('webp');
                $imagick->setImageCompressionQuality($quality);
                $imagick->writeImage($webpFullPath);
                $imagick->destroy();
            } else {
                imagewebp($image, $webpFullPath, $quality);
            }

            imagedestroy($image);
            return $webpPath;
        } catch (\Exception $e) {
            Log::error("Failed to convert image to WebP: " . $e->getMessage(), [
                'image_path' => $imagePath,
                'exception' => $e
            ]);
            return null;
        }
    }

    /**
     * Optimize image (compress without resizing)
     */
    public function optimize(string $imagePath, int $quality = 85): bool
    {
        if (!$this->driver) {
            return false;
        }

        $fullPath = storage_path('app/public/' . $imagePath);

        if (!file_exists($fullPath)) {
            return false;
        }

        try {
            $pathInfo = pathinfo($imagePath);
            $extension = strtolower($pathInfo['extension'] ?? 'jpg');
            
            $image = $this->loadImage($fullPath, $extension);
            if (!$image) {
                return false;
            }

            $this->saveImage($image, $fullPath, $extension, $quality);
            imagedestroy($image);
            
            return true;
        } catch (\Exception $e) {
            Log::error("Failed to optimize image: " . $e->getMessage(), [
                'image_path' => $imagePath,
                'exception' => $e
            ]);
            return false;
        }
    }

    /**
     * Load image from file
     */
    private function loadImage(string $path, string $extension)
    {
        if (!file_exists($path)) {
            Log::warning("Image file not found: {$path}");
            return false;
        }

        try {
            switch (strtolower($extension)) {
                case 'jpg':
                case 'jpeg':
                    $image = @imagecreatefromjpeg($path);
                    break;
                case 'png':
                    $image = @imagecreatefrompng($path);
                    break;
                case 'gif':
                    $image = @imagecreatefromgif($path);
                    break;
                case 'webp':
                    if (function_exists('imagecreatefromwebp')) {
                        $image = @imagecreatefromwebp($path);
                    } else {
                        Log::warning("WebP support not available");
                        return false;
                    }
                    break;
                default:
                    Log::warning("Unsupported image format: {$extension}");
                    return false;
            }

            if (!$image) {
                Log::warning("Failed to load image: {$path}");
                return false;
            }

            return $image;
        } catch (\Exception $e) {
            Log::error("Error loading image: " . $e->getMessage(), [
                'path' => $path,
                'extension' => $extension
            ]);
            return false;
        }
    }

    /**
     * Save image to file
     */
    private function saveImage($image, string $path, string $extension, int $quality): bool
    {
        if (!$image) {
            return false;
        }

        try {
            // Ensure directory exists
            $directory = dirname($path);
            if (!is_dir($directory)) {
                mkdir($directory, 0755, true);
            }

            $result = false;
            switch (strtolower($extension)) {
                case 'jpg':
                case 'jpeg':
                    $result = @imagejpeg($image, $path, $quality);
                    break;
                case 'png':
                    // PNG quality is 0-9, convert from 0-100
                    $pngQuality = (int)(9 - ($quality / 100) * 9);
                    $result = @imagepng($image, $path, $pngQuality);
                    break;
                case 'gif':
                    $result = @imagegif($image, $path);
                    break;
                case 'webp':
                    if (function_exists('imagewebp')) {
                        $result = @imagewebp($image, $path, $quality);
                    } else {
                        Log::warning("WebP support not available for saving");
                        return false;
                    }
                    break;
                default:
                    Log::warning("Unsupported image format for saving: {$extension}");
                    return false;
            }

            if (!$result) {
                Log::warning("Failed to save image: {$path}");
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Error saving image: " . $e->getMessage(), [
                'path' => $path,
                'extension' => $extension
            ]);
            return false;
        }
    }

    /**
     * Get image URL with size
     */
    public function getImageUrl(string $imagePath, string $size = 'original', bool $webp = true): string
    {
        if (empty($imagePath)) {
            return asset('images/default-news.jpg');
        }

        if ($size === 'original') {
            // Try WebP first if requested
            if ($webp) {
                $webpPath = $this->getWebPPath($imagePath);
                if ($webpPath && Storage::disk('public')->exists($webpPath)) {
                    return asset('storage/' . $webpPath);
                }
            }
            return asset('storage/' . $imagePath);
        }

        // Get sized image
        $pathInfo = pathinfo($imagePath);
        $sizedPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_' . $size . '.' . ($pathInfo['extension'] ?? 'jpg');

        // Try WebP first if requested
        if ($webp) {
            $webpSizedPath = $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '_' . $size . '.webp';
            if (Storage::disk('public')->exists($webpSizedPath)) {
                return asset('storage/' . $webpSizedPath);
            }
        }

        if (Storage::disk('public')->exists($sizedPath)) {
            return asset('storage/' . $sizedPath);
        }

        // Fallback to original
        return asset('storage/' . $imagePath);
    }

    /**
     * Get WebP path for an image
     */
    private function getWebPPath(string $imagePath): string
    {
        $pathInfo = pathinfo($imagePath);
        return $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '.webp';
    }

    /**
     * Process uploaded image (generate sizes + WebP)
     */
    public function processUploadedImage(string $imagePath, bool $generateSizes = true, bool $generateWebP = true): array
    {
        $results = [
            'original' => $imagePath,
            'sizes' => [],
            'webp' => null,
            'optimized' => false,
            'success' => false,
        ];

        if (empty($imagePath)) {
            Log::warning('Empty image path provided for processing');
            return $results;
        }

        $fullPath = storage_path('app/public/' . $imagePath);
        if (!file_exists($fullPath)) {
            Log::warning("Image file not found for processing: {$fullPath}");
            return $results;
        }

        if (!$this->driver) {
            Log::warning('Image processing skipped: No image library available');
            $results['success'] = true; // Mark as success even without processing
            return $results;
        }

        try {
            // Optimize original
            if ($this->optimize($imagePath)) {
                $results['optimized'] = true;
            }

            // Generate sizes
            if ($generateSizes) {
                $results['sizes'] = $this->generateSizes($imagePath);
            }

            // Generate WebP
            if ($generateWebP) {
                $results['webp'] = $this->convertToWebP($imagePath);
            }

            $results['success'] = true;
        } catch (\Exception $e) {
            Log::error("Failed to process uploaded image: " . $e->getMessage(), [
                'image_path' => $imagePath,
                'exception' => $e
            ]);
        }

        return $results;
    }

    /**
     * Delete image and all its variants
     */
    public function deleteImageVariants(string $imagePath): bool
    {
        if (empty($imagePath)) {
            return false;
        }

        try {
            $pathInfo = pathinfo($imagePath);
            $directory = $pathInfo['dirname'];
            $filename = $pathInfo['filename'];
            $extension = $pathInfo['extension'] ?? 'jpg';

            $variants = [
                $imagePath, // Original
                $directory . '/' . $filename . '.webp', // WebP original
                $directory . '/' . $filename . '_thumbnail.' . $extension,
                $directory . '/' . $filename . '_thumbnail.webp',
                $directory . '/' . $filename . '_medium.' . $extension,
                $directory . '/' . $filename . '_medium.webp',
                $directory . '/' . $filename . '_large.' . $extension,
                $directory . '/' . $filename . '_large.webp',
            ];

            foreach ($variants as $variant) {
                if (Storage::disk('public')->exists($variant)) {
                    Storage::disk('public')->delete($variant);
                }
            }

            return true;
        } catch (\Exception $e) {
            Log::error("Failed to delete image variants: " . $e->getMessage(), [
                'image_path' => $imagePath,
                'exception' => $e
            ]);
            return false;
        }
    }
}

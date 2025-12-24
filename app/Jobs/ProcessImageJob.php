<?php

namespace App\Jobs;

use App\Services\ImageProcessingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 300; // 5 minutes
    public $backoff = [60, 120, 300]; // Retry after 1min, 2min, 5min

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $imagePath,
        public bool $generateSizes = true,
        public bool $generateWebP = true
    ) {
        // Set queue name
        $this->onQueue('images');
    }

    /**
     * Execute the job.
     */
    public function handle(ImageProcessingService $imageService): void
    {
        try {
            Log::info("Processing image job started: {$this->imagePath}");

            $results = $imageService->processUploadedImage(
                $this->imagePath,
                $this->generateSizes,
                $this->generateWebP
            );

            if ($results['success']) {
                Log::info("Image processing completed successfully", [
                    'image_path' => $this->imagePath,
                    'sizes_generated' => count($results['sizes']),
                    'webp_created' => !empty($results['webp']),
                ]);
            } else {
                Log::warning("Image processing completed with warnings", [
                    'image_path' => $this->imagePath,
                    'results' => $results,
                ]);
            }
        } catch (\Exception $e) {
            Log::error("Image processing job failed: " . $e->getMessage(), [
                'image_path' => $this->imagePath,
                'exception' => $e,
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw to trigger retry mechanism
            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Image processing job failed permanently: " . $exception->getMessage(), [
            'image_path' => $this->imagePath,
            'exception' => $exception,
        ]);
    }
}


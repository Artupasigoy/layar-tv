<?php

namespace App\Jobs;

use App\Models\Media;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProcessImageJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 120; // 2 minutes

    protected Media $media;

    /**
     * Create a new job instance.
     */
    public function __construct(Media $media)
    {
        $this->media = $media;
    }

    /**
     * Execute the job.
     * 
     * Image processing for digital signage:
     * - Display version: max 1920px width
     * - Thumbnail: 320px width
     * - Optimized quality
     */
    public function handle(): void
    {
        $media = $this->media;

        // Update status to processing
        $media->update(['processing_status' => 'processing']);

        try {
            $inputPath = Storage::disk('public')->path($media->file_path);
            
            // Generate output filenames with hash for cache busting
            $baseName = pathinfo($media->file_path, PATHINFO_FILENAME);
            $hash = substr(md5($media->id . time()), 0, 8);
            
            // Ensure directories exist
            Storage::disk('public')->makeDirectory('media/display');
            Storage::disk('public')->makeDirectory('media/thumbnails');

            // Process with GD (built-in, no external dependencies)
            $displayFilename = "media/display/{$baseName}_{$hash}.jpg";
            $thumbnailFilename = "media/thumbnails/{$baseName}_{$hash}.jpg";

            $displayPath = Storage::disk('public')->path($displayFilename);
            $thumbnailPath = Storage::disk('public')->path($thumbnailFilename);

            // Get original image info
            $imageInfo = getimagesize($inputPath);
            if (!$imageInfo) {
                throw new \Exception("Cannot read image: {$inputPath}");
            }

            [$origWidth, $origHeight, $imageType] = $imageInfo;

            // Create image resource based on type
            $sourceImage = $this->createImageFromFile($inputPath, $imageType);
            if (!$sourceImage) {
                throw new \Exception("Cannot create image resource");
            }

            // Create display version (max 1920px width)
            $displayWidth = min($origWidth, 1920);
            $displayHeight = (int) ($origHeight * ($displayWidth / $origWidth));
            
            $displayImage = imagecreatetruecolor($displayWidth, $displayHeight);
            imagecopyresampled($displayImage, $sourceImage, 0, 0, 0, 0, $displayWidth, $displayHeight, $origWidth, $origHeight);
            imagejpeg($displayImage, $displayPath, 85);
            imagedestroy($displayImage);

            // Create thumbnail (320px width)
            $thumbWidth = 320;
            $thumbHeight = (int) ($origHeight * ($thumbWidth / $origWidth));
            
            $thumbImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
            imagecopyresampled($thumbImage, $sourceImage, 0, 0, 0, 0, $thumbWidth, $thumbHeight, $origWidth, $origHeight);
            imagejpeg($thumbImage, $thumbnailPath, 80);
            imagedestroy($thumbImage);

            // Clean up source image
            imagedestroy($sourceImage);

            // Update media record
            $media->update([
                'display_path' => $displayFilename,
                'thumbnail_path' => $thumbnailFilename,
                'processing_status' => 'completed',
            ]);

            Log::info("Image processed successfully: {$media->id}");

            // Trigger playlist regeneration
            GeneratePlaylistJob::dispatch();

        } catch (\Exception $e) {
            Log::error("Image processing failed: " . $e->getMessage());
            
            $media->update([
                'processing_status' => 'failed',
            ]);

            throw $e;
        }
    }

    /**
     * Create image resource from file based on type
     */
    protected function createImageFromFile(string $path, int $type)
    {
        switch ($type) {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($path);
            case IMAGETYPE_WEBP:
                return imagecreatefromwebp($path);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($path);
            default:
                return null;
        }
    }
}

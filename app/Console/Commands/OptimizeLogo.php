<?php

namespace App\Console\Commands;

use App\Models\Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class OptimizeLogo extends Command
{
    protected $signature = 'logo:optimize';
    protected $description = 'Resize existing logo to optimize file size';

    public function handle()
    {
        $settings = Setting::first();
        
        if (!$settings->logo_path) {
            $this->info('No logo found.');
            return 0;
        }

        $logoPath = Storage::disk('public')->path($settings->logo_path);
        
        if (!file_exists($logoPath)) {
            $this->error('Logo file not found: ' . $logoPath);
            return 1;
        }

        // Get original file size
        $originalSize = filesize($logoPath);
        $this->info("Original logo size: " . number_format($originalSize / 1024, 2) . " KB");

        // Get image info
        $imageInfo = getimagesize($logoPath);
        if (!$imageInfo) {
            $this->error('Cannot read image.');
            return 1;
        }

        [$origWidth, $origHeight, $imageType] = $imageInfo;
        $this->info("Original dimensions: {$origWidth}x{$origHeight}");

        // Create source image (suppress warnings about incorrect sRGB profiles)
        $sourceImage = match ($imageType) {
            IMAGETYPE_JPEG => @imagecreatefromjpeg($logoPath),
            IMAGETYPE_PNG => @imagecreatefrompng($logoPath),
            IMAGETYPE_WEBP => @imagecreatefromwebp($logoPath),
            IMAGETYPE_GIF => @imagecreatefromgif($logoPath),
            default => null,
        };

        if (!$sourceImage) {
            $this->error('Cannot create image resource.');
            return 1;
        }

        // Check if resize needed
        $maxWidth = 200;
        if ($origWidth <= $maxWidth) {
            $this->info("Logo is already optimized (width <= {$maxWidth}px).");
            imagedestroy($sourceImage);
            return 0;
        }

        // Calculate new dimensions
        $newWidth = $maxWidth;
        $newHeight = (int) ($origHeight * ($newWidth / $origWidth));

        // Create resized image
        $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preserve transparency for PNG
        if ($imageType === IMAGETYPE_PNG) {
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 0, 0, 0, 127);
            imagefilledrectangle($resizedImage, 0, 0, $newWidth, $newHeight, $transparent);
        }

        imagecopyresampled($resizedImage, $sourceImage, 0, 0, 0, 0, $newWidth, $newHeight, $origWidth, $origHeight);

        // Generate new filename
        $newFilename = 'logos/' . uniqid('optimized_') . '.png';
        $newPath = Storage::disk('public')->path($newFilename);

        // Save as PNG
        imagepng($resizedImage, $newPath, 8);

        // Cleanup
        imagedestroy($sourceImage);
        imagedestroy($resizedImage);

        // Get new file size
        $newSize = filesize($newPath);
        $this->info("New dimensions: {$newWidth}x{$newHeight}");
        $this->info("New logo size: " . number_format($newSize / 1024, 2) . " KB");
        $this->info("Reduction: " . number_format(100 - ($newSize / $originalSize * 100), 1) . "%");

        // Delete old logo
        Storage::disk('public')->delete($settings->logo_path);

        // Update settings
        $settings->update(['logo_path' => $newFilename]);

        $this->info("✓ Logo optimized successfully!");
        
        return 0;
    }
}

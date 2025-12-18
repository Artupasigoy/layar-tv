<?php

namespace App\Jobs;

use App\Models\Media;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ProcessVideoJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 1800; // 30 minutes for large files

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
     * FFmpeg processing for digital signage:
     * - H.264 codec, profile main, level 4.0
     * - Max 1920x1080 resolution
     * - 2-4 Mbps bitrate
     * - Keep audio (user requested)
     * - yuv420p pixel format for compatibility
     */
    public function handle(): void
    {
        $media = $this->media;

        // Update status to processing
        $media->update(['processing_status' => 'processing']);

        try {
            $inputPath = Storage::disk('public')->path($media->file_path);
            
            // Generate output filename with hash for cache busting
            $baseName = pathinfo($media->file_path, PATHINFO_FILENAME);
            $hash = substr(md5($media->id . time()), 0, 8);
            $outputFilename = "media/processed/{$baseName}_{$hash}.mp4";
            $outputPath = Storage::disk('public')->path($outputFilename);

            // Ensure processed directory exists
            Storage::disk('public')->makeDirectory('media/processed');

            // Get video duration before processing
            $duration = $this->getVideoDuration($inputPath);

            // FFmpeg command for signage-optimized video
            $ffmpegPath = $this->getFFmpegPath();
            $command = sprintf(
                '"%s" -i "%s" -vf "scale=\'min(1920,iw)\':\'min(1080,ih)\':force_original_aspect_ratio=decrease" ' .
                '-c:v libx264 -profile:v main -level 4.0 -b:v 3M -maxrate 4M -bufsize 6M ' .
                '-pix_fmt yuv420p -movflags +faststart ' .
                '-c:a aac -b:a 128k ' .
                '-y "%s" 2>&1',
                $ffmpegPath,
                $inputPath,
                $outputPath
            );

            Log::info("Processing video: {$command}");
            
            exec($command, $output, $returnCode);

            if ($returnCode !== 0) {
                throw new \Exception("FFmpeg failed with code {$returnCode}: " . implode("\n", $output));
            }

            // Generate thumbnail
            $thumbnailFilename = "media/thumbnails/{$baseName}_{$hash}.jpg";
            $thumbnailPath = Storage::disk('public')->path($thumbnailFilename);
            Storage::disk('public')->makeDirectory('media/thumbnails');

            $thumbCommand = sprintf(
                '"%s" -i "%s" -ss 00:00:01 -vframes 1 -vf "scale=320:-1" -y "%s" 2>&1',
                $ffmpegPath,
                $outputPath,
                $thumbnailPath
            );
            exec($thumbCommand);

            // Update media record
            $media->update([
                'processed_path' => $outputFilename,
                'thumbnail_path' => $thumbnailFilename,
                'duration' => $duration,
                'processing_status' => 'completed',
            ]);

            Log::info("Video processed successfully: {$media->id}");

            // Trigger playlist regeneration
            GeneratePlaylistJob::dispatch();

        } catch (\Exception $e) {
            Log::error("Video processing failed: " . $e->getMessage());
            
            $media->update([
                'processing_status' => 'failed',
            ]);

            throw $e;
        }
    }

    /**
     * Get FFmpeg path based on OS
     */
    protected function getFFmpegPath(): string
    {
        // Check common paths
        $paths = [
            'C:\laragon\bin\ffmpeg\bin\ffmpeg.exe', // Standard Laragon manual install with nested bin
            'C:\laragon\bin\ffmpeg\ffmpeg.exe',
            'C:\ffmpeg\bin\ffmpeg.exe',
            'ffmpeg', // System PATH
            '/usr/bin/ffmpeg',
            '/usr/local/bin/ffmpeg',
        ];

        foreach ($paths as $path) {
            if ($path === 'ffmpeg') {
                // Check if ffmpeg is in PATH
                exec('where ffmpeg 2>&1', $output, $returnCode);
                if ($returnCode === 0) {
                    return 'ffmpeg';
                }
            } elseif (file_exists($path)) {
                return $path;
            }
        }

        return 'ffmpeg'; // Default, hope it's in PATH
    }

    /**
     * Get video duration in seconds using FFprobe
     */
    protected function getVideoDuration(string $path): ?int
    {
        $ffprobePath = str_replace('ffmpeg', 'ffprobe', $this->getFFmpegPath());
        
        $command = sprintf(
            '"%s" -v error -show_entries format=duration -of default=noprint_wrappers=1:nokey=1 "%s" 2>&1',
            $ffprobePath,
            $path
        );

        exec($command, $output, $returnCode);

        if ($returnCode === 0 && !empty($output[0])) {
            return (int) round((float) $output[0]);
        }

        return null;
    }
}

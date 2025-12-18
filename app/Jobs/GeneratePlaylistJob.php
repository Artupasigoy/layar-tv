<?php

namespace App\Jobs;

use App\Models\Media;
use App\Models\Setting;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeneratePlaylistJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Generate static playlist.json for signage player.
     * This eliminates the need for polling - player loads this once.
     */
    public function handle(): void
    {
        try {
            // Ensure signage directory exists
            Storage::disk('public')->makeDirectory('signage');

            // Get all active media that are ready for playback
            $media = Media::where('is_active', true)
                ->where('processing_status', 'completed')
                ->orderBy('order')
                ->get()
                ->map(function ($m) {
                    return [
                        'id' => $m->id,
                        'type' => $m->type,
                        'url' => $m->type === 'video' ? $m->playback_url : $m->display_url,
                        'duration' => $m->duration, // For videos, null for images (use settings)
                    ];
                });

            // Get settings (create defaults if not exists)
            $settings = Setting::firstOrCreate([], [
                'show_clock' => true,
                'image_duration' => 10,
                'enable_animation' => false,
                'max_file_size' => 50,
                'auto_reload_interval' => 6,
            ]);
            
            $playlist = [
                'version' => time(), // For cache busting
                'generated_at' => now()->toISOString(),
                'settings' => [
                    'image_duration' => $settings->image_duration ?? 10,
                    'show_clock' => $settings->show_clock ?? true,
                    'logo_path' => $settings->logo_path ? Storage::url($settings->logo_path) : null,
                    'enable_animation' => $settings->enable_animation ?? false,
                    'auto_reload_interval' => $settings->auto_reload_interval ?? 6,
                ],
                'media' => $media,
            ];

            // Generate hash for cache busting
            $hash = md5(json_encode($playlist));
            $playlist['hash'] = $hash;

            // Write to static file
            $json = json_encode($playlist, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
            Storage::disk('public')->put('signage/playlist.json', $json);

            // Update settings with new hash
            if ($settings) {
                $settings->update(['playlist_hash' => $hash]);
            }

            Log::info("Playlist generated successfully. Hash: {$hash}");

        } catch (\Exception $e) {
            Log::error("Playlist generation failed: " . $e->getMessage());
            throw $e;
        }
    }
}

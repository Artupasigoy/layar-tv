<?php

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\Setting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SignageController extends Controller
{
    /**
     * Render the static signage player.
     * This is a minimal HTML page with inline CSS/JS - no Livewire, no Vite.
     */
    public function player(Request $request): View
    {
        $settings = Setting::first();
        
        return view('signage', [
            'settings' => $settings,
            'playlistUrl' => asset('storage/signage/playlist.json'),
        ]);
    }

    /**
     * Return the playlist JSON.
     * This can be served directly from static file, but this endpoint
     * allows for dynamic generation if needed.
     */
    public function playlist(): JsonResponse
    {
        // Try to serve from static file first
        if (Storage::disk('public')->exists('signage/playlist.json')) {
            $content = Storage::disk('public')->get('signage/playlist.json');
            $data = json_decode($content, true);
            
            return response()->json($data)
                ->header('Cache-Control', 'public, max-age=300'); // 5 min cache
        }

        // Fallback: generate on-the-fly
        $media = Media::where('is_active', true)
            ->where('processing_status', 'completed')
            ->orderBy('order')
            ->get()
            ->map(function ($m) {
                return [
                    'id' => $m->id,
                    'type' => $m->type,
                    'url' => $m->type === 'video' ? $m->playback_url : $m->display_url,
                    'duration' => $m->duration,
                ];
            });

        $settings = Setting::first();

        return response()->json([
            'version' => time(),
            'generated_at' => now()->toISOString(),
            'settings' => [
                'image_duration' => $settings->image_duration ?? 10,
                'show_clock' => $settings->show_clock ?? true,
                'logo_path' => $settings->logo_path ? asset('storage/' . $settings->logo_path) : null,
                'enable_animation' => $settings->enable_animation ?? false,
                'auto_reload_interval' => $settings->auto_reload_interval ?? 6,
            ],
            'media' => $media,
        ])->header('Cache-Control', 'public, max-age=60');
    }

    /**
     * Legacy /signage route - redirects to new /display
     */
    public function legacyRedirect()
    {
        return redirect()->route('display');
    }
}

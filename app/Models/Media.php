<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'title',
        'file_path',
        'processed_path',
        'thumbnail_path',
        'display_path',
        'type',
        'file_size',
        'duration',
        'processing_status',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'duration' => 'integer',
        'file_size' => 'integer',
    ];

    /**
     * Get the URL for playback (use processed if available)
     */
    public function getPlaybackUrlAttribute(): string
    {
        $path = $this->processed_path ?? $this->file_path;
        return Storage::url($path);
    }

    /**
     * Get the display image URL (for signage display)
     */
    public function getDisplayUrlAttribute(): string
    {
        $path = $this->display_path ?? $this->file_path;
        return Storage::url($path);
    }

    /**
     * Get thumbnail URL
     */
    public function getThumbnailUrlAttribute(): string
    {
        $path = $this->thumbnail_path ?? $this->file_path;
        return Storage::url($path);
    }

    /**
     * Check if media is ready for playback
     */
    public function isReady(): bool
    {
        return $this->processing_status === 'completed';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'show_clock',
        'logo_path',
        'image_duration',
        'enable_animation',
        'max_file_size',
        'auto_reload_interval',
        'playlist_hash',
    ];

    protected $casts = [
        'show_clock' => 'boolean',
        'enable_animation' => 'boolean',
        'image_duration' => 'integer',
        'max_file_size' => 'integer',
        'auto_reload_interval' => 'integer',
    ];
}

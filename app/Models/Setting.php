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
    ];
    protected $casts = [
        'show_clock' => 'boolean',
        'enable_animation' => 'boolean',
    ];
}

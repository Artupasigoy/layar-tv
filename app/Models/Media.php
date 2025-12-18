<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'title',
        'file_path',
        'type',
        'file_size',
        'order',
        'is_active',
    ];
}

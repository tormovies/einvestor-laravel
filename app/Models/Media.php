<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'wp_id', 'filename', 'original_filename', 'path', 'url',
        'mime_type', 'size', 'width', 'height', 'title', 'alt'
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Redirect extends Model
{
    protected $fillable = [
        'old_url', 'new_url', 'type', 'status_code', 'is_active', 'hits'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'status_code' => 'integer',
    ];

    public static function findRedirect(string $path): ?self
    {
        return self::where('old_url', $path)
            ->where('is_active', true)
            ->first();
    }
}

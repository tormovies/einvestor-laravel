<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Tag extends Model
{
    protected $fillable = [
        'wp_id', 'slug', 'name', 'description', 'count',
        'seo_title', 'seo_description', 'seo_h1', 'seo_intro_text'
    ];

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_tag');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_tag');
    }

    public function getUrlAttribute(): string
    {
        return route('tag.show', $this->slug);
    }
}

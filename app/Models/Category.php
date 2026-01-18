<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'wp_id', 'slug', 'name', 'description', 'type', 'parent_id', 'count'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_category')->where('type', 'post');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_category')->where('type', 'product');
    }

    public function getUrlAttribute(): string
    {
        return route('category.show', $this->slug);
    }
}

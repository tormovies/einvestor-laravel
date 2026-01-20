<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'wp_id', 'slug', 'name', 'description', 'short_description',
        'price', 'sku', 'stock_status', 'stock_quantity',
        'status', 'featured_image_id', 'published_at',
        'seo_title', 'seo_description', 'seo_h1', 'seo_intro_text'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'published_at' => 'datetime',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_category')
            ->where('type', 'product');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tag');
    }

    public function images(): BelongsToMany
    {
        return $this->belongsToMany(Media::class, 'product_image')
            ->orderBy('order')
            ->withPivot('order');
    }

    public function featuredImage(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'featured_image_id');
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProductFile::class)->orderBy('order');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function approvedComments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable')
            ->where('status', 'approved');
    }

    public function getUrlAttribute(): string
    {
        return route('products.show', $this->slug);
    }

    public function isInStock(): bool
    {
        return $this->stock_status === 'in_stock' 
            && ($this->stock_quantity === null || $this->stock_quantity > 0);
    }
}

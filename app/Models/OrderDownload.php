<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderDownload extends Model
{
    protected $fillable = [
        'order_id', 'order_item_id', 'product_file_id', 'user_id', 'email',
        'download_token', 'download_count', 'download_limit', 'expires_at'
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function productFile(): BelongsTo
    {
        return $this->belongsTo(ProductFile::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function canDownload(): bool
    {
        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        if ($this->download_count >= $this->download_limit) {
            return false;
        }

        return true;
    }

    public function incrementDownloadCount(): void
    {
        $this->increment('download_count');
    }
}

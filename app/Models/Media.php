<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $fillable = [
        'wp_id', 'filename', 'original_filename', 'path', 'url',
        'mime_type', 'size', 'width', 'height', 'title', 'alt'
    ];

    /**
     * Получить URL изображения
     * Если это локальный путь - возвращаем через Storage с учетом текущего хоста и порта
     * Если это внешний URL (старый WordPress) - возвращаем как есть
     */
    public function getImageUrlAttribute(): string
    {
        // Приоритет: используем path, если он есть
        if ($this->path) {
            // Проверяем, что это не внешний URL в path
            if (!str_starts_with($this->path, 'http://') && !str_starts_with($this->path, 'https://')) {
                // Это локальный путь - используем asset() для правильного URL с портом
                return asset('storage/' . $this->path);
            }
        }

        // Если URL начинается с http:// или https:// - проверяем, не localhost ли это
        if (str_starts_with($this->url, 'http://') || str_starts_with($this->url, 'https://')) {
            // Если это localhost без порта или с неправильным портом - используем path если есть
            if (str_contains($this->url, 'localhost') && $this->path) {
                return asset('storage/' . $this->path);
            }
            // Иначе это внешний URL (старый WordPress) - возвращаем как есть
            return $this->url;
        }

        // Если есть локальный путь - используем asset()
        if ($this->path) {
            return asset('storage/' . $this->path);
        }

        // Если url начинается с /storage/ - используем asset()
        if (str_starts_with($this->url, '/storage/')) {
            return asset($this->url);
        }

        // Возвращаем оригинальный URL как fallback
        return $this->url;
    }
}

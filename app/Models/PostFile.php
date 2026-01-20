<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostFile extends Model
{
    protected $fillable = [
        'post_id', 'file_path', 'file_name', 'file_size', 'mime_type', 'order', 'download_count'
    ];

    protected $casts = [
        'file_size' => 'integer',
        'order' => 'integer',
        'download_count' => 'integer',
    ];

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * Получить размер файла в человекочитаемом формате
     */
    public function getFormattedSizeAttribute(): string
    {
        if (!$this->file_size) {
            return '0 B';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $size = $this->file_size;
        $unitIndex = 0;

        while ($size >= 1024 && $unitIndex < count($units) - 1) {
            $size /= 1024;
            $unitIndex++;
        }

        return number_format($size, 2) . ' ' . $units[$unitIndex];
    }

    /**
     * Получить иконку файла на основе расширения
     */
    public function getFileIconAttribute(): string
    {
        $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'mq4':
            case 'mq5':
                return 'code';
            case 'ex4':
            case 'ex5':
                return 'executable';
            case 'zip':
            case 'rar':
            case '7z':
            case 'gz':
            case 'tar':
                return 'archive';
            default:
                return 'file';
        }
    }

    /**
     * Получить цвет иконки на основе типа файла
     */
    public function getFileIconColorAttribute(): string
    {
        $extension = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        
        switch ($extension) {
            case 'mq4':
            case 'mq5':
                return '#2563eb'; // Синий
            case 'ex4':
            case 'ex5':
                return '#10b981'; // Зеленый
            case 'zip':
            case 'rar':
            case '7z':
                return '#f59e0b'; // Оранжевый
            default:
                return '#6b7280'; // Серый
        }
    }
}

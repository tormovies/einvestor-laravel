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
        // Нормализуем путь - убираем начальный слеш
        $normalizedPath = trim($path, '/');
        
        // Пробуем найти по точному совпадению (основной случай)
        $redirect = self::where('old_url', $normalizedPath)
            ->where('is_active', true)
            ->first();
        
        if ($redirect) {
            return $redirect;
        }
        
        // Если не нашли, пробуем декодировать URL (на случай если пришел закодированный, но Laravel его не декодировал)
        $decodedPath = urldecode($normalizedPath);
        if ($decodedPath !== $normalizedPath) {
            $decodedPath = trim($decodedPath, '/');
            $redirect = self::where('old_url', $decodedPath)
                ->where('is_active', true)
                ->first();
            
            if ($redirect) {
                return $redirect;
            }
        }
        
        // Также пробуем найти по закодированной версии (если в базе хранится закодированная версия)
        $encodedPath = urlencode($normalizedPath);
        if ($encodedPath !== $normalizedPath) {
            $redirect = self::where('old_url', $encodedPath)
                ->where('is_active', true)
                ->first();
            
            if ($redirect) {
                return $redirect;
            }
        }
        
        // Специальный случай: если в базе хранится URL-encoded кириллица (например: %d0%b8%d0%bd...),
        // а запрос приходит декодированным, нужно проверить все записи где old_url после декодирования совпадает
        // Используем более эффективный подход - проверяем только те записи, где есть % в URL (признак URL-encoding)
        $possibleEncoded = self::where('is_active', true)
            ->where('old_url', 'like', '%%%')
            ->get();
        
        foreach ($possibleEncoded as $redirect) {
            $decodedOldUrl = urldecode($redirect->old_url);
            $decodedOldUrlNormalized = trim($decodedOldUrl, '/');
            if ($decodedOldUrlNormalized === $normalizedPath) {
                return $redirect;
            }
        }
        
        return null;
    }
}

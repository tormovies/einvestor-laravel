<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = [
        'key', 'value', 'type', 'group', 'description'
    ];

    /**
     * Получить значение настройки по ключу
     */
    public static function get(string $key, $default = null)
    {
        return Cache::remember("setting.{$key}", 3600, function () use ($key, $default) {
            $setting = self::where('key', $key)->first();
            
            if (!$setting) {
                return $default;
            }
            
            return self::castValue($setting->value, $setting->type);
        });
    }

    /**
     * Установить значение настройки
     */
    public static function set(string $key, $value, string $type = 'string', string $group = 'general', ?string $description = null): void
    {
        $setting = self::firstOrNew(['key' => $key]);
        $setting->value = self::prepareValue($value, $type);
        $setting->type = $type;
        $setting->group = $group;
        if ($description) {
            $setting->description = $description;
        }
        $setting->save();
        
        Cache::forget("setting.{$key}");
    }

    /**
     * Приведение значения к нужному типу
     */
    private static function castValue($value, string $type)
    {
        if ($value === null) {
            return null;
        }
        
        return match ($type) {
            'boolean', 'bool' => filter_var($value, FILTER_VALIDATE_BOOLEAN),
            'integer', 'int' => (int) $value,
            'float', 'double' => (float) $value,
            'json', 'array' => json_decode($value, true),
            default => $value,
        };
    }

    /**
     * Подготовка значения для сохранения
     */
    private static function prepareValue($value, string $type)
    {
        return match ($type) {
            'boolean', 'bool' => $value ? '1' : '0',
            'json', 'array' => is_string($value) ? $value : json_encode($value),
            default => (string) $value,
        };
    }

    /**
     * Очистка кэша настроек
     */
    public static function clearCache(): void
    {
        Cache::flush();
    }
}

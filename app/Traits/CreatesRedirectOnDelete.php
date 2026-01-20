<?php

namespace App\Traits;

use App\Models\Redirect;

trait CreatesRedirectOnDelete
{
    /**
     * Создает или обновляет редирект 301 для удаляемого материала на главную страницу
     * 
     * @param string $oldUrl URL удаляемого материала (без домена, например: /products/slug или /category/slug)
     * @return Redirect|null Созданный или обновленный редирект
     */
    protected function createRedirectToHome(string $oldUrl): ?Redirect
    {
        // Нормализуем URL - убираем начальный слеш если есть
        $normalizedOldUrl = ltrim($oldUrl, '/');
        
        // Главная страница для редиректа
        $homeUrl = '/';
        
        // Проверяем, существует ли уже редирект для этого URL
        $existingRedirect = Redirect::where('old_url', $normalizedOldUrl)->first();
        
        if ($existingRedirect) {
            // Если редирект уже существует, обновляем его на главную страницу
            $existingRedirect->update([
                'new_url' => $homeUrl,
                'status_code' => 301,
                'is_active' => true,
            ]);
            
            return $existingRedirect;
        }
        
        // Создаем новый редирект
        return Redirect::create([
            'old_url' => $normalizedOldUrl,
            'new_url' => $homeUrl,
            'type' => 'permanent',
            'status_code' => 301,
            'is_active' => true,
            'hits' => 0,
        ]);
    }

    /**
     * Получить URL для редиректа на основе типа модели
     */
    protected function getUrlForRedirect($model): string
    {
        // Если у модели есть метод getUrlAttribute или url атрибут
        if (method_exists($model, 'getUrlAttribute') || isset($model->url)) {
            $fullUrl = $model->url ?? $model->getUrlAttribute();
            
            // Извлекаем путь из полного URL (убираем домен)
            $parsedUrl = parse_url($fullUrl);
            return $parsedUrl['path'] ?? '/';
        }
        
        // Fallback на slug
        if (isset($model->slug)) {
            // Определяем префикс на основе типа модели
            $className = class_basename(get_class($model));
            
            switch ($className) {
                case 'Page':
                    return '/' . $model->slug;
                case 'Product':
                    return '/products/' . $model->slug;
                case 'Post':
                    return '/articles/' . $model->slug;
                case 'Category':
                    return '/category/' . $model->slug;
                case 'Tag':
                    return '/tag/' . $model->slug;
                default:
                    return '/' . $model->slug;
            }
        }
        
        return '/';
    }
}

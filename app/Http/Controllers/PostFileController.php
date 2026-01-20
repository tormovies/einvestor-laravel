<?php

namespace App\Http\Controllers;

use App\Models\PostFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class PostFileController extends Controller
{
    /**
     * Скачивание файла статьи
     * GET /post-file/{id}
     */
    public function download($id)
    {
        // Находим файл
        $postFile = PostFile::with('post')->findOrFail($id);
        
        // Проверяем, что файл принадлежит опубликованной статье
        if (!$postFile->post || $postFile->post->status !== 'publish') {
            abort(404, 'Файл не найден или статья не опубликована');
        }

        // Проверка Referer (защита от прямых ссылок со сторонних сайтов)
        $referer = request()->header('referer');
        $appUrl = config('app.url');
        $appHost = parse_url($appUrl, PHP_URL_HOST);
        $refererHost = $referer ? parse_url($referer, PHP_URL_HOST) : null;
        
        // Разрешаем если:
        // 1. Referer отсутствует (пользователь может блокировать referer - разрешаем)
        // 2. Referer содержит наш домен
        // Запрещаем если referer есть, но с другого домена
        if ($referer && $refererHost && $refererHost !== $appHost) {
            abort(403, 'Доступ к файлу разрешен только с сайта ' . $appHost);
        }

        // Базовая защита от массовой скачки через rate limiting по IP
        $key = 'post-file-download:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 10)) {
            $seconds = RateLimiter::availableIn($key);
            abort(429, "Слишком много запросов. Попробуйте через {$seconds} секунд.");
        }
        RateLimiter::hit($key, 60); // 10 запросов в минуту

        // Проверяем существование файла на диске
        $filePath = $postFile->file_path;
        $absolutePath = null;

        // Пробуем разные варианты расположения файла
        if (Storage::disk('local')->exists($filePath)) {
            $absolutePath = Storage::disk('local')->path($filePath);
        } elseif (file_exists(storage_path('app/' . ltrim($filePath, '/')))) {
            $absolutePath = storage_path('app/' . ltrim($filePath, '/'));
        } elseif (file_exists($filePath) && (strpos($filePath, '/') === 0 || preg_match('/^[A-Z]:\\\\/i', $filePath))) {
            $absolutePath = $filePath;
        }

        if (!$absolutePath || !file_exists($absolutePath)) {
            abort(404, 'Файл не найден на сервере');
        }

        // Увеличиваем счетчик скачиваний
        $postFile->increment('download_count');

        // Возвращаем файл для скачивания
        return response()->download($absolutePath, $postFile->file_name, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $postFile->file_name . '"',
        ]);
    }
}

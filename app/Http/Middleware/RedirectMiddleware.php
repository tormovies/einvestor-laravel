<?php

namespace App\Http\Middleware;

use App\Models\Redirect;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Проверяем редиректы только для GET запросов
        if ($request->isMethod('GET')) {
            // Получаем путь из запроса
            // Используем getPathInfo() чтобы получить путь как есть, без декодирования
            // Это важно для URL-encoded кириллицы (%d0%b8...)
            $path = trim($request->getPathInfo(), '/');
            
            // Также пробуем декодированную версию (на случай если Laravel уже декодировал)
            $decodedPath = trim($request->path(), '/');
            
            // Исключаем системные пути - они обрабатываются роутингом
            $excludedPaths = [
                '',  // Главная страница
                'cart',
                'checkout',
                'download',
                'login',
                'logout',
                'account',
                'articles',
                'products',
                'category',
                'tag',
                'admin',
                'api',
            ];
            
            // Проверяем, не начинается ли путь с исключенного префикса
            $pathParts = explode('/', $path);
            $firstSegment = $pathParts[0] ?? '';
            
            if (in_array($firstSegment, $excludedPaths)) {
                return $next($request);
            }
            
            // Проверяем таблицу редиректов
            // Пробуем сначала с путем как есть (может быть URL-encoded)
            $redirect = Redirect::findRedirect($path);
            
            // Если не нашли и пути разные, пробуем декодированную версию
            if (!$redirect && $decodedPath !== $path) {
                $redirect = Redirect::findRedirect($decodedPath);
            }
            
            if ($redirect) {
                // Нормализуем URL для сравнения (убираем начальный слеш)
                $normalizedOldUrl = trim($redirect->old_url, '/');
                $normalizedNewUrl = trim($redirect->new_url, '/');
                
                // Если старый и новый URL одинаковые, пропускаем редирект
                // (это может быть редирект на самого себя, что вызывает цикл)
                if ($normalizedOldUrl === $normalizedNewUrl) {
                    return $next($request);
                }
                
                $redirect->increment('hits');
                return redirect($redirect->new_url, $redirect->status_code);
            }
        }
        
        return $next($request);
    }
}

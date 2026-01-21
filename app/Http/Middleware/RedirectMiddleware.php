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
            
            // ВАЖНО: Главная страница (пустой путь) НЕ должна проверяться на редиректы
            // Это предотвращает циклы, когда есть редирект с old_url = '' (пустая строка)
            // Главная страница должна обрабатываться через обычный роутинг Laravel
            if ($path === '' || $decodedPath === '') {
                return $next($request);
            }
            
            // ВАЖНО: Сначала проверяем редиректы для ВСЕХ путей
            // Это позволяет редиректам работать даже для путей, начинающихся с articles, products и т.д.
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
                
                // Дополнительная защита: если редирект ведет на главную страницу (/),
                // и мы уже на главной странице (не должно произойти из-за проверки выше, но на всякий случай)
                if ($normalizedNewUrl === '' && $path === '') {
                    return $next($request);
                }
                
                $redirect->increment('hits');
                return redirect($redirect->new_url, $redirect->status_code);
            }
            
            // Если редирект не найден, проверяем исключения
            // Исключаем системные пути - они обрабатываются роутингом
            // Но только если для них нет редиректа (проверка выше)
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
                'robokassa',  // Робокасса - обрабатывается отдельно
            ];
            
            // Проверяем, не начинается ли путь с исключенного префикса
            $pathParts = explode('/', $path);
            $firstSegment = $pathParts[0] ?? '';
            
            // Если путь начинается с исключенного префикса и редирект не найден,
            // пропускаем его дальше в роутинг
            if (in_array($firstSegment, $excludedPaths)) {
                return $next($request);
            }
        }
        
        return $next($request);
    }
}

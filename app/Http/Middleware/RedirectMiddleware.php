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
            $path = trim($request->path(), '/');
            
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
            $redirect = Redirect::findRedirect($path);
            
            if ($redirect) {
                $redirect->increment('hits');
                return redirect($redirect->new_url, $redirect->status_code);
            }
        }
        
        return $next($request);
    }
}

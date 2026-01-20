<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Http\Request;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'robokassa/result',
        'robokassa/success',
        'robokassa/fail',
    ];

    /**
     * Determine if the request has a URI that should pass through CSRF verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function shouldPassThrough($request)
    {
        // Проверяем путь разными способами для надежности
        $path = $request->path();
        $pathInfo = trim($request->getPathInfo(), '/');
        
        // Явно пропускаем все запросы к Робокассе без проверки CSRF
        if (str_starts_with($path, 'robokassa/') || 
            str_starts_with($pathInfo, 'robokassa/') || 
            $request->is('robokassa/*')) {
            \Illuminate\Support\Facades\Log::info('VerifyCsrfToken: shouldPassThrough - Пропуск CSRF для Робокассы', [
                'path' => $path,
                'pathInfo' => $pathInfo,
                'method' => $request->method(),
            ]);
            return true;
        }
        
        // Вызываем родительский метод для проверки массива $except
        return parent::shouldPassThrough($request);
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, \Closure $next)
    {
        // Проверяем путь разными способами для надежности
        $path = $request->path();
        $pathInfo = trim($request->getPathInfo(), '/');
        
        // Явно пропускаем все запросы к Робокассе без проверки CSRF
        if (str_starts_with($path, 'robokassa/') || 
            str_starts_with($pathInfo, 'robokassa/') || 
            $request->is('robokassa/*')) {
            \Illuminate\Support\Facades\Log::info('VerifyCsrfToken: handle - Пропуск CSRF для Робокассы', [
                'path' => $path,
                'pathInfo' => $pathInfo,
                'method' => $request->method(),
            ]);
            return $next($request);
        }

        try {
            return parent::handle($request, $next);
        } catch (\Illuminate\Session\TokenMismatchException $e) {
            // Если ошибка CSRF для Робокассы - выдаем отладочную информацию
            $path = $request->path();
            $pathInfo = trim($request->getPathInfo(), '/');
            
            if (str_starts_with($path, 'robokassa/') || 
                str_starts_with($pathInfo, 'robokassa/') || 
                $request->is('robokassa/*')) {
                \Illuminate\Support\Facades\Log::warning('VerifyCsrfToken: CSRF ошибка для Робокассы, возвращаем отладку', [
                    'path' => $path,
                    'pathInfo' => $pathInfo,
                ]);
                
                $debugInfo = [
                    'error' => 'CSRF Token Mismatch',
                    'path' => $path,
                    'pathInfo' => $pathInfo,
                    'method' => $request->method(),
                    'url' => $request->fullUrl(),
                    'all_params' => $request->all(),
                    'query_params' => $request->query(),
                    'post_params' => $request->post(),
                    'headers' => array_map(function ($header) {
                        return is_array($header) ? implode(', ', $header) : $header;
                    }, $request->headers->all()),
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'has_session' => $request->hasSession(),
                    'session_id' => $request->hasSession() ? session()->getId() : null,
                    'csrf_token' => csrf_token(),
                    'x_csrf_token_header' => $request->header('X-CSRF-TOKEN'),
                    'x_xsrf_token_header' => $request->header('X-XSRF-TOKEN'),
                ];
                
                return response()->view('debug.robokassa', ['debug' => $debugInfo], 200);
            }
            throw $e;
        }
    }
}

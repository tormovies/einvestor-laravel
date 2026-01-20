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
        $fullUrl = $request->fullUrl();
        
        // Логируем ВСЕ запросы к robokassa для отладки
        if (str_contains($path, 'robokassa') || str_contains($pathInfo, 'robokassa') || str_contains($fullUrl, 'robokassa')) {
            \Illuminate\Support\Facades\Log::info('VerifyCsrfToken: Запрос к Робокассе обнаружен', [
                'path' => $path,
                'pathInfo' => $pathInfo,
                'fullUrl' => $fullUrl,
                'method' => $request->method(),
                'uri' => $request->getRequestUri(),
            ]);
        }
        
        // Явно пропускаем все запросы к Робокассе без проверки CSRF
        if (str_starts_with($path, 'robokassa/') || 
            str_starts_with($pathInfo, 'robokassa/') || 
            $request->is('robokassa/*') ||
            str_contains($path, 'robokassa') ||
            str_contains($pathInfo, 'robokassa')) {
            \Illuminate\Support\Facades\Log::info('VerifyCsrfToken: handle - Пропуск CSRF для Робокассы', [
                'path' => $path,
                'pathInfo' => $pathInfo,
                'method' => $request->method(),
                'fullUrl' => $fullUrl,
            ]);
            return $next($request);
        }

        try {
            return parent::handle($request, $next);
        } catch (\Illuminate\Session\TokenMismatchException $e) {
            // Если ошибка CSRF - проверяем, не для ли Робокассы
            $path = $request->path();
            $pathInfo = trim($request->getPathInfo(), '/');
            $fullUrl = $request->fullUrl();
            
            \Illuminate\Support\Facades\Log::error('VerifyCsrfToken: CSRF TokenMismatchException', [
                'path' => $path,
                'pathInfo' => $pathInfo,
                'fullUrl' => $fullUrl,
                'method' => $request->method(),
            ]);
            
            // Проверяем все возможные варианты пути к Робокассе
            if (str_starts_with($path, 'robokassa/') || 
                str_starts_with($pathInfo, 'robokassa/') || 
                $request->is('robokassa/*') ||
                str_contains($path, 'robokassa') ||
                str_contains($pathInfo, 'robokassa') ||
                str_contains($fullUrl, 'robokassa')) {
                \Illuminate\Support\Facades\Log::warning('VerifyCsrfToken: CSRF ошибка для Робокассы, возвращаем отладку', [
                    'path' => $path,
                    'pathInfo' => $pathInfo,
                    'fullUrl' => $fullUrl,
                ]);
                
                try {
                    $debugInfo = [
                        'error' => 'CSRF Token Mismatch',
                        'path' => $path,
                        'pathInfo' => $pathInfo,
                        'fullUrl' => $fullUrl,
                        'method' => $request->method(),
                        'uri' => $request->getRequestUri(),
                        'all_params' => $request->all(),
                        'query_params' => $request->query(),
                        'post_params' => $request->post(),
                        'headers' => array_map(function ($header) {
                            return is_array($header) ? implode(', ', $header) : (string)$header;
                        }, $request->headers->all()),
                        'ip' => $request->ip(),
                        'user_agent' => $request->userAgent(),
                        'has_session' => $request->hasSession(),
                        'session_id' => $request->hasSession() ? (session()->getId() ?? null) : null,
                    ];
                    
                    try {
                        $debugInfo['csrf_token'] = csrf_token();
                    } catch (\Exception $e) {
                        $debugInfo['csrf_token'] = 'Не доступен';
                    }
                    
                    $debugInfo['x_csrf_token_header'] = $request->header('X-CSRF-TOKEN');
                    $debugInfo['x_xsrf_token_header'] = $request->header('X-XSRF-TOKEN');
                    
                    return response()->view('debug.robokassa', ['debug' => $debugInfo], 200);
                } catch (\Exception $viewException) {
                    \Illuminate\Support\Facades\Log::error('Error rendering debug view', ['error' => $viewException->getMessage()]);
                    return response()->json([
                        'error' => 'CSRF Token Mismatch для Робокассы',
                        'message' => $viewException->getMessage(),
                        'path' => $path,
                    ], 200);
                }
            }
            throw $e;
        }
    }
}

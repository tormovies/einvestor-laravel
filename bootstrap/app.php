<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\RedirectMiddleware::class,
        ]);
        
        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Обработка CSRF ошибок для Робокассы - показываем отладочную информацию
        $exceptions->render(function (\Illuminate\Session\TokenMismatchException $e, \Illuminate\Http\Request $request) {
            $path = $request->path();
            $pathInfo = trim($request->getPathInfo(), '/');
            $fullUrl = $request->fullUrl();
            
            \Illuminate\Support\Facades\Log::error('Global exception handler: TokenMismatchException', [
                'path' => $path,
                'pathInfo' => $pathInfo,
                'fullUrl' => $fullUrl,
                'method' => $request->method(),
            ]);
            
            // Проверяем разными способами - более агрессивно
            if (str_starts_with($path, 'robokassa/') || 
                str_starts_with($pathInfo, 'robokassa/') || 
                $request->is('robokassa/*') ||
                str_contains($path, 'robokassa') ||
                str_contains($pathInfo, 'robokassa') ||
                str_contains($fullUrl, 'robokassa')) {
                
                \Illuminate\Support\Facades\Log::warning('Global exception handler: CSRF для Робокассы - показываем отладку', [
                    'path' => $path,
                    'pathInfo' => $pathInfo,
                    'fullUrl' => $fullUrl,
                ]);
                
                try {
                    $debugInfo = [
                        'error' => 'CSRF Token Mismatch (перехвачено глобальным обработчиком)',
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
                    \Illuminate\Support\Facades\Log::error('Error in global exception handler view', [
                        'error' => $viewException->getMessage(),
                        'trace' => $viewException->getTraceAsString(),
                    ]);
                    return response()->json([
                        'error' => 'CSRF Token Mismatch для Робокассы (глобальный обработчик)',
                        'message' => $viewException->getMessage(),
                        'path' => $path,
                        'fullUrl' => $fullUrl,
                    ], 200);
                }
            }
        });
    })->create();

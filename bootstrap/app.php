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
            if ($request->is('robokassa/*')) {
                $debugInfo = [
                    'error' => 'CSRF Token Mismatch',
                    'path' => $request->path(),
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
        });
    })->create();

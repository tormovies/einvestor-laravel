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
        // Явно пропускаем все запросы к Робокассе без проверки CSRF
        if ($request->is('robokassa/*')) {
            return $next($request);
        }

        try {
            return parent::handle($request, $next);
        } catch (\Illuminate\Session\TokenMismatchException $e) {
            // Если ошибка CSRF для Робокассы - выдаем отладочную информацию
            if ($request->is('robokassa/*')) {
                return response()->json([
                    'error' => 'CSRF Token Mismatch',
                    'debug' => [
                        'path' => $request->path(),
                        'method' => $request->method(),
                        'url' => $request->fullUrl(),
                        'all_params' => $request->all(),
                        'headers' => $request->headers->all(),
                        'has_session' => $request->hasSession(),
                        'session_id' => $request->hasSession() ? session()->getId() : null,
                        'csrf_token' => csrf_token(),
                        'x_csrf_token_header' => $request->header('X-CSRF-TOKEN'),
                        'x_xsrf_token_header' => $request->header('X-XSRF-TOKEN'),
                    ],
                    'message' => 'Для отладки: проверьте, что запрос от Робокассы не требует CSRF токен'
                ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
            }
            throw $e;
        }
    }
}

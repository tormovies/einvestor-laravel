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
     * Determine if the session and input CSRF tokens match.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function tokensMatch($request)
    {
        // Если путь начинается с robokassa/, пропускаем проверку
        if (str_starts_with($request->path(), 'robokassa/')) {
            return true;
        }

        return parent::tokensMatch($request);
    }
}

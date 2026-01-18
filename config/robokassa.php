<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Robokassa Configuration
    |--------------------------------------------------------------------------
    |
    | Настройки для интеграции с платежной системой Робокасса
    |
    */

    // Логин магазина в системе Робокасса
    'merchant_login' => env('ROBOKASSA_MERCHANT_LOGIN', ''),

    // Пароль #1 (для создания платежа)
    'password1' => env('ROBOKASSA_PASSWORD1', ''),

    // Пароль #2 (для проверки уведомлений)
    'password2' => env('ROBOKASSA_PASSWORD2', ''),

    // Алгоритм хеширования (md5, sha256, sha512)
    'hash_type' => env('ROBOKASSA_HASH_TYPE', 'md5'),

    // Тестовый режим
    'is_test' => env('ROBOKASSA_IS_TEST', true),

    // URL для уведомлений (Result URL)
    'result_url' => env('ROBOKASSA_RESULT_URL', env('APP_URL') . '/robokassa/result'),

    // URL успешной оплаты (Success URL)
    'success_url' => env('ROBOKASSA_SUCCESS_URL', env('APP_URL') . '/robokassa/success'),

    // URL неуспешной оплаты (Fail URL)
    'fail_url' => env('ROBOKASSA_FAIL_URL', env('APP_URL') . '/robokassa/fail'),
];

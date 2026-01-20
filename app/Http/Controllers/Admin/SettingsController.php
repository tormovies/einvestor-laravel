<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Отображение страницы настроек
     */
    public function index()
    {
        // Получаем все настройки Робокассы
        $settings = [
            // Робокасса
            'merchant_login' => Setting::get('robokassa.merchant_login', config('robokassa.merchant_login', '')),
            'password1_test' => Setting::get('robokassa.password1_test', ''),
            'password1_production' => Setting::get('robokassa.password1_production', ''),
            'password2_test' => Setting::get('robokassa.password2_test', ''),
            'password2_production' => Setting::get('robokassa.password2_production', ''),
            'hash_type' => Setting::get('robokassa.hash_type', config('robokassa.hash_type', 'md5')),
            'is_test' => Setting::get('robokassa.is_test', config('robokassa.is_test', true)),
            // Почта
            'mail_mailer' => Setting::get('mail.mailer', config('mail.default', 'log')),
            'mail_host' => Setting::get('mail.host', config('mail.mailers.smtp.host', '')),
            'mail_port' => Setting::get('mail.port', config('mail.mailers.smtp.port', 587)),
            'mail_username' => Setting::get('mail.username', config('mail.mailers.smtp.username', '')),
            'mail_password' => Setting::get('mail.password', ''),
            'mail_password_old' => Setting::get('mail.password', ''),
            'mail_encryption' => Setting::get('mail.encryption', config('mail.mailers.smtp.encryption', 'tls')),
            'mail_from_address' => Setting::get('mail.from_address', config('mail.from.address', '')),
            'mail_from_name' => Setting::get('mail.from_name', config('mail.from.name', '')),
            'mail_admin_email' => Setting::get('mail.admin_email', config('mail.admin_email', '')),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Сохранение настроек
     */
    public function store(Request $request)
    {
        // Валидация настроек Робокассы
        $robokassaValidated = $request->validate([
            'merchant_login' => 'required|string|max:255',
            'password1_test' => 'nullable|string|max:255',
            'password1_test_old' => 'nullable|string',
            'password1_production' => 'nullable|string|max:255',
            'password1_production_old' => 'nullable|string',
            'password2_test' => 'nullable|string|max:255',
            'password2_test_old' => 'nullable|string',
            'password2_production' => 'nullable|string|max:255',
            'password2_production_old' => 'nullable|string',
            'hash_type' => 'required|in:md5,sha256,sha512',
            'is_test' => 'nullable|boolean',
        ]);

        // Валидация настроек почты
        $mailValidated = $request->validate([
            'mail_mailer' => 'required|in:smtp,log,sendmail',
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|integer|min:1|max:65535',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_password_old' => 'nullable|string',
            'mail_encryption' => 'nullable|in:tls,ssl,null',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',
            'mail_admin_email' => 'nullable|email|max:255',
        ]);

        // Сохраняем настройки Робокассы
        $isTest = $request->has('is_test') && $request->boolean('is_test');
        Setting::set('robokassa.merchant_login', $robokassaValidated['merchant_login'], 'string', 'payment', 'Логин магазина в Робокассе');
        
        // Пароли: если поле пустое, сохраняем старое значение
        // Проверяем именно на пустую строку, так как браузер может заполнить поле через автозаполнение
        $password1Test = (isset($robokassaValidated['password1_test']) && $robokassaValidated['password1_test'] !== '') 
            ? $robokassaValidated['password1_test'] 
            : ($robokassaValidated['password1_test_old'] ?? '');
        $password1Production = (isset($robokassaValidated['password1_production']) && $robokassaValidated['password1_production'] !== '') 
            ? $robokassaValidated['password1_production'] 
            : ($robokassaValidated['password1_production_old'] ?? '');
        $password2Test = (isset($robokassaValidated['password2_test']) && $robokassaValidated['password2_test'] !== '') 
            ? $robokassaValidated['password2_test'] 
            : ($robokassaValidated['password2_test_old'] ?? '');
        $password2Production = (isset($robokassaValidated['password2_production']) && $robokassaValidated['password2_production'] !== '') 
            ? $robokassaValidated['password2_production'] 
            : ($robokassaValidated['password2_production_old'] ?? '');
        
        Setting::set('robokassa.password1_test', $password1Test, 'string', 'payment', 'Пароль #1 для тестового режима');
        Setting::set('robokassa.password1_production', $password1Production, 'string', 'payment', 'Пароль #1 для рабочего режима');
        Setting::set('robokassa.password2_test', $password2Test, 'string', 'payment', 'Пароль #2 для тестового режима');
        Setting::set('robokassa.password2_production', $password2Production, 'string', 'payment', 'Пароль #2 для рабочего режима');
        Setting::set('robokassa.hash_type', $robokassaValidated['hash_type'], 'string', 'payment', 'Тип хеширования');
        Setting::set('robokassa.is_test', $isTest, 'boolean', 'payment', 'Тестовый режим');

        // Сохраняем настройки почты
        Setting::set('mail.mailer', $mailValidated['mail_mailer'], 'string', 'email', 'Драйвер почты');
        Setting::set('mail.host', $mailValidated['mail_host'] ?? '', 'string', 'email', 'SMTP хост');
        Setting::set('mail.port', $mailValidated['mail_port'] ?? 587, 'integer', 'email', 'SMTP порт');
        Setting::set('mail.username', $mailValidated['mail_username'] ?? '', 'string', 'email', 'SMTP пользователь');
        
        // Пароль почты: если поле пустое, сохраняем старое значение
        // Проверяем именно на пустую строку
        $mailPassword = (isset($mailValidated['mail_password']) && $mailValidated['mail_password'] !== '') 
            ? $mailValidated['mail_password'] 
            : ($mailValidated['mail_password_old'] ?? '');
        Setting::set('mail.password', $mailPassword, 'string', 'email', 'SMTP пароль');
        
        Setting::set('mail.encryption', $mailValidated['mail_encryption'] ?? 'tls', 'string', 'email', 'Шифрование SMTP');
        Setting::set('mail.from_address', $mailValidated['mail_from_address'] ?? '', 'string', 'email', 'Email отправителя');
        Setting::set('mail.from_name', $mailValidated['mail_from_name'] ?? '', 'string', 'email', 'Имя отправителя');
        Setting::set('mail.admin_email', $mailValidated['mail_admin_email'] ?? '', 'string', 'email', 'Email администратора для уведомлений');

        // Очищаем кэш настроек
        Setting::clearCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Настройки успешно сохранены');
    }
}

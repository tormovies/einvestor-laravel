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
            'merchant_login' => Setting::get('robokassa.merchant_login', config('robokassa.merchant_login', '')),
            'password1_test' => Setting::get('robokassa.password1_test', ''),
            'password1_production' => Setting::get('robokassa.password1_production', ''),
            'password2_test' => Setting::get('robokassa.password2_test', ''),
            'password2_production' => Setting::get('robokassa.password2_production', ''),
            'hash_type' => Setting::get('robokassa.hash_type', config('robokassa.hash_type', 'md5')),
            'is_test' => Setting::get('robokassa.is_test', config('robokassa.is_test', true)),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Сохранение настроек
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
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

        // Определяем, какие пароли использовать в зависимости от режима
        $isTest = $request->has('is_test') && $request->boolean('is_test');
        
        // Сохраняем все настройки
        Setting::set('robokassa.merchant_login', $validated['merchant_login'], 'string', 'payment', 'Логин магазина в Робокассе');
        
        // Пароли: если поле пустое, сохраняем старое значение
        $password1Test = !empty($validated['password1_test']) 
            ? $validated['password1_test'] 
            : ($validated['password1_test_old'] ?? '');
        $password1Production = !empty($validated['password1_production']) 
            ? $validated['password1_production'] 
            : ($validated['password1_production_old'] ?? '');
        $password2Test = !empty($validated['password2_test']) 
            ? $validated['password2_test'] 
            : ($validated['password2_test_old'] ?? '');
        $password2Production = !empty($validated['password2_production']) 
            ? $validated['password2_production'] 
            : ($validated['password2_production_old'] ?? '');
        
        Setting::set('robokassa.password1_test', $password1Test, 'string', 'payment', 'Пароль #1 для тестового режима');
        Setting::set('robokassa.password1_production', $password1Production, 'string', 'payment', 'Пароль #1 для рабочего режима');
        Setting::set('robokassa.password2_test', $password2Test, 'string', 'payment', 'Пароль #2 для тестового режима');
        Setting::set('robokassa.password2_production', $password2Production, 'string', 'payment', 'Пароль #2 для рабочего режима');
        Setting::set('robokassa.hash_type', $validated['hash_type'], 'string', 'payment', 'Тип хеширования');
        Setting::set('robokassa.is_test', $isTest, 'boolean', 'payment', 'Тестовый режим');

        // Очищаем кэш настроек
        Setting::clearCache();

        return redirect()->route('admin.settings.index')
            ->with('success', 'Настройки успешно сохранены');
    }
}

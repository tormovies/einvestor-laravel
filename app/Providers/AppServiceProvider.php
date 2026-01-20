<?php

namespace App\Providers;

use App\Models\Setting;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Применяем настройки почты из БД, если таблица settings существует
        if (Schema::hasTable('settings')) {
            try {
                // Переопределяем настройки почты из БД
                $mailMailer = Setting::get('mail.mailer');
                if ($mailMailer) {
                    Config::set('mail.default', $mailMailer);
                }

                // Настройки SMTP из БД
                $mailHost = Setting::get('mail.host');
                if ($mailHost) {
                    Config::set('mail.mailers.smtp.host', $mailHost);
                }

                $mailPort = Setting::get('mail.port');
                if ($mailPort) {
                    Config::set('mail.mailers.smtp.port', $mailPort);
                }

                $mailUsername = Setting::get('mail.username');
                if ($mailUsername) {
                    Config::set('mail.mailers.smtp.username', $mailUsername);
                }

                $mailPassword = Setting::get('mail.password');
                if ($mailPassword) {
                    Config::set('mail.mailers.smtp.password', $mailPassword);
                }

                $mailEncryption = Setting::get('mail.encryption');
                if ($mailEncryption && $mailEncryption !== 'null') {
                    Config::set('mail.mailers.smtp.encryption', $mailEncryption);
                } elseif ($mailEncryption === 'null') {
                    Config::set('mail.mailers.smtp.encryption', null);
                }

                // Email отправителя
                $mailFromAddress = Setting::get('mail.from_address');
                if ($mailFromAddress) {
                    Config::set('mail.from.address', $mailFromAddress);
                }

                $mailFromName = Setting::get('mail.from_name');
                if ($mailFromName) {
                    Config::set('mail.from.name', $mailFromName);
                }
            } catch (\Exception $e) {
                // Игнорируем ошибки, если таблица settings еще не создана или есть проблемы
                // В этом случае будут использоваться настройки из .env
            }
        }
    }
}

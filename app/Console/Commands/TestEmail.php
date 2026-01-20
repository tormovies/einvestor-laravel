<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Mail\UserCreatedMail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email : Email адрес для отправки тестового письма}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Отправка тестового письма с паролем пользователю';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Некорректный email адрес: ' . $email);
            return 1;
        }
        
        $this->info('Проверка настроек почты...');
        
        // Проверяем настройки
        $mailer = config('mail.default');
        $fromAddress = config('mail.from.address');
        $fromName = config('mail.from.name');
        
        $this->line("Mailer: {$mailer}");
        $this->line("From: {$fromName} <{$fromAddress}>");
        
        // Находим или создаем тестового пользователя
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->info("Пользователь с email {$email} не найден. Создаю тестового пользователя...");
            $user = User::create([
                'email' => $email,
                'name' => 'Тестовый пользователь',
                'password' => bcrypt('test123'),
            ]);
            $this->info("Пользователь создан (ID: {$user->id})");
        } else {
            $this->info("Используется существующий пользователь (ID: {$user->id}, имя: {$user->name})");
        }
        
        // Генерируем тестовый пароль
        $testPassword = 'TEST_PASSWORD_' . strtoupper(\Illuminate\Support\Str::random(8));
        
        $this->info("\nОтправка тестового письма на {$email}...");
        
        try {
            Mail::to($email)->send(new UserCreatedMail($user, $testPassword));
            
            $this->info("✅ Письмо успешно отправлено!");
            $this->line("Тестовый пароль: {$testPassword}");
            
            if ($mailer === 'log') {
                $this->warn("\n⚠️  Внимание: Mailer установлен в 'log' - письмо записано в логи, но не отправлено реально.");
                $this->line("Проверьте файл: storage/logs/laravel.log");
            }
            
            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Ошибка при отправке письма:");
            $this->error($e->getMessage());
            $this->error("\nStack trace:");
            $this->line($e->getTraceAsString());
            
            return 1;
        }
    }
}

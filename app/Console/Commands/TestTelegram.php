<?php

namespace App\Console\Commands;

use App\Services\TelegramNotificationService;
use Illuminate\Console\Command;

class TestTelegram extends Command
{
    protected $signature = 'test:telegram';
    protected $description = 'Отправить тестовое сообщение в Telegram (проверка TELEGRAM_BOT_TOKEN и TELEGRAM_CHAT_ID)';

    public function handle(TelegramNotificationService $telegram): int
    {
        if (!$telegram->isConfigured()) {
            $this->error('Telegram не настроен. Добавьте в .env:');
            $this->line('  TELEGRAM_BOT_TOKEN=...');
            $this->line('  TELEGRAM_CHAT_ID=...');
            return 1;
        }

        $this->info('Отправляю тестовое сообщение...');
        $text = "🧪 Тест\nУведомления из eInvestor (локалка) работают.";
        $ok = $telegram->send($text);

        if ($ok) {
            $this->info('Сообщение отправлено. Проверьте свой Telegram.');
            return 0;
        }

        $this->error('Не удалось отправить. Проверьте токен и chat_id, смотрите storage/logs/laravel.log');
        return 1;
    }
}

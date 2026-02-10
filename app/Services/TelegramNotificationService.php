<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotificationService
{
    protected ?string $botToken;
    protected ?string $chatId;

    public function __construct()
    {
        $this->botToken = config('services.telegram.bot_token');
        $this->chatId = config('services.telegram.chat_id');
    }

    /**
     * Можно ли отправлять (настроены токен и chat_id).
     */
    public function isConfigured(): bool
    {
        return !empty($this->botToken) && !empty($this->chatId);
    }

    /**
     * Отправить сообщение в личный чат Telegram.
     * Возвращает true при успехе, false при ошибке или если не настроено.
     */
    public function send(string $text): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $url = "https://api.telegram.org/bot{$this->botToken}/sendMessage";

        try {
            $response = Http::timeout(5)->post($url, [
                'chat_id' => $this->chatId,
                'text' => $text,
                'disable_web_page_preview' => true,
            ]);

            if (!$response->successful()) {
                Log::warning('Telegram send failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Telegram send error', ['message' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Уведомление о новом заказе.
     */
    public function notifyNewOrder(\App\Models\Order $order): bool
    {
        $order->loadMissing('items');
        $lines = [
            '🆕 Новый заказ',
            '№ ' . $order->number,
            'Сумма: ' . number_format($order->total, 0, ',', ' ') . ' ₽',
            'Email: ' . $order->email,
        ];
        if ($order->name) {
            $lines[] = 'Имя: ' . $order->name;
        }
        $productNames = $order->items->pluck('product_name')->filter()->unique()->values();
        if ($productNames->isNotEmpty()) {
            $lines[] = 'Товар: ' . $productNames->implode(', ');
        }
        return $this->send(implode("\n", $lines));
    }

    /**
     * Уведомление об оплате заказа.
     */
    public function notifyOrderPaid(\App\Models\Order $order): bool
    {
        $order->loadMissing('items');
        $lines = [
            '✅ Заказ оплачен',
            '№ ' . $order->number,
            'Сумма: ' . number_format($order->total, 0, ',', ' ') . ' ₽',
            'Email: ' . $order->email,
        ];
        $productNames = $order->items->pluck('product_name')->filter()->unique()->values();
        if ($productNames->isNotEmpty()) {
            $lines[] = 'Товар: ' . $productNames->implode(', ');
        }
        return $this->send(implode("\n", $lines));
    }
}

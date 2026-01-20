<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class DeleteTestOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:delete-test 
                            {--email= : Удалить заказы с указанным email}
                            {--status= : Удалить заказы с указанным статусом (pending, processing, completed)}
                            {--payment_status= : Удалить заказы с указанным статусом оплаты (pending, paid)}
                            {--date= : Удалить заказы до указанной даты (формат: Y-m-d, например: 2026-01-20)}
                            {--all : Удалить ВСЕ заказы (требует подтверждения)}
                            {--force : Принудительное удаление без подтверждения}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Удаление тестовых заказов по различным критериям';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $query = Order::query();
        $conditions = [];
        
        // Сбор условий
        if ($email = $this->option('email')) {
            $query->where('email', $email);
            $conditions[] = "email = {$email}";
        }
        
        if ($status = $this->option('status')) {
            $query->where('status', $status);
            $conditions[] = "status = {$status}";
        }
        
        if ($paymentStatus = $this->option('payment_status')) {
            $query->where('payment_status', $paymentStatus);
            $conditions[] = "payment_status = {$paymentStatus}";
        }
        
        if ($date = $this->option('date')) {
            try {
                $dateObj = \Carbon\Carbon::parse($date);
                $query->where('created_at', '<=', $dateObj);
                $conditions[] = "created_at <= {$date}";
            } catch (\Exception $e) {
                $this->error("Некорректная дата: {$date}. Используйте формат Y-m-d (например: 2026-01-20)");
                return 1;
            }
        }
        
        // Если выбран --all без других условий
        if ($this->option('all') && empty($conditions)) {
            if (!$this->option('force')) {
                if (!$this->confirm('⚠️  ВНИМАНИЕ: Вы собираетесь удалить ВСЕ заказы! Продолжить?')) {
                    $this->info('Отменено');
                    return 0;
                }
            }
            
            $count = Order::count();
            Order::query()->delete();
            $this->info("✅ Удалено заказов: {$count}");
            return 0;
        }
        
        // Если нет условий - показать помощь
        if (empty($conditions)) {
            $this->info('Использование команды для удаления тестовых заказов:');
            $this->line('');
            $this->line('  Удалить по email:');
            $this->line('    php artisan orders:delete-test --email=test@example.com');
            $this->line('');
            $this->line('  Удалить неоплаченные заказы:');
            $this->line('    php artisan orders:delete-test --payment_status=pending');
            $this->line('');
            $this->line('  Удалить заказы до определенной даты:');
            $this->line('    php artisan orders:delete-test --date=2026-01-20');
            $this->line('');
            $this->line('  Удалить все заказы (с подтверждением):');
            $this->line('    php artisan orders:delete-test --all');
            $this->line('');
            $this->line('  Комбинация условий:');
            $this->line('    php artisan orders:delete-test --email=test@example.com --payment_status=pending');
            return 0;
        }
        
        // Подсчитываем количество заказов для удаления
        $count = $query->count();
        
        if ($count === 0) {
            $this->warn('Заказы не найдены по указанным критериям');
            return 0;
        }
        
        // Показываем информацию о заказах
        $this->info("Найдено заказов для удаления: {$count}");
        $this->line('Условия: ' . implode(', ', $conditions));
        $this->line('');
        
        // Показываем примеры заказов
        $sample = $query->take(5)->get();
        $this->table(
            ['Номер', 'Email', 'Сумма', 'Статус', 'Оплата', 'Дата'],
            $sample->map(function ($order) {
                return [
                    $order->number,
                    $order->email,
                    number_format($order->total, 0, ',', ' ') . ' ₽',
                    $order->status,
                    $order->payment_status,
                    $order->created_at->format('d.m.Y H:i'),
                ];
            })->toArray()
        );
        
        if ($count > 5) {
            $this->line("... и еще " . ($count - 5) . " заказов");
        }
        
        $this->line('');
        
        // Подтверждение
        if (!$this->option('force')) {
            if (!$this->confirm("Удалить эти {$count} заказов?")) {
                $this->info('Отменено');
                return 0;
            }
        }
        
        // Удаление
        $deleted = $query->delete();
        
        $this->info("✅ Успешно удалено заказов: {$deleted}");
        
        return 0;
    }
}

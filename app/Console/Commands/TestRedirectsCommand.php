<?php

namespace App\Console\Commands;

use App\Models\Redirect;
use Illuminate\Console\Command;

class TestRedirectsCommand extends Command
{
    protected $signature = 'redirects:test {url?}';
    protected $description = 'Проверка работы редиректов';

    public function handle()
    {
        $testUrl = $this->argument('url');
        
        if ($testUrl) {
            // Тестируем конкретный URL
            $path = trim($testUrl, '/');
            $this->info("Тестирование URL: {$path}");
            
            $redirect = Redirect::findRedirect($path);
            
            if ($redirect) {
                $this->info("✅ Редирект найден:");
                $this->line("   Старый URL: {$redirect->old_url}");
                $this->line("   Новый URL: {$redirect->new_url}");
                $this->line("   Код: {$redirect->status_code}");
                $this->line("   Активен: " . ($redirect->is_active ? 'Да' : 'Нет'));
                $this->line("   Использований: {$redirect->hits}");
            } else {
                $this->error("❌ Редирект не найден для URL: {$path}");
                
                // Показываем похожие редиректы
                $similar = Redirect::where('old_url', 'like', "%{$path}%")
                    ->orWhere('old_url', 'like', "%" . urlencode($path) . "%")
                    ->get();
                
                if ($similar->count() > 0) {
                    $this->warn("Похожие редиректы:");
                    foreach ($similar as $r) {
                        $this->line("   - {$r->old_url} -> {$r->new_url} (активен: " . ($r->is_active ? 'Да' : 'Нет') . ")");
                    }
                }
            }
        } else {
            // Показываем статистику
            $total = Redirect::count();
            $active = Redirect::where('is_active', true)->count();
            $inactive = Redirect::where('is_active', false)->count();
            
            $this->info("Статистика редиректов:");
            $this->line("   Всего: {$total}");
            $this->line("   Активных: {$active}");
            $this->line("   Неактивных: {$inactive}");
            
            if ($inactive > 0) {
                $this->warn("\nНеактивные редиректы:");
                $inactiveRedirects = Redirect::where('is_active', false)->get();
                foreach ($inactiveRedirects as $r) {
                    $this->line("   - {$r->old_url} -> {$r->new_url}");
                }
            }
            
            // Показываем последние добавленные редиректы
            $recent = Redirect::orderBy('created_at', 'desc')->limit(10)->get();
            if ($recent->count() > 0) {
                $this->info("\nПоследние добавленные редиректы:");
                foreach ($recent as $r) {
                    $status = $r->is_active ? '✅' : '❌';
                    $this->line("   {$status} {$r->old_url} -> {$r->new_url} (создан: {$r->created_at->format('d.m.Y H:i')})");
                }
            }
        }
        
        return 0;
    }
}

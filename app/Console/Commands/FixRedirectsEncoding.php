<?php

namespace App\Console\Commands;

use App\Models\Redirect;
use Illuminate\Console\Command;

class FixRedirectsEncoding extends Command
{
    protected $signature = 'redirects:fix-encoding {--import-file= : Путь к файлу redirects.json для переимпорта}';
    protected $description = 'Исправляет кодировку URL в редиректах - проверяет все редиректы и создает дубликаты с URL-encoded версиями если нужно, или переимпортирует из JSON';

    public function handle()
    {
        $importFile = $this->option('import-file');
        
        if ($importFile && file_exists($importFile)) {
            return $this->reimportRedirects($importFile);
        }
        
        $this->info('Проверка редиректов на URL-encoded кириллицу...');
        
        $redirects = Redirect::where('is_active', true)->get();
        $fixed = 0;
        $created = 0;
        
        foreach ($redirects as $redirect) {
            $oldUrl = $redirect->old_url;
            
            // Проверяем, содержит ли URL кириллицу, которая должна быть URL-encoded
            if (preg_match('/[а-яёА-ЯЁ]/u', $oldUrl)) {
                $encodedUrl = urlencode($oldUrl);
                $existingEncoded = Redirect::where('old_url', $encodedUrl)->first();
                
                if (!$existingEncoded) {
                    Redirect::create([
                        'old_url' => $encodedUrl,
                        'new_url' => $redirect->new_url,
                        'type' => $redirect->type,
                        'status_code' => $redirect->status_code,
                        'is_active' => true,
                    ]);
                    $created++;
                    $this->line("  Создан редирект: {$encodedUrl} -> {$redirect->new_url}");
                }
            }
            
            // Проверяем обратный случай: если URL уже закодирован, создаем декодированную версию
            if (strpos($oldUrl, '%') !== false && urldecode($oldUrl) !== $oldUrl) {
                $decodedUrl = urldecode($oldUrl);
                $existingDecoded = Redirect::where('old_url', $decodedUrl)->first();
                
                if (!$existingDecoded) {
                    Redirect::create([
                        'old_url' => $decodedUrl,
                        'new_url' => $redirect->new_url,
                        'type' => $redirect->type,
                        'status_code' => $redirect->status_code,
                        'is_active' => true,
                    ]);
                    $created++;
                    $this->line("  Создан редирект: {$decodedUrl} -> {$redirect->new_url}");
                }
            }
            
            $fixed++;
        }
        
        $this->info("Проверено редиректов: {$fixed}");
        $this->info("Создано дополнительных редиректов: {$created}");
        $this->info('Готово! Теперь редиректы будут работать с обоими вариантами URL (закодированным и декодированным).');
        
        if (!$importFile) {
            $this->info('');
            $this->comment('Для переимпорта редиректов из JSON используйте:');
            $this->comment('php artisan redirects:fix-encoding --import-file=путь/к/redirects.json');
        }
        
        return 0;
    }
    
    private function reimportRedirects(string $filePath): int
    {
        $this->info('Переимпорт редиректов из JSON...');
        
        $redirects = json_decode(file_get_contents($filePath), true) ?? [];
        
        if (empty($redirects)) {
            $this->error('Файл пуст или не содержит данных');
            return 1;
        }
        
        $count = 0;
        $created = 0;
        $updated = 0;
        
        foreach ($redirects as $redirect) {
            // Получаем путь из старого URL
            $oldUrl = parse_url($redirect['old_url'], PHP_URL_PATH) ?: $redirect['old_url'];
            $oldUrl = ltrim($oldUrl, '/');
            
            // ВАЖНО: НЕ декодируем URL - сохраняем как есть (может быть URL-encoded кириллица)
            // Это нужно для правильной работы редиректов со старыми WordPress URL
            
            // Определяем новый URL в зависимости от типа
            $newUrl = match($redirect['type'] ?? '') {
                'post' => '/articles/' . $redirect['slug'],
                'page' => '/' . $redirect['slug'],
                'product' => '/products/' . $redirect['slug'],
                'category' => '/category/' . $redirect['slug'],
                'tag' => '/tag/' . $redirect['slug'],
                'product_category' => '/category/' . $redirect['slug'],
                default => '/' . $redirect['slug'],
            };
            
            $existing = Redirect::where('old_url', $oldUrl)->first();
            
            if ($existing) {
                $existing->update([
                    'new_url' => $newUrl,
                    'type' => $redirect['type'],
                    'status_code' => 301,
                    'is_active' => true,
                ]);
                $updated++;
            } else {
                Redirect::create([
                    'old_url' => $oldUrl,
                    'new_url' => $newUrl,
                    'type' => $redirect['type'],
                    'status_code' => 301,
                    'is_active' => true,
                ]);
                $created++;
            }
            
            // Если URL содержит URL-encoded кириллицу, создаем также декодированную версию
            if (strpos($oldUrl, '%') !== false) {
                $decodedUrl = urldecode($oldUrl);
                if ($decodedUrl !== $oldUrl && !Redirect::where('old_url', $decodedUrl)->exists()) {
                    Redirect::create([
                        'old_url' => $decodedUrl,
                        'new_url' => $newUrl,
                        'type' => $redirect['type'],
                        'status_code' => 301,
                        'is_active' => true,
                    ]);
                    $created++;
                }
            }
            
            $count++;
        }
        
        $this->info("Обработано редиректов: {$count}");
        $this->info("Создано новых: {$created}");
        $this->info("Обновлено существующих: {$updated}");
        $this->info('Готово!');
        
        return 0;
    }
}

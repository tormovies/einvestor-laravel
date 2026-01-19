<?php

namespace App\Console\Commands;

use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class FixProductImagesInDescription extends Command
{
    protected $signature = 'products:fix-images-in-description 
                            {--dry-run : Показать что будет сделано без реальных изменений}
                            {--download : Скачать изображения с продакшена}
                            {--domain=einvestor.ru : Домен для замены localhost}';
    
    protected $description = 'Исправление URL изображений в описаниях товаров';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $download = $this->option('download');
        $domain = $this->option('domain');

        $this->info('🔍 Поиск товаров с изображениями в описаниях...');
        $this->newLine();

        // Находим товары с описаниями, содержащими ссылки на изображения
        $products = Product::whereNotNull('description')
            ->where(function($query) {
                $query->where('description', 'like', '%http://einvestor.ru%')
                      ->orWhere('description', 'like', '%https://einvestor.ru%')
                      ->orWhere('description', 'like', '%http://www.einvestor.ru%')
                      ->orWhere('description', 'like', '%https://www.einvestor.ru%')
                      ->orWhere('description', 'like', '%http://localhost%')
                      ->orWhere('description', 'like', '%http://127.0.0.1%')
                      ->orWhere('description', 'like', '%wp-content/uploads%');
            })
            ->get();

        if ($products->isEmpty()) {
            $this->info('✅ Товаров с изображениями в описаниях не найдено.');
            return 0;
        }

        $this->info("📦 Найдено товаров: {$products->count()}");
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  РЕЖИМ ПРОВЕРКИ (dry-run) - изменения не будут сохранены');
            $this->newLine();
        }

        // Убеждаемся, что папка существует
        Storage::disk('public')->makeDirectory('products/images');

        $updatedCount = 0;
        $errorCount = 0;
        $downloadedCount = 0;

        $progressBar = $this->output->createProgressBar($products->count());
        $progressBar->start();

        foreach ($products as $product) {
            try {
                $result = $this->fixProductDescription($product, $dryRun, $download, $domain);
                
                if ($result['updated']) {
                    $updatedCount++;
                }
                if ($result['downloaded'] > 0) {
                    $downloadedCount += $result['downloaded'];
                }
                if ($result['error']) {
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $errorCount++;
                if ($this->option('verbose')) {
                    $this->newLine();
                    $this->error("Ошибка при обработке товара ID {$product->id}: " . $e->getMessage());
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Итоги
        $this->info('📊 Результаты:');
        $this->table(
            ['Статус', 'Количество'],
            [
                ['✅ Обновлено товаров', $updatedCount],
                ['📥 Скачано изображений', $downloadedCount],
                ['❌ Ошибки', $errorCount],
                ['📦 Всего товаров', $products->count()],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->info('💡 Для реального обновления запустите команду без флага --dry-run');
        }

        return 0;
    }

    private function fixProductDescription(Product $product, bool $dryRun, bool $download, string $domain): array
    {
        $description = $product->description;
        $originalDescription = $description;
        $updated = false;
        $downloaded = 0;
        $error = false;

        // Собираем все URL изображений из разных источников
        $imageUrls = [];

        // 1. Паттерн для поиска всех img тегов с src
        $imgPattern = '/<img[^>]+src=["\']([^"\']+)["\'][^>]*>/i';
        preg_match_all($imgPattern, $description, $imgMatches);
        if (!empty($imgMatches[1])) {
            $imageUrls = array_merge($imageUrls, $imgMatches[1]);
        }

        // 2. Паттерн для поиска всех ссылок <a href="..."> с изображениями
        $linkPattern = '/<a[^>]+href=["\']([^"\']+)["\'][^>]*>/i';
        preg_match_all($linkPattern, $description, $linkMatches);
        if (!empty($linkMatches[1])) {
            foreach ($linkMatches[1] as $linkUrl) {
                // Проверяем, является ли это URL изображения
                if ($this->isImageUrl($linkUrl)) {
                    $imageUrls[] = $linkUrl;
                }
            }
        }

        // Убираем дубликаты
        $imageUrls = array_unique($imageUrls);

        if (empty($imageUrls)) {
            return ['updated' => false, 'downloaded' => 0, 'error' => false];
        }

        foreach ($imageUrls as $imageUrl) {
            // Пропускаем уже локальные пути
            if (str_starts_with($imageUrl, '/storage/') || str_starts_with($imageUrl, 'storage/')) {
                continue;
            }

            // Пропускаем, если это не URL изображения
            if (!$this->isImageUrl($imageUrl)) {
                continue;
            }

            // Извлекаем имя файла из URL
            $path = parse_url($imageUrl, PHP_URL_PATH);
            if (!$path) {
                continue;
            }

            $filename = basename($path);
            if (empty($filename)) {
                continue;
            }

            // Генерируем локальный путь (относительный)
            $localPath = 'products/images/' . $filename;
            $localUrl = '/storage/' . $localPath; // Используем относительный путь

            // Если нужно скачать изображение
            if ($download && !$dryRun) {
                // Заменяем localhost на реальный домен для скачивания
                $downloadUrl = $imageUrl;
                if (str_contains($downloadUrl, 'localhost') || str_contains($downloadUrl, '127.0.0.1')) {
                    $downloadUrl = str_replace(['http://localhost:8000', 'http://127.0.0.1:8000'], "https://{$domain}", $downloadUrl);
                    $downloadUrl = str_replace(['http://localhost', 'http://127.0.0.1'], "https://{$domain}", $downloadUrl);
                }

                // Скачиваем только если файл еще не существует
                if (!Storage::disk('public')->exists($localPath)) {
                    $imageContent = $this->downloadImage($downloadUrl);
                    if ($imageContent !== false) {
                        Storage::disk('public')->put($localPath, $imageContent);
                        $downloaded++;
                    }
                }
            }

            // Заменяем URL в описании (и в src, и в href) - используем относительный путь
            // Заменяем все варианты: полный URL, URL с портом, и т.д.
            $description = str_replace($imageUrl, $localUrl, $description);
            
            // Также заменяем варианты с localhost без порта и с портом
            $description = preg_replace(
                '/(src|href)=["\']http:\/\/localhost(:8000)?\/storage\/([^"\']+)["\']/i',
                '$1="' . $localUrl . '"',
                $description
            );
            
            $updated = true;
        }

        // Обновляем описание в базе данных
        if ($updated && !$dryRun) {
            $product->update(['description' => $description]);
        }

        return [
            'updated' => $updated,
            'downloaded' => $downloaded,
            'error' => $error
        ];
    }

    /**
     * Проверяет, является ли URL ссылкой на изображение
     */
    private function isImageUrl(string $url): bool
    {
        // Проверяем расширение файла
        $path = parse_url($url, PHP_URL_PATH);
        if ($path) {
            $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $imageExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp'];
            if (in_array($extension, $imageExtensions)) {
                return true;
            }
        }

        // Проверяем, содержит ли URL wp-content/uploads (типичный путь WordPress для изображений)
        if (str_contains($url, 'wp-content/uploads')) {
            return true;
        }

        return false;
    }

    private function downloadImage(string $url): string|false
    {
        try {
            // Пробуем через HTTP клиент
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'image/*,*/*',
                ])
                ->get($url);
            
            if ($response->successful()) {
                $content = $response->body();
                if (strlen($content) > 0) {
                    return $content;
                }
            }

            // Если HTTP клиент не сработал, пробуем file_get_contents
            if (ini_get('allow_url_fopen')) {
                $context = stream_context_create([
                    'http' => [
                        'method' => 'GET',
                        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n",
                        'timeout' => 30,
                    ],
                ]);
                
                $content = @file_get_contents($url, false, $context);
                if ($content !== false && strlen($content) > 0) {
                    return $content;
                }
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MigrateMediaImages extends Command
{
    protected $signature = 'media:migrate-images 
                            {--dry-run : Показать что будет сделано без реальных изменений}
                            {--limit= : Ограничить количество обрабатываемых изображений}
                            {--domain= : Заменить домен в URL (например: einvestor.ru)}
                            {--update-only : Только обновить URL в БД без скачивания файлов}';
    
    protected $description = 'Миграция изображений с внешних URL на локальное хранилище';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $limit = $this->option('limit') ? (int)$this->option('limit') : null;
        $domain = $this->option('domain');
        $updateOnly = $this->option('update-only');

        $this->info('🔍 Поиск изображений с внешними URL...');
        $this->newLine();

        // Находим все записи с внешними URL (начинаются с http:// или https://)
        $query = Media::where(function($q) {
            $q->where('url', 'like', 'http://%')
              ->orWhere('url', 'like', 'https://%');
        });

        // Если указан домен, фильтруем по нему
        if ($domain) {
            $query->where(function($q) use ($domain) {
                $q->where('url', 'like', "http://{$domain}%")
                  ->orWhere('url', 'like', "https://{$domain}%")
                  ->orWhere('url', 'like', "http://www.{$domain}%")
                  ->orWhere('url', 'like', "https://www.{$domain}%");
            });
        }

        $mediaItems = $query;

        if ($limit) {
            $mediaItems = $mediaItems->limit($limit);
        }

        $mediaItems = $mediaItems->get();

        if ($mediaItems->isEmpty()) {
            $this->info('✅ Изображений с внешними URL не найдено.');
            return 0;
        }

        $this->info("📦 Найдено изображений: {$mediaItems->count()}");
        $this->newLine();

        if ($dryRun) {
            $this->warn('⚠️  РЕЖИМ ПРОВЕРКИ (dry-run) - изменения не будут сохранены');
            $this->newLine();
        }

        // Убеждаемся, что папка существует
        Storage::disk('public')->makeDirectory('products/images');

        $successCount = 0;
        $errorCount = 0;
        $skippedCount = 0;

        $progressBar = $this->output->createProgressBar($mediaItems->count());
        $progressBar->start();

        foreach ($mediaItems as $media) {
            try {
                if ($updateOnly) {
                    $result = $this->updateUrlOnly($media, $dryRun);
                } else {
                    $result = $this->migrateImage($media, $dryRun);
                }
                
                if ($result === 'success') {
                    $successCount++;
                } elseif ($result === 'skipped') {
                    $skippedCount++;
                } else {
                    $errorCount++;
                }
            } catch (\Exception $e) {
                $errorCount++;
                if ($this->option('verbose') || !$dryRun) {
                    $this->newLine();
                    $this->error("Ошибка при обработке ID {$media->id} ({$media->url}): " . $e->getMessage());
                }
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Итоги
        $this->info('📊 Результаты миграции:');
        $this->table(
            ['Статус', 'Количество'],
            [
                ['✅ Успешно', $successCount],
                ['⏭️  Пропущено', $skippedCount],
                ['❌ Ошибки', $errorCount],
                ['📦 Всего', $mediaItems->count()],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->info('💡 Для реальной миграции запустите команду без флага --dry-run');
        }

        return 0;
    }

    private function migrateImage(Media $media, bool $dryRun): string
    {
        $externalUrl = $media->url;
        $domain = $this->option('domain');

        // Пропускаем, если это уже локальный путь
        if (!str_starts_with($externalUrl, 'http://') && !str_starts_with($externalUrl, 'https://')) {
            return 'skipped';
        }

        // Если указан домен для замены, заменяем localhost на реальный домен
        if ($domain && (str_contains($externalUrl, 'localhost') || str_contains($externalUrl, '127.0.0.1'))) {
            $externalUrl = str_replace(['http://localhost:8000', 'http://127.0.0.1:8000'], "https://{$domain}", $externalUrl);
            $externalUrl = str_replace(['http://localhost', 'http://127.0.0.1'], "https://{$domain}", $externalUrl);
        }

        // Определяем расширение файла
        $extension = $this->getFileExtension($externalUrl, $media->mime_type);
        
        // Генерируем имя файла
        $filename = $this->generateFilename($media, $extension);
        $localPath = 'products/images/' . $filename;

        // Проверяем, не существует ли уже этот файл
        if (Storage::disk('public')->exists($localPath)) {
            if (!$dryRun) {
                // Файл уже существует, обновляем только URL в базе
                $this->updateMediaUrl($media, $localPath);
            }
            return 'skipped';
        }

        if ($dryRun) {
            // В режиме проверки просто показываем что будет сделано
            return 'success';
        }

        // Скачиваем изображение
        try {
            if ($this->option('verbose')) {
                $this->line("  Скачивание: {$externalUrl}");
            }
            
            $imageContent = $this->downloadImage($externalUrl);
            
            if ($imageContent === false) {
                if ($this->option('verbose')) {
                    $this->warn("  Не удалось скачать изображение");
                }
                return 'error';
            }
            
            if ($this->option('verbose')) {
                $this->info("  Скачано: " . strlen($imageContent) . " байт");
            }

            // Сохраняем локально
            Storage::disk('public')->put($localPath, $imageContent);

            // Получаем информацию о файле
            $fileInfo = $this->getImageInfo($localPath);

            // Обновляем запись в базе данных
            $media->update([
                'path' => $localPath,
                'url' => Storage::disk('public')->url($localPath),
                'filename' => $filename,
                'size' => $fileInfo['size'] ?? $media->size,
                'width' => $fileInfo['width'] ?? $media->width,
                'height' => $fileInfo['height'] ?? $media->height,
            ]);

            return 'success';

        } catch (\Exception $e) {
            $this->newLine();
            $this->error("Ошибка при скачивании {$externalUrl}: " . $e->getMessage());
            return 'error';
        }
    }

    private function downloadImage(string $url): string|false
    {
        try {
            // Пробуем через HTTP клиент с правильными заголовками
            $response = Http::timeout(30)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                    'Accept' => 'image/*,*/*',
                ])
                ->get($url);
            
            if ($response->successful()) {
                $content = $response->body();
                // Проверяем, что это действительно изображение
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

            if ($this->option('verbose')) {
                $this->warn("  Не удалось скачать: HTTP статус или пустой ответ");
            }
            
            return false;
        } catch (\Exception $e) {
            if ($this->option('verbose')) {
                $this->warn("  Исключение: " . $e->getMessage());
            }
            return false;
        }
    }

    private function getFileExtension(string $url, ?string $mimeType): string
    {
        // Пробуем извлечь из URL
        $path = parse_url($url, PHP_URL_PATH);
        if ($path) {
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            if ($ext && in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg'])) {
                return strtolower($ext);
            }
        }

        // Пробуем из mime type
        if ($mimeType) {
            $mimeMap = [
                'image/jpeg' => 'jpg',
                'image/jpg' => 'jpg',
                'image/png' => 'png',
                'image/gif' => 'gif',
                'image/webp' => 'webp',
                'image/svg+xml' => 'svg',
            ];
            if (isset($mimeMap[$mimeType])) {
                return $mimeMap[$mimeType];
            }
        }

        // По умолчанию jpg
        return 'jpg';
    }

    private function generateFilename(Media $media, string $extension): string
    {
        // Используем оригинальное имя файла, если есть
        if ($media->original_filename) {
            $name = pathinfo($media->original_filename, PATHINFO_FILENAME);
            $name = Str::slug($name);
        } else {
            // Или используем ID и случайную строку
            $name = 'img_' . $media->id . '_' . Str::random(8);
        }

        return $name . '.' . $extension;
    }

    private function getImageInfo(string $localPath): array
    {
        $fullPath = Storage::disk('public')->path($localPath);
        
        $info = [
            'size' => filesize($fullPath),
            'width' => null,
            'height' => null,
        ];

        // Получаем размеры изображения
        if (function_exists('getimagesize')) {
            $imageInfo = @getimagesize($fullPath);
            if ($imageInfo !== false) {
                $info['width'] = $imageInfo[0];
                $info['height'] = $imageInfo[1];
            }
        }

        return $info;
    }

    private function updateMediaUrl(Media $media, string $localPath): void
    {
        $media->update([
            'path' => $localPath,
            'url' => Storage::disk('public')->url($localPath),
        ]);
    }

    /**
     * Обновить только URL в БД, извлекая имя файла из внешнего URL
     */
    private function updateUrlOnly(Media $media, bool $dryRun): string
    {
        $externalUrl = $media->url;

        // Извлекаем имя файла из URL
        $path = parse_url($externalUrl, PHP_URL_PATH);
        if (!$path) {
            return 'error';
        }

        $filename = basename($path);
        if (empty($filename)) {
            return 'error';
        }

        // Генерируем локальный путь
        $localPath = 'products/images/' . $filename;

        if ($dryRun) {
            if ($this->option('verbose')) {
                $this->line("  Будет обновлено: {$externalUrl} -> /storage/{$localPath}");
            }
            return 'success';
        }

        // Обновляем запись в базе данных
        $media->update([
            'path' => $localPath,
            'url' => Storage::disk('public')->url($localPath),
            'filename' => $filename,
        ]);

        return 'success';
    }
}

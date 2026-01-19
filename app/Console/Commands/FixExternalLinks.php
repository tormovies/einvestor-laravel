<?php

namespace App\Console\Commands;

use App\Models\Product;
use App\Models\Post;
use App\Models\Page;
use App\Models\Category;
use App\Models\Tag;
use Illuminate\Console\Command;

class FixExternalLinks extends Command
{
    protected $signature = 'links:fix-external 
                            {--dry-run : Показать что будет сделано без реальных изменений}
                            {--domain=einvestor.ru : Домен для поиска ссылок}';
    
    protected $description = 'Исправление внешних ссылок на einvestor.ru на локальные ссылки';

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        $domain = $this->option('domain');

        $this->info('🔍 Поиск внешних ссылок на ' . $domain . '...');
        $this->newLine();

        $totalUpdated = 0;
        $totalLinks = 0;

        // Обрабатываем продукты
        $this->info('📦 Обработка товаров...');
        $products = Product::whereNotNull('description')
            ->orWhereNotNull('short_description')
            ->get();
        
        $productResult = $this->processModels($products, [
            'description' => 'description',
            'short_description' => 'short_description',
        ], $dryRun, $domain);
        $totalUpdated += $productResult['updated'];
        $totalLinks += $productResult['links'];

        // Обрабатываем статьи
        $this->info('📝 Обработка статей...');
        $posts = Post::whereNotNull('content')
            ->orWhereNotNull('excerpt')
            ->get();
        
        $postResult = $this->processModels($posts, [
            'content' => 'content',
            'excerpt' => 'excerpt',
        ], $dryRun, $domain);
        $totalUpdated += $postResult['updated'];
        $totalLinks += $postResult['links'];

        // Обрабатываем страницы
        $this->info('📄 Обработка страниц...');
        $pages = Page::whereNotNull('content')
            ->orWhereNotNull('excerpt')
            ->get();
        
        $pageResult = $this->processModels($pages, [
            'content' => 'content',
            'excerpt' => 'excerpt',
        ], $dryRun, $domain);
        $totalUpdated += $pageResult['updated'];
        $totalLinks += $pageResult['links'];

        // Обрабатываем категории
        $this->info('📂 Обработка категорий...');
        $categories = Category::whereNotNull('description')->get();
        
        $categoryResult = $this->processModels($categories, [
            'description' => 'description',
        ], $dryRun, $domain);
        $totalUpdated += $categoryResult['updated'];
        $totalLinks += $categoryResult['links'];

        $this->newLine();
        $this->info('📊 Результаты:');
        $this->table(
            ['Статус', 'Количество'],
            [
                ['✅ Обновлено записей', $totalUpdated],
                ['🔗 Найдено ссылок', $totalLinks],
            ]
        );

        if ($dryRun) {
            $this->newLine();
            $this->info('💡 Для реального обновления запустите команду без флага --dry-run');
        }

        return 0;
    }

    private function processModels($models, array $fields, bool $dryRun, string $domain): array
    {
        $updated = 0;
        $totalLinks = 0;

        foreach ($models as $model) {
            $changed = false;
            $data = [];

            foreach ($fields as $field => $dbField) {
                $content = $model->$dbField;
                if (empty($content)) {
                    continue;
                }

                // Ищем все ссылки на указанный домен
                $pattern = '/<a[^>]+href=["\'](https?:\/\/' . preg_quote($domain, '/') . '[^"\']+)["\'][^>]*>/i';
                preg_match_all($pattern, $content, $matches);

                if (!empty($matches[1])) {
                    $links = array_unique($matches[1]);
                    $totalLinks += count($links);

                    foreach ($links as $externalUrl) {
                        $localUrl = $this->convertToLocalUrl($externalUrl);
                        
                        if ($localUrl) {
                            // Заменяем URL в href
                            $content = str_replace($externalUrl, $localUrl, $content);
                            // Также заменяем в тексте ссылки, если там был полный URL
                            $content = str_replace($externalUrl, $localUrl, $content);
                            $changed = true;
                            
                            if ($this->option('verbose')) {
                                $this->line("  {$model->getTable()}#{$model->id} ({$dbField}): {$externalUrl} -> {$localUrl}");
                            }
                        } else {
                            if ($this->option('verbose')) {
                                $this->warn("  {$model->getTable()}#{$model->id} ({$dbField}): Не удалось найти локальный URL для {$externalUrl}");
                            }
                        }
                    }
                }

                if ($changed) {
                    $data[$dbField] = $content;
                }
            }

            if ($changed && !$dryRun) {
                $model->update($data);
                $updated++;
            } elseif ($changed) {
                $updated++;
            }
        }

        return ['updated' => $updated, 'links' => $totalLinks];
    }

    private function convertToLocalUrl(string $externalUrl): ?string
    {
        // Парсим URL
        $parsed = parse_url($externalUrl);
        if (!$parsed || !isset($parsed['path'])) {
            return null;
        }

        $path = $parsed['path'];
        
        // Декодируем URL-encoded путь (может быть несколько раз закодирован)
        while (str_contains($path, '%')) {
            $decoded = urldecode($path);
            if ($decoded === $path) {
                break; // Больше нечего декодировать
            }
            $path = $decoded;
        }
        
        // Убираем начальный и конечный слэш
        $path = trim($path, '/');
        
        // Убираем расширение .html если есть
        $path = preg_replace('/\.html$/', '', $path);
        
        // Убираем префиксы типа /product/, /post/, /page/ и т.д.
        $path = preg_replace('/^(product|post|page|category|tag)\//', '', $path);
        
        // Если путь все еще содержит URL-encoded символы, пробуем еще раз
        if (str_contains($path, '%')) {
            $path = urldecode($path);
        }

        // Пробуем найти продукт по slug
        $product = Product::where('slug', $path)->first();
        if ($product) {
            return '/products/' . $product->slug;
        }

        // Пробуем найти статью по slug
        $post = Post::where('slug', $path)->first();
        if ($post) {
            return '/articles/' . $post->slug;
        }

        // Пробуем найти страницу по slug
        $page = Page::where('slug', $path)->first();
        if ($page) {
            return '/' . $page->slug;
        }

        // Пробуем найти категорию по slug
        $category = Category::where('slug', $path)->first();
        if ($category) {
            return '/category/' . $category->slug;
        }

        // Пробуем найти тег по slug
        $tag = Tag::where('slug', $path)->first();
        if ($tag) {
            return '/tag/' . $tag->slug;
        }
        
        // Если не найдено по прямому slug, пробуем найти по частичному совпадению
        // (на случай, если slug немного отличается)
        $product = Product::where('slug', 'like', '%' . $path . '%')->first();
        if ($product) {
            return '/products/' . $product->slug;
        }
        
        $post = Post::where('slug', 'like', '%' . $path . '%')->first();
        if ($post) {
            return '/articles/' . $post->slug;
        }

        // Если ничего не найдено, возвращаем null
        return null;
    }
}

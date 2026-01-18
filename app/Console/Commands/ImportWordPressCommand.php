<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Media;
use App\Models\Page;
use App\Models\Post;
use App\Models\Product;
use App\Models\Redirect;
use App\Models\Tag;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ImportWordPressCommand extends Command
{
    protected $signature = 'import:wordpress {--path=../einvestor.ru/wordpress-export}';
    protected $description = 'Импорт данных из WordPress';

    private $categoryMap = [];
    private $tagMap = [];
    private $mediaMap = [];
    private $userMap = [];
    private $defaultUserId = null;

    public function handle()
    {
        $exportPath = $this->option('path');
        
        if (!is_dir($exportPath)) {
            $this->error("Папка экспорта не найдена: {$exportPath}");
            return 1;
        }

        $this->info('Начинаем импорт данных из WordPress...');
        $this->newLine();

        DB::transaction(function () use ($exportPath) {
            // 0. Создаем пользователя по умолчанию если его нет
            $this->createDefaultUser();
            
            // 1. Импорт категорий
            $this->importCategories($exportPath);
            
            // 2. Импорт тегов
            $this->importTags($exportPath);
            
            // 3. Импорт медиафайлов
            $this->importMedia($exportPath);
            
            // 4. Импорт постов
            $this->importPosts($exportPath);
            
            // 5. Импорт страниц
            $this->importPages($exportPath);
            
            // 6. Импорт товаров
            $this->importProducts($exportPath);
            
            // 7. Создание редиректов
            $this->createRedirects($exportPath);
        });

        $this->newLine();
        $this->info('✅ Импорт завершен успешно!');
        
        return 0;
    }

    private function createDefaultUser()
    {
        $user = \App\Models\User::firstOrCreate(
            ['email' => 'admin@einvestor.ru'],
            [
                'name' => 'Администратор',
                'password' => bcrypt('password'),
                'is_admin' => true,
            ]
        );
        
        // Убеждаемся, что пользователь является администратором
        if (!$user->is_admin) {
            $user->update(['is_admin' => true]);
        }
        
        $this->defaultUserId = $user->id;
        $this->info("Пользователь по умолчанию: ID {$this->defaultUserId} (Администратор)");
    }

    private function importCategories(string $exportPath)
    {
        $this->info('Импорт категорий...');
        
        $categories = json_decode(file_get_contents($exportPath . '/categories.json'), true) ?? [];
        $productCategories = json_decode(file_get_contents($exportPath . '/product_categories.json'), true) ?? [];
        
        // Импортируем категории постов
        foreach ($categories as $cat) {
            $category = Category::updateOrCreate(
                ['wp_id' => $cat['id']],
                [
                    'slug' => $cat['slug'],
                    'name' => $cat['name'],
                    'description' => $cat['description'] ?? null,
                    'type' => 'post',
                    'parent_id' => null,
                    'count' => $cat['count'] ?? 0,
                ]
            );
            
            $this->categoryMap[$cat['id']] = $category->id;
        }
        
        // Импортируем категории товаров (с уникальным slug если конфликт)
        foreach ($productCategories as $cat) {
            // Проверяем, не существует ли уже категория с таким slug
            $existing = Category::where('slug', $cat['slug'])->first();
            $slug = $cat['slug'];
            
            // Если существует и это не та же категория, добавляем префикс
            if ($existing && $existing->wp_id != $cat['id']) {
                $slug = 'product-' . $slug;
            }
            
            $category = Category::updateOrCreate(
                ['wp_id' => $cat['id']],
                [
                    'slug' => $slug,
                    'name' => $cat['name'],
                    'description' => $cat['description'] ?? null,
                    'type' => 'product',
                    'parent_id' => null,
                    'count' => $cat['count'] ?? 0,
                ]
            );
            
            $this->categoryMap[$cat['id']] = $category->id;
        }
        
        $this->info("Импортировано категорий: " . (count($categories) + count($productCategories)));
    }

    private function importTags(string $exportPath)
    {
        $this->info('Импорт тегов...');
        
        $tags = json_decode(file_get_contents($exportPath . '/tags.json'), true) ?? [];
        
        foreach ($tags as $tag) {
            $tagModel = Tag::updateOrCreate(
                ['wp_id' => $tag['id']],
                [
                    'slug' => $tag['slug'],
                    'name' => $tag['name'],
                    'description' => $tag['description'] ?? null,
                    'count' => $tag['count'] ?? 0,
                ]
            );
            
            $this->tagMap[$tag['id']] = $tagModel->id;
        }
        
        $this->info("Импортировано тегов: " . count($tags));
    }

    private function importMedia(string $exportPath)
    {
        $this->info('Импорт медиафайлов...');
        
        $media = json_decode(file_get_contents($exportPath . '/media.json'), true) ?? [];
        
        foreach ($media as $item) {
            $mediaModel = Media::updateOrCreate(
                ['wp_id' => $item['id']],
                [
                    'filename' => basename($item['file'] ?? $item['url']),
                    'original_filename' => $item['title'] ?? basename($item['file'] ?? $item['url']),
                    'path' => $item['file'] ?? '',
                    'url' => $item['url'],
                    'mime_type' => $item['mime_type'] ?? 'image/jpeg',
                    'size' => null,
                    'width' => null,
                    'height' => null,
                    'title' => $item['title'] ?? null,
                    'alt' => null,
                ]
            );
            
            $this->mediaMap[$item['id']] = $mediaModel->id;
        }
        
        $this->info("Импортировано медиафайлов: " . count($media));
    }

    private function importPosts(string $exportPath)
    {
        $this->info('Импорт постов...');
        
        $posts = json_decode(file_get_contents($exportPath . '/posts.json'), true) ?? [];
        
        foreach ($posts as $post) {
            $postModel = Post::updateOrCreate(
                ['wp_id' => $post['id']],
                [
                    'slug' => $post['slug'],
                    'title' => $post['title'],
                    'content' => $post['content'] ?? '',
                    'excerpt' => $post['excerpt'] ?? null,
                    'status' => $post['status'] ?? 'publish',
                    'author_id' => $this->defaultUserId,
                    'featured_image_id' => $this->getMediaId($post['featured_image'] ?? null),
                    'published_at' => $post['date'] ?? now(),
                ]
            );
            
            // Связи с категориями
            if (isset($post['categories']) && is_array($post['categories'])) {
                $categoryIds = [];
                foreach ($post['categories'] as $wpCatId) {
                    if (isset($this->categoryMap[$wpCatId])) {
                        $categoryIds[] = $this->categoryMap[$wpCatId];
                    }
                }
                $postModel->categories()->sync($categoryIds);
            }
            
            // Связи с тегами
            if (isset($post['tags']) && is_array($post['tags'])) {
                $tagIds = [];
                foreach ($post['tags'] as $wpTagId) {
                    if (isset($this->tagMap[$wpTagId])) {
                        $tagIds[] = $this->tagMap[$wpTagId];
                    }
                }
                $postModel->tags()->sync($tagIds);
            }
        }
        
        $this->info("Импортировано постов: " . count($posts));
    }

    private function importPages(string $exportPath)
    {
        $this->info('Импорт страниц...');
        
        $pages = json_decode(file_get_contents($exportPath . '/pages.json'), true) ?? [];
        
        foreach ($pages as $page) {
            Page::updateOrCreate(
                ['wp_id' => $page['id']],
                [
                    'slug' => $page['slug'],
                    'title' => $page['title'],
                    'content' => $page['content'] ?? '',
                    'excerpt' => $page['excerpt'] ?? null,
                    'status' => $page['status'] ?? 'publish',
                    'parent_id' => null, // TODO: обработать parent
                    'menu_order' => $page['menu_order'] ?? 0,
                    'author_id' => $this->defaultUserId,
                    'featured_image_id' => $this->getMediaId($page['featured_image'] ?? null),
                    'published_at' => $page['date'] ?? now(),
                ]
            );
        }
        
        $this->info("Импортировано страниц: " . count($pages));
    }

    private function importProducts(string $exportPath)
    {
        $this->info('Импорт товаров...');
        
        $products = json_decode(file_get_contents($exportPath . '/products.json'), true) ?? [];
        
        foreach ($products as $product) {
            // Обрабатываем SKU - если пустой или null, делаем уникальным
            $sku = $product['sku'] ?? null;
            if (empty($sku)) {
                $sku = 'PROD-' . $product['id'];
            }
            
            // Проверяем уникальность SKU
            $existingProduct = Product::where('sku', $sku)->where('wp_id', '!=', $product['id'])->first();
            if ($existingProduct) {
                $sku = 'PROD-' . $product['id'] . '-' . time();
            }
            
            $productModel = Product::updateOrCreate(
                ['wp_id' => $product['id']],
                [
                    'slug' => $product['slug'],
                    'name' => $product['title'],
                    'description' => $product['content'] ?? null,
                    'short_description' => $product['excerpt'] ?? null,
                    'price' => $product['price'] ?? 0,
                    'sku' => $sku,
                    'stock_status' => $this->mapStockStatus($product['stock_status'] ?? 'in_stock'),
                    'stock_quantity' => $product['stock_quantity'] ?? null,
                    'status' => $product['status'] ?? 'publish',
                    'featured_image_id' => $this->getMediaId($product['featured_image'] ?? null),
                    'file_path' => null, // TODO: извлечь из метаданных
                    'file_name' => null,
                    'file_size' => null,
                    'published_at' => $product['date'] ?? now(),
                ]
            );
            
            // Связи с категориями товаров
            if (isset($product['categories']) && is_array($product['categories'])) {
                $categoryIds = [];
                foreach ($product['categories'] as $wpCatId) {
                    if (isset($this->categoryMap[$wpCatId])) {
                        $categoryIds[] = $this->categoryMap[$wpCatId];
                    }
                }
                $productModel->categories()->sync($categoryIds);
            }
            
            // Связи с тегами
            if (isset($product['tags']) && is_array($product['tags'])) {
                $tagIds = [];
                foreach ($product['tags'] as $wpTagId) {
                    if (isset($this->tagMap[$wpTagId])) {
                        $tagIds[] = $this->tagMap[$wpTagId];
                    }
                }
                $productModel->tags()->sync($tagIds);
            }
        }
        
        $this->info("Импортировано товаров: " . count($products));
    }

    private function createRedirects(string $exportPath)
    {
        $this->info('Создание редиректов...');
        
        $redirects = json_decode(file_get_contents($exportPath . '/redirects.json'), true) ?? [];
        
        $count = 0;
        foreach ($redirects as $redirect) {
            $oldUrl = parse_url($redirect['old_url'], PHP_URL_PATH) ?: $redirect['old_url'];
            $oldUrl = ltrim($oldUrl, '/');
            
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
            
            Redirect::updateOrCreate(
                ['old_url' => $oldUrl],
                [
                    'new_url' => $newUrl,
                    'type' => $redirect['type'],
                    'status_code' => 301,
                    'is_active' => true,
                ]
            );
            
            $count++;
        }
        
        $this->info("Создано редиректов: {$count}");
    }

    private function mapStockStatus(string $status): string
    {
        return match(strtolower($status)) {
            'instock', 'in_stock' => 'in_stock',
            'outofstock', 'out_of_stock' => 'out_of_stock',
            'onbackorder', 'on_backorder' => 'on_backorder',
            default => 'in_stock',
        };
    }

    private function getMediaId(?int $wpMediaId): ?int
    {
        if (!$wpMediaId || !isset($this->mediaMap[$wpMediaId])) {
            return null;
        }
        
        // Проверяем, что media действительно существует
        $mediaId = $this->mediaMap[$wpMediaId];
        if (Media::where('id', $mediaId)->exists()) {
            return $mediaId;
        }
        
        return null;
    }
}

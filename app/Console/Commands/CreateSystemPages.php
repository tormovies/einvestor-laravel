<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\User;
use Illuminate\Console\Command;

class CreateSystemPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pages:create-system';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создать системные страницы (_home, _products_list, _articles_list) если их нет';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Получаем первого администратора или создаем заглушку
        $adminUser = User::where('is_admin', true)->first();
        $authorId = $adminUser ? $adminUser->id : null;

        $systemPages = [
            [
                'slug' => '_home',
                'title' => 'Главная страница',
                'content' => '',
                'description' => 'Системная страница для главной страницы сайта',
            ],
            [
                'slug' => '_products_list',
                'title' => 'Список товаров',
                'content' => '',
                'description' => 'Системная страница для страницы списка товаров',
            ],
            [
                'slug' => '_articles_list',
                'title' => 'Список статей',
                'content' => '',
                'description' => 'Системная страница для страницы списка статей',
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($systemPages as $pageData) {
            $existing = Page::where('slug', $pageData['slug'])->first();
            
            if ($existing) {
                $this->line("⚠️  Страница '{$pageData['slug']}' уже существует, пропускаем");
                $skipped++;
                continue;
            }

            Page::create([
                'slug' => $pageData['slug'],
                'title' => $pageData['title'],
                'content' => $pageData['content'],
                'excerpt' => null,
                'status' => 'publish',
                'parent_id' => null,
                'menu_order' => 0,
                'author_id' => $authorId,
                'featured_image_id' => null,
                'published_at' => now(),
                'seo_title' => null,
                'seo_description' => null,
                'seo_h1' => null,
                'seo_intro_text' => null,
            ]);

            $this->info("✅ Создана системная страница: {$pageData['slug']} - {$pageData['title']}");
            $created++;
        }

        $this->newLine();
        $this->info("Готово! Создано: {$created}, пропущено: {$skipped}");

        return 0;
    }
}

<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Создаем системные страницы если их нет
        $this->createSystemPages();
    }

    /**
     * Создать системные страницы (_home, _products_list, _articles_list)
     */
    private function createSystemPages(): void
    {
        $adminUser = User::where('is_admin', true)->first();
        $authorId = $adminUser ? $adminUser->id : null;

        $systemPages = [
            [
                'slug' => '_home',
                'title' => 'Главная страница',
                'content' => '',
            ],
            [
                'slug' => '_products_list',
                'title' => 'Список товаров',
                'content' => '',
            ],
            [
                'slug' => '_articles_list',
                'title' => 'Список статей',
                'content' => '',
            ],
        ];

        foreach ($systemPages as $pageData) {
            Page::firstOrCreate(
                ['slug' => $pageData['slug']],
                [
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
                ]
            );
        }
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Traits\CreatesRedirectOnDelete;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PageController extends Controller
{
    use CreatesRedirectOnDelete;
    /**
     * Список страниц
     */
    public function index(Request $request)
    {
        $query = Page::with('parent', 'featuredImage')
            ->orderBy('created_at', 'desc');

        // Фильтрация по статусу
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Поиск по названию
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        $pages = $query->paginate(20);

        return view('admin.pages.index', compact('pages'));
    }

    /**
     * Форма создания страницы
     */
    public function create()
    {
        $parentPages = Page::whereNull('parent_id')->orderBy('title')->get();

        return view('admin.pages.create', compact('parentPages'));
    }

    /**
     * Сохранение новой страницы
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:pages,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'status' => 'required|in:publish,draft,private',
            'parent_id' => 'nullable|exists:pages,id',
            'menu_order' => 'nullable|integer|min:0',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:320',
            'seo_h1' => 'nullable|string|max:255',
            'seo_intro_text' => 'nullable|string',
        ]);

        // Генерация slug, если не указан
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Проверка уникальности
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Page::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Установка даты публикации
        if ($validated['status'] === 'publish' && !$request->has('published_at')) {
            $validated['published_at'] = now();
        }

        // Установка menu_order по умолчанию
        if (!isset($validated['menu_order'])) {
            $validated['menu_order'] = 0;
        }

        // Обеспечиваем, что content не будет null (для NOT NULL ограничения)
        if (!isset($validated['content']) || $validated['content'] === null) {
            $validated['content'] = '';
        }

        // Установка author_id (текущий пользователь)
        $validated['author_id'] = auth()->id();

        Page::create($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Страница успешно создана');
    }

    /**
     * Форма редактирования страницы
     */
    public function edit($id)
    {
        $page = Page::findOrFail($id);
        $parentPages = Page::whereNull('parent_id')
            ->where('id', '!=', $id)
            ->orderBy('title')
            ->get();

        return view('admin.pages.edit', compact('page', 'parentPages'));
    }

    /**
     * Обновление страницы
     */
    public function update(Request $request, $id)
    {
        $page = Page::findOrFail($id);
        
        // Запрещаем изменение slug системных страниц
        $systemPages = ['_home', '_products_list', '_articles_list'];
        $isSystemPage = in_array($page->slug, $systemPages);
        
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => $isSystemPage ? 'prohibited' : 'nullable|string|max:255|unique:pages,slug,' . $id,
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'status' => 'required|in:publish,draft,private',
            'parent_id' => 'nullable|exists:pages,id',
            'menu_order' => 'nullable|integer|min:0',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:320',
            'seo_h1' => 'nullable|string|max:255',
            'seo_intro_text' => 'nullable|string',
        ]);

        // Генерация slug, если не указан
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Проверка уникальности
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Page::where('slug', $validated['slug'])->where('id', '!=', $id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Установка даты публикации
        if ($validated['status'] === 'publish' && !$page->published_at) {
            $validated['published_at'] = now();
        }

        // Установка menu_order по умолчанию
        if (!isset($validated['menu_order'])) {
            $validated['menu_order'] = 0;
        }

        // Обеспечиваем, что content не будет null (для NOT NULL ограничения)
        if (!isset($validated['content']) || $validated['content'] === null) {
            $validated['content'] = '';
        }
        
        // Для системных страниц не позволяем изменять slug
        if ($isSystemPage) {
            unset($validated['slug']);
        }

        $page->update($validated);

        return redirect()->route('admin.pages.index')
            ->with('success', 'Страница успешно обновлена');
    }

    /**
     * Удаление страницы
     */
    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        
        // Запрещаем удаление системных страниц
        $systemPages = ['_home', '_products_list', '_articles_list'];
        if (in_array($page->slug, $systemPages)) {
            return redirect()->route('admin.pages.index')
                ->with('error', 'Нельзя удалить системную страницу');
        }
        
        // Получаем URL страницы для редиректа (до удаления)
        $oldUrl = $this->getUrlForRedirect($page);
        
        // Удаляем страницу
        $page->delete();
        
        // Создаем или обновляем редирект на главную страницу
        $redirect = $this->createRedirectToHome($oldUrl);
        
        $message = 'Страница успешно удалена';
        if ($redirect) {
            $message .= '. Создан редирект 301: ' . $oldUrl . ' → /';
        }

        return redirect()->route('admin.pages.index')
            ->with('success', $message);
    }
}

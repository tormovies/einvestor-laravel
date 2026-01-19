<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    /**
     * Список категорий
     */
    public function index(Request $request)
    {
        $query = Category::with('parent')
            ->orderBy('type')
            ->orderBy('name');

        // Фильтрация по типу
        if ($request->has('type') && $request->type) {
            $query->where('type', $request->type);
        }

        // Поиск по названию
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->paginate(30);

        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Форма создания категории
     */
    public function create()
    {
        $parentCategories = Category::whereNull('parent_id')->orderBy('name')->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Сохранение новой категории
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug',
            'description' => 'nullable|string',
            'type' => 'required|in:post,product',
            'parent_id' => 'nullable|exists:categories,id',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:320',
            'seo_h1' => 'nullable|string|max:255',
            'seo_intro_text' => 'nullable|string',
        ]);

        // Генерация slug, если не указан
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            
            // Проверка уникальности
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Category::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        Category::create($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Категория успешно создана');
    }

    /**
     * Форма редактирования категории
     */
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        $parentCategories = Category::whereNull('parent_id')
            ->where('id', '!=', $id)
            ->where('type', $category->type)
            ->orderBy('name')
            ->get();

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    /**
     * Обновление категории
     */
    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:categories,slug,' . $id,
            'description' => 'nullable|string',
            'type' => 'required|in:post,product',
            'parent_id' => 'nullable|exists:categories,id',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:320',
            'seo_h1' => 'nullable|string|max:255',
            'seo_intro_text' => 'nullable|string',
        ]);

        // Генерация slug, если не указан
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            
            // Проверка уникальности
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Category::where('slug', $validated['slug'])->where('id', '!=', $id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $category->update($validated);

        return redirect()->route('admin.categories.index')
            ->with('success', 'Категория успешно обновлена');
    }

    /**
     * Удаление категории
     */
    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        
        // Проверка на наличие дочерних категорий
        if ($category->children()->count() > 0) {
            return redirect()->route('admin.categories.index')
                ->with('error', 'Нельзя удалить категорию с дочерними категориями');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')
            ->with('success', 'Категория успешно удалена');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    /**
     * Список тегов
     */
    public function index(Request $request)
    {
        $query = Tag::orderBy('name');

        // Поиск по названию
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        $tags = $query->paginate(30);

        return view('admin.tags.index', compact('tags'));
    }

    /**
     * Форма создания тега
     */
    public function create()
    {
        return view('admin.tags.create');
    }

    /**
     * Сохранение нового тега
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tags,slug',
            'description' => 'nullable|string',
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
            while (Tag::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        Tag::create($validated);

        return redirect()->route('admin.tags.index')
            ->with('success', 'Тег успешно создан');
    }

    /**
     * Форма редактирования тега
     */
    public function edit($id)
    {
        $tag = Tag::findOrFail($id);

        return view('admin.tags.edit', compact('tag'));
    }

    /**
     * Обновление тега
     */
    public function update(Request $request, $id)
    {
        $tag = Tag::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:tags,slug,' . $id,
            'description' => 'nullable|string',
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
            while (Tag::where('slug', $validated['slug'])->where('id', '!=', $id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $tag->update($validated);

        return redirect()->route('admin.tags.index')
            ->with('success', 'Тег успешно обновлен');
    }

    /**
     * Удаление тега
     */
    public function destroy($id)
    {
        $tag = Tag::findOrFail($id);
        $tag->delete();

        return redirect()->route('admin.tags.index')
            ->with('success', 'Тег успешно удален');
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * Список постов
     */
    public function index(Request $request)
    {
        $query = Post::with('categories', 'tags', 'featuredImage')
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

        $posts = $query->paginate(20);

        return view('admin.posts.index', compact('posts'));
    }

    /**
     * Форма создания поста
     */
    public function create()
    {
        $categories = Category::where('type', 'post')->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.create', compact('categories', 'tags'));
    }

    /**
     * Сохранение нового поста
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'status' => 'required|in:publish,draft',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Генерация slug, если не указан
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Проверка уникальности
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Post::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Установка даты публикации
        if ($validated['status'] === 'publish' && !$request->has('published_at')) {
            $validated['published_at'] = now();
        }

        // Сохранение поста
        $categories = $validated['categories'] ?? [];
        $tags = $validated['tags'] ?? [];
        unset($validated['categories'], $validated['tags']);

        $post = Post::create($validated);

        // Привязка категорий и тегов
        if (!empty($categories)) {
            $post->categories()->sync($categories);
        }
        if (!empty($tags)) {
            $post->tags()->sync($tags);
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Пост успешно создан');
    }

    /**
     * Детали поста
     */
    public function show($id)
    {
        $post = Post::with('categories', 'tags', 'featuredImage', 'comments')
            ->findOrFail($id);

        return view('admin.posts.show', compact('post'));
    }

    /**
     * Форма редактирования поста
     */
    public function edit($id)
    {
        $post = Post::with('categories', 'tags')->findOrFail($id);
        $categories = Category::where('type', 'post')->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.posts.edit', compact('post', 'categories', 'tags'));
    }

    /**
     * Обновление поста
     */
    public function update(Request $request, $id)
    {
        $post = Post::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:posts,slug,' . $id,
            'excerpt' => 'nullable|string|max:500',
            'content' => 'nullable|string',
            'status' => 'required|in:publish,draft',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Генерация slug, если не указан
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['title']);
            
            // Проверка уникальности
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Post::where('slug', $validated['slug'])->where('id', '!=', $id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Установка даты публикации
        if ($validated['status'] === 'publish' && !$post->published_at) {
            $validated['published_at'] = now();
        }

        // Обновление поста
        $categories = $validated['categories'] ?? [];
        $tags = $validated['tags'] ?? [];
        unset($validated['categories'], $validated['tags']);

        $post->update($validated);

        // Синхронизация категорий и тегов
        $post->categories()->sync($categories);
        $post->tags()->sync($tags);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Пост успешно обновлен');
    }

    /**
     * Удаление поста
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('admin.posts.index')
            ->with('success', 'Пост успешно удален');
    }
}

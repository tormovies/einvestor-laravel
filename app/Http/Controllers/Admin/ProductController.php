<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Список товаров
     */
    public function index(Request $request)
    {
        $query = Product::with('categories', 'featuredImage')
            ->orderBy('created_at', 'desc');

        // Фильтрация по статусу
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Поиск по названию или SKU
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('sku', 'like', "%{$search}%");
            });
        }

        $products = $query->paginate(20);

        return view('admin.products.index', compact('products'));
    }

    /**
     * Форма создания товара
     */
    public function create()
    {
        $categories = Category::where('type', 'product')->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.products.create', compact('categories', 'tags'));
    }

    /**
     * Сохранение нового товара
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'stock_status' => 'required|in:in_stock,out_of_stock',
            'stock_quantity' => 'nullable|integer|min:0',
            'status' => 'required|in:publish,draft',
            'file' => 'nullable|file|max:10240', // 10MB max
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Генерация slug, если не указан
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            
            // Проверка уникальности
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Product::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Загрузка файла
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('products', 'local');
            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
        }

        // Установка даты публикации
        if ($validated['status'] === 'publish' && !$request->has('published_at')) {
            $validated['published_at'] = now();
        }

        // Сохранение товара
        $categories = $validated['categories'] ?? [];
        $tags = $validated['tags'] ?? [];
        unset($validated['categories'], $validated['tags'], $validated['file']);

        $product = Product::create($validated);

        // Привязка категорий и тегов
        if (!empty($categories)) {
            $product->categories()->sync($categories);
        }
        if (!empty($tags)) {
            $product->tags()->sync($tags);
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Товар успешно создан');
    }

    /**
     * Детали товара
     */
    public function show($id)
    {
        $product = Product::with('categories', 'tags', 'featuredImage', 'orderItems')
            ->findOrFail($id);

        return view('admin.products.show', compact('product'));
    }

    /**
     * Форма редактирования товара
     */
    public function edit($id)
    {
        $product = Product::with('categories', 'tags')->findOrFail($id);
        $categories = Category::where('type', 'product')->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'tags'));
    }

    /**
     * Обновление товара
     */
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $id,
            'description' => 'nullable|string',
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $id,
            'stock_status' => 'required|in:in_stock,out_of_stock',
            'stock_quantity' => 'nullable|integer|min:0',
            'status' => 'required|in:publish,draft',
            'file' => 'nullable|file|max:10240',
            'categories' => 'nullable|array',
            'categories.*' => 'exists:categories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:tags,id',
        ]);

        // Генерация slug, если не указан
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
            
            // Проверка уникальности
            $originalSlug = $validated['slug'];
            $counter = 1;
            while (Product::where('slug', $validated['slug'])->where('id', '!=', $id)->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Загрузка нового файла (старый удаляется)
        if ($request->hasFile('file')) {
            // Удаление старого файла
            if ($product->file_path && Storage::disk('local')->exists($product->file_path)) {
                Storage::disk('local')->delete($product->file_path);
            }

            $file = $request->file('file');
            $path = $file->store('products', 'local');
            $validated['file_path'] = $path;
            $validated['file_name'] = $file->getClientOriginalName();
            $validated['file_size'] = $file->getSize();
        }

        // Установка даты публикации
        if ($validated['status'] === 'publish' && !$product->published_at) {
            $validated['published_at'] = now();
        }

        // Обновление товара
        $categories = $validated['categories'] ?? [];
        $tags = $validated['tags'] ?? [];
        unset($validated['categories'], $validated['tags'], $validated['file']);

        $product->update($validated);

        // Синхронизация категорий и тегов
        $product->categories()->sync($categories);
        $product->tags()->sync($tags);

        return redirect()->route('admin.products.index')
            ->with('success', 'Товар успешно обновлен');
    }

    /**
     * Удаление товара
     */
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        // Удаление файла
        if ($product->file_path && Storage::disk('local')->exists($product->file_path)) {
            Storage::disk('local')->delete($product->file_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Товар успешно удален');
    }
}

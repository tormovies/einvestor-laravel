<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Media;
use App\Models\Product;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
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
            'description' => 'nullable|string|max:65535', // TEXT field max size
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'stock_status' => 'required|in:in_stock,out_of_stock',
            'stock_quantity' => 'nullable|integer|min:0',
            'status' => 'required|in:publish,draft',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
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

        // Загрузка изображения
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = 'products/images/' . $filename;
            
            // Убеждаемся, что папка существует
            Storage::disk('public')->makeDirectory('products/images');
            
            // Сохранение изображения
            Storage::disk('public')->put($path, file_get_contents($image));
            
            // Получение размеров изображения
            $imageInfo = getimagesize($image->getRealPath());
            $width = $imageInfo[0] ?? null;
            $height = $imageInfo[1] ?? null;
            
            // Создание записи в media
            // Сохраняем относительный путь в url, аксессор image_url сгенерирует правильный URL
            $media = Media::create([
                'filename' => $filename,
                'original_filename' => $image->getClientOriginalName(),
                'path' => $path,
                'url' => '/storage/' . $path, // Сохраняем относительный путь
                'mime_type' => $image->getMimeType(),
                'size' => $image->getSize(),
                'width' => $width,
                'height' => $height,
                'title' => $validated['name'],
                'alt' => $validated['name'],
            ]);
            
            $validated['featured_image_id'] = $media->id;
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

        // Обработка base64 изображений в описании
        if (isset($validated['description']) && !empty($validated['description'])) {
            \Log::info('Processing description for base64 images (create)', [
                'description_length' => strlen($validated['description']),
                'has_data_image' => strpos($validated['description'], 'data:image') !== false,
            ]);
            $validated['description'] = $this->processBase64Images($validated['description'], $validated['name'] ?? 'Product');
        }

        // Сохранение товара
        $categories = $validated['categories'] ?? [];
        $tags = $validated['tags'] ?? [];
        unset($validated['categories'], $validated['tags'], $validated['file'], $validated['image']);

        try {
            $product = Product::create($validated);
        } catch (\Exception $e) {
            \Log::error('Error creating product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->withErrors(['error' => 'Ошибка при создании товара: ' . $e->getMessage()])->withInput();
        }
        
        // Перезагружаем связи для корректного отображения
        $product->refresh();
        $product->load('featuredImage');

        // Привязка категорий и тегов
        if (!empty($categories)) {
            $product->categories()->sync($categories);
        }
        if (!empty($tags)) {
            // Фильтруем только существующие ID тегов
            $existingTagIds = Tag::whereIn('id', $tags)->pluck('id')->toArray();
            $product->tags()->sync($existingTagIds);
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
        $product = Product::with('categories', 'tags', 'featuredImage')->findOrFail($id);
        
        \Log::info('Edit form loaded', [
            'product_id' => $product->id,
            'featured_image_id' => $product->featured_image_id,
            'has_featured_image' => $product->featuredImage !== null,
            'featured_image_url' => $product->featuredImage ? $product->featuredImage->image_url : null,
        ]);
        
        $categories = Category::where('type', 'product')->orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('admin.products.edit', compact('product', 'categories', 'tags'));
    }

    /**
     * Обновление товара
     */
    public function update(Request $request, $id)
    {
        $product = Product::with('categories', 'tags', 'featuredImage')->findOrFail($id);

        // Логирование входящих данных (временно для отладки)
        \Log::info('Update request received', [
            'product_id' => $id,
            'has_description' => $request->has('description'),
            'description_length' => strlen($request->input('description', '')),
            'has_image' => $request->hasFile('image'),
        ]);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . $id,
            'description' => 'nullable|string|max:65535', // TEXT field max size
            'short_description' => 'nullable|string|max:500',
            'price' => 'required|numeric|min:0',
            'sku' => 'nullable|string|max:100|unique:products,sku,' . $id,
            'stock_status' => 'required|in:in_stock,out_of_stock',
            'stock_quantity' => 'nullable|integer|min:0',
            'status' => 'required|in:publish,draft',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
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

        // Загрузка нового изображения (старое удаляется)
        if ($request->hasFile('image')) {
            \Log::info('Image file detected', [
                'file_name' => $request->file('image')->getClientOriginalName(),
                'file_size' => $request->file('image')->getSize(),
            ]);

            // Удаление старого изображения
            if ($product->featured_image_id) {
                $oldMedia = Media::find($product->featured_image_id);
                if ($oldMedia && Storage::disk('public')->exists($oldMedia->path)) {
                    Storage::disk('public')->delete($oldMedia->path);
                }
                if ($oldMedia) {
                    $oldMedia->delete();
                }
            }

            $image = $request->file('image');
            $filename = time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
            $path = 'products/images/' . $filename;
            
            // Убеждаемся, что папка существует
            Storage::disk('public')->makeDirectory('products/images');
            
            // Сохранение изображения
            $saved = Storage::disk('public')->put($path, file_get_contents($image));
            
            \Log::info('Image save attempt', [
                'path' => $path,
                'saved' => $saved,
                'file_exists' => Storage::disk('public')->exists($path),
            ]);
            
            if (!$saved) {
                \Log::error('Failed to save image file', ['path' => $path]);
                return back()->withErrors(['image' => 'Не удалось сохранить изображение'])->withInput();
            }
            
            // Получение размеров изображения
            $imageInfo = getimagesize($image->getRealPath());
            $width = $imageInfo[0] ?? null;
            $height = $imageInfo[1] ?? null;
            
            // Создание записи в media
            // Сохраняем относительный путь в url, аксессор image_url сгенерирует правильный URL
            $media = Media::create([
                'filename' => $filename,
                'original_filename' => $image->getClientOriginalName(),
                'path' => $path,
                'url' => '/storage/' . $path, // Сохраняем относительный путь
                'mime_type' => $image->getMimeType(),
                'size' => $image->getSize(),
                'width' => $width,
                'height' => $height,
                'title' => $validated['name'],
                'alt' => $validated['name'],
            ]);
            
            \Log::info('Media record created', [
                'media_id' => $media->id,
                'path' => $media->path,
                'url' => $media->url,
            ]);
            
            $validated['featured_image_id'] = $media->id;
            
            \Log::info('Featured image ID set', [
                'featured_image_id' => $validated['featured_image_id'],
            ]);
        } else {
            \Log::info('No image file in request');
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
        
        // Удаляем поля, которые не должны попасть в update
        // НО НЕ удаляем description - оно должно сохраниться!
        unset($validated['categories'], $validated['tags'], $validated['file'], $validated['image']);

        // Обработка base64 изображений в описании
        if (isset($validated['description']) && !empty($validated['description'])) {
            \Log::info('Processing description for base64 images', [
                'product_id' => $product->id,
                'description_length' => strlen($validated['description']),
                'has_data_image' => strpos($validated['description'], 'data:image') !== false,
            ]);
            $validated['description'] = $this->processBase64Images($validated['description'], $product->name ?? 'Product');
        }

        // Проверка размера description перед сохранением
        if (isset($validated['description']) && strlen($validated['description']) > 65535) {
            \Log::warning('Description too long', [
                'product_id' => $product->id,
                'description_length' => strlen($validated['description']),
            ]);
            // Обрезаем до максимального размера
            $validated['description'] = mb_substr($validated['description'], 0, 65535);
        }

        // Логирование для отладки (временно)
        \Log::info('Updating product', [
            'product_id' => $product->id,
            'description_length' => strlen($validated['description'] ?? ''),
            'has_featured_image_id' => isset($validated['featured_image_id']),
            'featured_image_id' => $validated['featured_image_id'] ?? null,
            'validated_keys' => array_keys($validated),
        ]);

        // Обновляем товар (featured_image_id должен быть в validated, если изображение загружено)
        // description также должно быть в validated
        try {
            $updated = $product->update($validated);
        } catch (\Exception $e) {
            \Log::error('Error updating product', [
                'product_id' => $product->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            return back()->withErrors(['error' => 'Ошибка при сохранении товара: ' . $e->getMessage()])->withInput();
        }
        
        \Log::info('Product update result', [
            'updated' => $updated,
            'product_featured_image_id_after' => $product->featured_image_id,
        ]);
        
        // Перезагружаем связи для корректного отображения
        $product->refresh();
        $product->load('featuredImage', 'categories', 'tags');
        
        \Log::info('Product after refresh', [
            'product_featured_image_id' => $product->featured_image_id,
            'has_featured_image' => $product->featuredImage !== null,
        ]);

        // Синхронизация категорий и тегов
        if (!empty($categories)) {
            $product->categories()->sync($categories);
        }
        if (!empty($tags)) {
            // Фильтруем только существующие ID тегов
            $existingTagIds = Tag::whereIn('id', $tags)->pluck('id')->toArray();
            $product->tags()->sync($existingTagIds);
        } else {
            $product->tags()->sync([]);
        }

        return redirect()->route('admin.products.edit', $product->id)
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

    /**
     * Создание нового тега через AJAX
     */
    public function createTag(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        // Проверяем, существует ли тег с таким именем
        $tag = Tag::where('name', $validated['name'])->first();
        
        if ($tag) {
            return response()->json([
                'success' => true,
                'tag' => [
                    'id' => $tag->id,
                    'name' => $tag->name,
                ],
                'message' => 'Тег уже существует'
            ]);
        }

        // Создаем новый тег
        $slug = Str::slug($validated['name']);
        $originalSlug = $slug;
        $counter = 1;
        while (Tag::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $tag = Tag::create([
            'name' => $validated['name'],
            'slug' => $slug,
        ]);

        return response()->json([
            'success' => true,
            'tag' => [
                'id' => $tag->id,
                'name' => $tag->name,
            ],
            'message' => 'Тег успешно создан'
        ]);
    }

    /**
     * Обработка base64 изображений в описании
     * Конвертирует data:image/... в файлы и заменяет в HTML
     */
    private function processBase64Images(string $html, string $productName = 'Product'): string
    {
        // Улучшенный паттерн для поиска base64 изображений
        // Поддерживает одинарные и двойные кавычки, пробелы, различные варианты написания
        $pattern = '/<img[^>]*\s+src\s*=\s*(["\'])(data:image\/([^;]+);base64,([^"\']+))\1[^>]*>/i';
        
        // Также пробуем альтернативный паттерн для случаев, когда кавычки могут быть в другом порядке
        $pattern2 = '/<img[^>]*src\s*=\s*["\'](data:image\/([^;]+);base64,([^"\']+))["\'][^>]*>/i';
        
        preg_match_all($pattern, $html, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        
        // Если первый паттерн не нашел, пробуем второй
        if (empty($matches)) {
            preg_match_all($pattern2, $html, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
        }
        
        if (empty($matches)) {
            \Log::info('No base64 images found in description', ['html_length' => strlen($html)]);
            return $html; // Нет base64 изображений
        }

        \Log::info('Found base64 images', ['count' => count($matches)]);

        // Убеждаемся, что папка существует
        Storage::disk('public')->makeDirectory('products/images');

        // Обрабатываем в обратном порядке, чтобы позиции не сдвигались при замене
        $replacements = [];
        
        foreach ($matches as $index => $match) {
            $fullMatch = $match[0][0]; // Полный тег <img>
            $offset = $match[0][1]; // Позиция в строке
            
            // Определяем, какой паттерн сработал
            if (isset($match[4])) {
                // Первый паттерн (с кавычками в группе 1)
                $quote = $match[1][0]; // Кавычка (одинарная или двойная)
                $dataUrl = $match[2][0]; // data:image/...;base64,...
                $imageType = $match[3][0]; // jpeg, png, gif, webp
                $base64Data = $match[4][0]; // base64 данные
            } else {
                // Второй паттерн (без отдельной группы для кавычек)
                $dataUrl = $match[1][0]; // data:image/...;base64,...
                $imageType = $match[2][0]; // jpeg, png, gif, webp
                $base64Data = $match[3][0]; // base64 данные
                // Определяем кавычку из тега
                $quote = (strpos($fullMatch, 'src="') !== false) ? '"' : "'";
            }

            \Log::info('Processing base64 image', [
                'index' => $index + 1,
                'type' => $imageType,
                'data_length' => strlen($base64Data),
                'offset' => $offset,
            ]);

            try {
                // Декодируем base64
                $imageData = base64_decode($base64Data, true);
                
                if ($imageData === false) {
                    \Log::warning('Failed to decode base64 image', [
                        'index' => $index + 1,
                        'type' => $imageType,
                        'data_preview' => substr($base64Data, 0, 50) . '...',
                    ]);
                    continue;
                }

                // Определяем расширение файла
                $extension = $imageType === 'jpeg' ? 'jpg' : ($imageType === 'svg+xml' ? 'svg' : $imageType);
                
                // Генерируем уникальное имя файла для каждого изображения
                $filename = time() . '_' . ($index + 1) . '_' . Str::random(10) . '.' . $extension;
                $path = 'products/images/' . $filename;

                // Сохраняем файл
                $saved = Storage::disk('public')->put($path, $imageData);
                
                if (!$saved) {
                    \Log::warning('Failed to save base64 image', [
                        'index' => $index + 1,
                        'path' => $path,
                    ]);
                    continue;
                }

                // Получаем размеры изображения
                $imageInfo = @getimagesizefromstring($imageData);
                $width = $imageInfo[0] ?? null;
                $height = $imageInfo[1] ?? null;

                // Создаем запись в media
                $media = Media::create([
                    'filename' => $filename,
                    'original_filename' => 'editor-image-' . ($index + 1) . '.' . $extension,
                    'path' => $path,
                    'url' => '/storage/' . $path,
                    'mime_type' => 'image/' . $imageType,
                    'size' => strlen($imageData),
                    'width' => $width,
                    'height' => $height,
                    'title' => $productName . ' - Image ' . ($index + 1),
                    'alt' => $productName . ' - Image ' . ($index + 1),
                ]);

                // Заменяем data:image на локальный путь
                $localUrl = asset('storage/' . $path);
                $newImgTag = preg_replace(
                    '/src=' . preg_quote($quote, '/') . 'data:image\/[^' . preg_quote($quote, '/') . ']+' . preg_quote($quote, '/') . '/i',
                    'src=' . $quote . $localUrl . $quote,
                    $fullMatch
                );

                // Сохраняем замену для применения в обратном порядке
                $replacements[] = [
                    'old' => $fullMatch,
                    'new' => $newImgTag,
                    'offset' => $offset,
                ];

                \Log::info('Base64 image converted successfully', [
                    'index' => $index + 1,
                    'media_id' => $media->id,
                    'path' => $path,
                    'url' => $localUrl,
                ]);

            } catch (\Exception $e) {
                \Log::error('Error processing base64 image', [
                    'index' => $index + 1,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                    'type' => $imageType,
                ]);
                continue;
            }
        }

        // Применяем замены в обратном порядке (с конца строки), чтобы позиции не сдвигались
        if (!empty($replacements)) {
            // Сортируем по позиции в обратном порядке
            usort($replacements, function($a, $b) {
                return $b['offset'] - $a['offset'];
            });

            foreach ($replacements as $replacement) {
                $html = substr_replace($html, $replacement['new'], $replacement['offset'], strlen($replacement['old']));
            }

            \Log::info('All base64 images replaced', ['count' => count($replacements)]);
        }

        return $html;
    }
}

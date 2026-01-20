<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Media;
use App\Models\Post;
use App\Models\PostFile;
use App\Models\Tag;
use App\Traits\CreatesRedirectOnDelete;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostController extends Controller
{
    use CreatesRedirectOnDelete;
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

        $posts = $query->paginate(20)->withQueryString();

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
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240', // 10MB max
            'delete_files' => 'nullable|array',
            'delete_files.*' => 'exists:post_files,id',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:320',
            'seo_h1' => 'nullable|string|max:255',
            'seo_intro_text' => 'nullable|string',
        ]);

        // Валидация расширений файлов
        if ($request->hasFile('files')) {
            $allowedExtensions = ['mq4', 'ex4', 'mq5', 'ex5', 'zip', 'rar', '7z'];
            foreach ($request->file('files') as $index => $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                if (!in_array($extension, $allowedExtensions)) {
                    return back()->withErrors([
                        "files.{$index}" => "Файл должен иметь одно из расширений: mq4, ex4, mq5, ex5, zip, rar, 7z"
                    ])->withInput();
                }
            }
        }

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

        // Обработка base64 изображений в содержании
        if (isset($validated['content']) && !empty($validated['content'])) {
            $validated['content'] = $this->processBase64Images($validated['content'], $validated['title'] ?? 'Post');
        }

        // Сохранение поста
        $categories = $validated['categories'] ?? [];
        $tags = $validated['tags'] ?? [];
        unset($validated['categories'], $validated['tags'], $validated['files'], $validated['delete_files']);

        $post = Post::create($validated);

        // Привязка категорий и тегов
        if (!empty($categories)) {
            $post->categories()->sync($categories);
        }
        if (!empty($tags)) {
            $post->tags()->sync($tags);
        }

        // Загрузка файлов
        if ($request->hasFile('files')) {
            $order = 0;
            foreach ($request->file('files') as $file) {
                $path = $file->store('posts', 'local');
                PostFile::create([
                    'post_id' => $post->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'order' => ++$order,
                    'download_count' => 0,
                ]);
            }
        }

        return redirect()->route('admin.posts.index')
            ->with('success', 'Пост успешно создан');
    }

    /**
     * Детали поста
     */
    public function show($id)
    {
        $post = Post::with('categories', 'tags', 'featuredImage', 'comments', 'files')
            ->findOrFail($id);

        return view('admin.posts.show', compact('post'));
    }

    /**
     * Форма редактирования поста
     */
    public function edit($id)
    {
        $post = Post::with('categories', 'tags', 'files')->findOrFail($id);
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
            'files' => 'nullable|array',
            'files.*' => 'file|max:10240', // 10MB max
            'delete_files' => 'nullable|array',
            'delete_files.*' => 'exists:post_files,id',
            'seo_title' => 'nullable|string|max:255',
            'seo_description' => 'nullable|string|max:320',
            'seo_h1' => 'nullable|string|max:255',
            'seo_intro_text' => 'nullable|string',
        ]);

        // Валидация расширений файлов
        if ($request->hasFile('files')) {
            $allowedExtensions = ['mq4', 'ex4', 'mq5', 'ex5', 'zip', 'rar', '7z'];
            foreach ($request->file('files') as $index => $file) {
                $extension = strtolower($file->getClientOriginalExtension());
                if (!in_array($extension, $allowedExtensions)) {
                    return back()->withErrors([
                        "files.{$index}" => "Файл должен иметь одно из расширений: mq4, ex4, mq5, ex5, zip, rar, 7z"
                    ])->withInput();
                }
            }
        }

        // Обработка base64 изображений в содержании (ДО валидации для уменьшения размера)
        $contentInput = $request->input('content', '');
        $base64CountBeforeValidation = preg_match_all('/data:image\/[^;]+;base64,/', $contentInput, $base64MatchesBefore);
        
        if (!empty($contentInput) && $base64CountBeforeValidation > 0) {
            $contentInput = $this->processBase64Images($contentInput, $post->title ?? 'Post');
            $request->merge(['content' => $contentInput]);
        }

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

        // Удаление выбранных файлов (обрабатываем ДО обновления поста)
        if ($request->has('delete_files')) {
            $deleteFileIds = is_array($request->delete_files) ? $request->delete_files : [$request->delete_files];
            foreach ($deleteFileIds as $fileId) {
                $postFile = PostFile::where('id', $fileId)
                    ->where('post_id', $post->id)
                    ->first();
                
                if ($postFile) {
                    // Удаляем физический файл
                    if (Storage::disk('local')->exists($postFile->file_path)) {
                        Storage::disk('local')->delete($postFile->file_path);
                    }
                    // Удаляем запись из БД
                    $postFile->delete();
                }
            }
        }

        // Загрузка новых файлов (обрабатываем ДО обновления поста)
        if ($request->hasFile('files')) {
            $order = $post->files()->max('order') ?? 0;
            foreach ($request->file('files') as $file) {
                $path = $file->store('posts', 'local');
                PostFile::create([
                    'post_id' => $post->id,
                    'file_path' => $path,
                    'file_name' => $file->getClientOriginalName(),
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'order' => ++$order,
                    'download_count' => 0,
                ]);
            }
        }

        // Установка даты публикации
        if ($validated['status'] === 'publish' && !$post->published_at) {
            $validated['published_at'] = now();
        }

        // Проверка: если base64 изображения все еще остались
        if (isset($validated['content']) && !empty($validated['content'])) {
            $base64Count = preg_match_all('/data:image\/[^;]+;base64,/', $validated['content'], $base64Matches);
            if ($base64Count > 0) {
                $validated['content'] = $this->processBase64Images($validated['content'], $post->title ?? 'Post');
            }
        }

        // Обновление поста
        $categories = $validated['categories'] ?? [];
        $tags = $validated['tags'] ?? [];
        
        // Удаляем поля, которые не должны попасть в update
        unset($validated['categories'], $validated['tags'], $validated['files'], $validated['delete_files']);

        $post->update($validated);

        // Перезагружаем связи для корректного отображения
        $post->refresh();
        $post->load('files');

        // Синхронизация категорий и тегов
        if (!empty($categories)) {
            $post->categories()->sync($categories);
        } else {
            $post->categories()->sync([]);
        }
        if (!empty($tags)) {
            $post->tags()->sync($tags);
        } else {
            $post->tags()->sync([]);
        }

        return redirect()->route('admin.posts.edit', $post->id)
            ->with('success', 'Пост успешно обновлен');
    }

    /**
     * Удаление поста
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        
        // Получаем URL поста для редиректа (до удаления)
        $oldUrl = $this->getUrlForRedirect($post);
        
        // Удаляем файлы поста (физически файлы остаются, но записи удаляются)
        // Файлы остаются в папке, но становятся недоступными, так как записи удаляются
        foreach ($post->files as $file) {
            // НЕ удаляем физические файлы - они остаются в папке
            $file->delete();
        }
        
        // Удаляем пост
        $post->delete();
        
        // Создаем или обновляем редирект на главную страницу
        $redirect = $this->createRedirectToHome($oldUrl);
        
        $message = 'Пост успешно удален';
        if ($redirect) {
            $message .= '. Создан редирект 301: ' . $oldUrl . ' → /';
        }

        return redirect()->route('admin.posts.index')
            ->with('success', $message);
    }

    /**
     * Обработка base64 изображений в содержании
     * Конвертирует data:image/... в файлы и заменяет в HTML
     */
    private function processBase64Images(string $html, string $postTitle = 'Post'): string
    {
        // Универсальный паттерн для поиска base64 изображений
        $patterns = [
            '/<img[^>]*\s+src\s*=\s*(["\'])(data:image\/([^;]+);base64,([^"\']+))\1[^>]*>/i',
            '/<img[^>]*src=(["\'])(data:image\/([^;]+);base64,([^"\']+))\1[^>]*>/i',
            '/<img[^>]*src\s*=\s*["\'](data:image\/([^;]+);base64,([^"\']+))["\'][^>]*>/i',
        ];
        
        $allMatches = [];
        $usedOffsets = [];
        
        foreach ($patterns as $patternIndex => $pattern) {
            preg_match_all($pattern, $html, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
            
            foreach ($matches as $match) {
                $offset = $match[0][1];
                $isDuplicate = false;
                foreach ($usedOffsets as $usedOffset) {
                    if (abs($offset - $usedOffset) < 10) {
                        $isDuplicate = true;
                        break;
                    }
                }
                
                if (!$isDuplicate) {
                    $allMatches[] = [
                        'match' => $match,
                        'pattern' => $patternIndex,
                    ];
                    $usedOffsets[] = $offset;
                }
            }
        }
        
        if (empty($allMatches)) {
            return $html;
        }

        // Убеждаемся, что папка существует
        Storage::disk('public')->makeDirectory('posts/images');

        $replacements = [];
        
        foreach ($allMatches as $index => $matchData) {
            $match = $matchData['match'];
            $fullMatch = $match[0][0];
            $offset = $match[0][1];
            
            $quote = '"';
            $imageType = '';
            $base64Data = '';
            
            if (isset($match[4]) && isset($match[4][0])) {
                $quote = $match[1][0] ?? '"';
                $imageType = $match[3][0] ?? '';
                $base64Data = $match[4][0] ?? '';
            } elseif (isset($match[3]) && isset($match[3][0])) {
                $imageType = $match[2][0] ?? '';
                $base64Data = $match[3][0] ?? '';
                $quote = (strpos($fullMatch, 'src="') !== false) ? '"' : "'";
            } else {
                continue;
            }
            
            if (empty($base64Data) || empty($imageType)) {
                continue;
            }

            try {
                $imageData = base64_decode($base64Data, true);
                
                if ($imageData === false) {
                    continue;
                }

                $extension = $imageType === 'jpeg' ? 'jpg' : ($imageType === 'svg+xml' ? 'svg' : $imageType);
                
                $filename = time() . '_' . ($index + 1) . '_' . Str::random(10) . '.' . $extension;
                $path = 'posts/images/' . $filename;

                $saved = Storage::disk('public')->put($path, $imageData);
                
                if (!$saved) {
                    continue;
                }

                $imageInfo = @getimagesizefromstring($imageData);
                $width = $imageInfo[0] ?? null;
                $height = $imageInfo[1] ?? null;

                $media = Media::create([
                    'filename' => $filename,
                    'original_filename' => 'editor-image-' . ($index + 1) . '.' . $extension,
                    'path' => $path,
                    'url' => '/storage/' . $path,
                    'mime_type' => 'image/' . $imageType,
                    'size' => strlen($imageData),
                    'width' => $width,
                    'height' => $height,
                    'title' => $postTitle . ' - Image ' . ($index + 1),
                    'alt' => $postTitle . ' - Image ' . ($index + 1),
                ]);

                $localUrl = asset('storage/' . $path);
                $newImgTag = preg_replace(
                    '/src=' . preg_quote($quote, '/') . 'data:image\/[^' . preg_quote($quote, '/') . ']+' . preg_quote($quote, '/') . '/i',
                    'src=' . $quote . $localUrl . $quote,
                    $fullMatch
                );

                $replacements[] = [
                    'old' => $fullMatch,
                    'new' => $newImgTag,
                    'offset' => $offset,
                ];

            } catch (\Exception $e) {
                continue;
            }
        }

        if (!empty($replacements)) {
            usort($replacements, function($a, $b) {
                return $b['offset'] - $a['offset'];
            });

            foreach ($replacements as $replacement) {
                $html = substr_replace($html, $replacement['new'], $replacement['offset'], strlen($replacement['old']));
            }
        }

        return $html;
    }
}

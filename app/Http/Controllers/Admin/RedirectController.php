<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Redirect;
use Illuminate\Http\Request;

class RedirectController extends Controller
{
    /**
     * Список редиректов
     */
    public function index(Request $request)
    {
        $query = Redirect::orderBy('created_at', 'desc');

        // Поиск по старому или новому URL
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('old_url', 'like', "%{$search}%")
                  ->orWhere('new_url', 'like', "%{$search}%");
            });
        }

        // Фильтрация по статусу активности
        if ($request->filled('is_active')) {
            $isActive = $request->is_active === '1' || $request->is_active === 'true' || $request->is_active === true;
            $query->where('is_active', $isActive);
        }

        $redirects = $query->paginate(30)->withQueryString();

        return view('admin.redirects.index', compact('redirects'));
    }

    /**
     * Форма создания редиректа
     */
    public function create()
    {
        return view('admin.redirects.create');
    }

    /**
     * Сохранение нового редиректа
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'old_url' => 'required|string|max:255|unique:redirects,old_url',
            'new_url' => 'required|string|max:255',
            'type' => 'nullable|string|max:50',
            'status_code' => 'required|integer|in:301,302,307,308',
            'is_active' => 'boolean',
        ], [
            'old_url.unique' => 'Редирект с таким старым URL уже существует',
            'status_code.in' => 'Код статуса должен быть 301, 302, 307 или 308',
        ]);

        // Нормализуем URL - убираем начальный слеш
        $validated['old_url'] = trim($validated['old_url'], '/');
        $newUrlTrimmed = trim($validated['new_url'], '/');
        
        // Если после trim получилась пустая строка, это главная страница - сохраняем '/'
        if ($newUrlTrimmed === '') {
            $validated['new_url'] = '/';
        } else {
            // Если new_url начинается не с /, добавляем
            if (!str_starts_with($newUrlTrimmed, '/')) {
                $validated['new_url'] = '/' . $newUrlTrimmed;
            } else {
                $validated['new_url'] = $newUrlTrimmed;
            }
        }

        // Проверяем, что old_url не равен new_url
        if ($validated['old_url'] === trim($validated['new_url'], '/')) {
            return back()->withInput()->withErrors([
                'new_url' => 'Старый и новый URL не могут быть одинаковыми'
            ]);
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;

        Redirect::create($validated);

        return redirect()->route('admin.redirects.index')
            ->with('success', 'Редирект успешно создан');
    }

    /**
     * Форма редактирования редиректа
     */
    public function edit($id)
    {
        $redirect = Redirect::findOrFail($id);

        return view('admin.redirects.edit', compact('redirect'));
    }

    /**
     * Обновление редиректа
     */
    public function update(Request $request, $id)
    {
        $redirect = Redirect::findOrFail($id);

        $validated = $request->validate([
            'old_url' => 'required|string|max:255|unique:redirects,old_url,' . $id,
            'new_url' => 'required|string|max:255',
            'type' => 'nullable|string|max:50',
            'status_code' => 'required|integer|in:301,302,307,308',
            'is_active' => 'boolean',
        ], [
            'old_url.unique' => 'Редирект с таким старым URL уже существует',
            'status_code.in' => 'Код статуса должен быть 301, 302, 307 или 308',
        ]);

        // Нормализуем URL - убираем начальный слеш
        $validated['old_url'] = trim($validated['old_url'], '/');
        $newUrlTrimmed = trim($validated['new_url'], '/');
        
        // Сохраняем old_url как есть (без декодирования)
        // Если пользователь ввел URL-encoded версию (%d0%b8...), сохраняем её
        // Если ввел обычную версию - сохраняем обычную
        // При поиске будем проверять оба варианта
        
        // Если после trim получилась пустая строка, это главная страница - сохраняем '/'
        if ($newUrlTrimmed === '') {
            $validated['new_url'] = '/';
        } else {
            // Если new_url начинается не с /, добавляем
            if (!str_starts_with($newUrlTrimmed, '/')) {
                $validated['new_url'] = '/' . $newUrlTrimmed;
            } else {
                $validated['new_url'] = $newUrlTrimmed;
            }
        }

        // Проверяем, что old_url не равен new_url
        if ($validated['old_url'] === trim($validated['new_url'], '/')) {
            return back()->withInput()->withErrors([
                'new_url' => 'Старый и новый URL не могут быть одинаковыми'
            ]);
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;

        $redirect->update($validated);

        return redirect()->route('admin.redirects.index')
            ->with('success', 'Редирект успешно обновлен');
    }

    /**
     * Удаление редиректа
     */
    public function destroy($id)
    {
        $redirect = Redirect::findOrFail($id);
        $redirect->delete();

        return redirect()->route('admin.redirects.index')
            ->with('success', 'Редирект успешно удален');
    }
}

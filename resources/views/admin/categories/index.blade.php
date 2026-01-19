@extends('layouts.app')

@section('title', 'Категории - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Управление категориями</h1>
        <a href="{{ route('admin.categories.create') }}" class="btn">+ Создать категорию</a>
    </div>
    
    @if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        {{ session('error') }}
    </div>
    @endif
    
    <div style="margin-bottom: 2rem;">
        <form method="GET" action="{{ route('admin.categories.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск по названию" 
                   style="flex: 1; min-width: 200px; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
            <select name="type" style="padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <option value="">Все типы</option>
                <option value="post" {{ request('type') === 'post' ? 'selected' : '' }}>Статьи</option>
                <option value="product" {{ request('type') === 'product' ? 'selected' : '' }}>Товары</option>
            </select>
            <button type="submit" class="btn">Поиск</button>
            @if(request()->anyFilled(['search', 'type']))
            <a href="{{ route('admin.categories.index') }}" class="btn" style="background: #6b7280;">Сбросить</a>
            @endif
        </form>
    </div>
    
    @if($categories->count() > 0)
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Название</th>
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Slug</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Тип</th>
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Родительская категория</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($categories as $category)
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.5rem;">
                    <div style="font-size: 0.875rem; font-weight: 500;">
                        <a href="{{ route('category.show', $category->slug) }}" target="_blank" style="color: #2563eb; text-decoration: none;">
                            {{ $category->name }}
                        </a>
                    </div>
                    @if($category->description)
                    <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.125rem;">
                        {{ Str::limit(strip_tags($category->description), 50) }}
                    </div>
                    @endif
                </td>
                <td style="padding: 0.5rem; font-size: 0.875rem; color: #6b7280;">{{ $category->slug }}</td>
                <td style="padding: 0.5rem; text-align: center;">
                    @if($category->type === 'post')
                    <span style="font-size: 0.75rem; background: #eff6ff; color: #2563eb; padding: 0.25rem 0.5rem; border-radius: 4px;">Статьи</span>
                    @else
                    <span style="font-size: 0.75rem; background: #f0fdf4; color: #16a34a; padding: 0.25rem 0.5rem; border-radius: 4px;">Товары</span>
                    @endif
                </td>
                <td style="padding: 0.5rem; font-size: 0.875rem;">
                    @if($category->parent)
                    {{ $category->parent->name }}
                    @else
                    <span style="color: #9ca3af;">-</span>
                    @endif
                </td>
                <td style="padding: 0.5rem; text-align: center;">
                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                        <a href="{{ route('category.show', $category->slug) }}" target="_blank"
                           style="padding: 0.5rem; text-decoration: none; color: #2563eb; font-size: 1.25rem;" 
                           title="Просмотр">👁️</a>
                        <a href="{{ route('admin.categories.edit', $category->id) }}" 
                           style="padding: 0.5rem; text-decoration: none; color: #2563eb; font-size: 1.25rem;" 
                           title="Редактировать">✏️</a>
                        <form action="{{ route('admin.categories.destroy', $category->id) }}" method="POST" style="display: inline;" 
                              onsubmit="return confirm('Вы уверены, что хотите удалить эту категорию?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    style="padding: 0.5rem; background: none; border: none; cursor: pointer; font-size: 1.25rem; color: #dc2626;" 
                                    title="Удалить">🗑️</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 2rem;">
        {{ $categories->links() }}
    </div>
    @else
    <p style="color: #6b7280; margin-top: 1rem;">Категорий не найдено</p>
    @endif
</div>
@endsection

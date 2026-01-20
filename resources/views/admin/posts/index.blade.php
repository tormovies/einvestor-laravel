@extends('layouts.app')

@section('title', 'Посты - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Управление постами</h1>
        <a href="{{ route('admin.posts.create') }}" class="btn">+ Создать пост</a>
    </div>
    
    @if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        {{ session('success') }}
    </div>
    @endif
    
    <div style="margin-bottom: 2rem;">
        <form method="GET" action="{{ route('admin.posts.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск по названию" 
                   style="flex: 1; min-width: 200px; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
            <select name="status" style="padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <option value="">Все статусы</option>
                <option value="publish" {{ request('status') === 'publish' ? 'selected' : '' }}>Опубликовано</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Черновик</option>
            </select>
            <button type="submit" class="btn">Поиск</button>
            @if(request()->anyFilled(['search', 'status']))
            <a href="{{ route('admin.posts.index') }}" class="btn" style="background: #6b7280;">Сбросить</a>
            @endif
        </form>
    </div>
    
    @if($posts->count() > 0)
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Название</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Статус</th>
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Категории</th>
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Дата</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $post)
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.5rem;">
                    <div style="font-size: 0.875rem; font-weight: 500;">
                        <a href="{{ route('articles.show', $post->slug) }}" target="_blank" style="color: #2563eb; text-decoration: none;">
                            {{ $post->title }}
                        </a>
                    </div>
                    @if($post->excerpt)
                    <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.125rem;">
                        {{ Str::limit(strip_tags($post->excerpt), 50) }}
                    </div>
                    @endif
                </td>
                <td style="padding: 0.5rem; text-align: center;">
                    @if($post->status === 'publish')
                    <span style="font-size: 1.25rem;" title="Опубликовано">✅</span>
                    @else
                    <span style="font-size: 1.25rem;" title="Черновик">❌</span>
                    @endif
                </td>
                <td style="padding: 0.5rem;">
                    @if($post->categories->count() > 0)
                    <div style="display: flex; flex-wrap: wrap; gap: 0.25rem;">
                        @foreach($post->categories->take(2) as $category)
                        <span style="font-size: 0.7rem; background: #e5e7eb; padding: 0.2rem 0.4rem; border-radius: 3px;">
                            {{ $category->name }}
                        </span>
                        @endforeach
                        @if($post->categories->count() > 2)
                        <span style="font-size: 0.7rem; color: #6b7280;">+{{ $post->categories->count() - 2 }}</span>
                        @endif
                    </div>
                    @else
                    <span style="color: #9ca3af; font-size: 0.75rem;">-</span>
                    @endif
                </td>
                <td style="padding: 0.5rem; font-size: 0.875rem;">{{ $post->created_at->format('d.m.Y') }}</td>
                <td style="padding: 0.5rem; text-align: center;">
                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                        <a href="{{ route('articles.show', $post->slug) }}" target="_blank"
                           style="padding: 0.5rem; text-decoration: none; color: #2563eb; font-size: 1.25rem;" 
                           title="Просмотр">👁️</a>
                        <a href="{{ route('admin.posts.edit', $post->id) }}" 
                           style="padding: 0.5rem; text-decoration: none; color: #2563eb; font-size: 1.25rem;" 
                           title="Редактировать">✏️</a>
                        <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" style="display: inline;" 
                              onsubmit="return confirm('Вы уверены, что хотите удалить этот пост?');">
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
    
    <div class="pagination-wrapper">
        {{ $posts->links('vendor.pagination.compact') }}
    </div>
    @else
    <p style="color: #6b7280; margin-top: 1rem;">Постов не найдено</p>
    @endif
</div>

@push('styles')
<style>
.pagination-wrapper {
    margin-top: 2rem;
    display: flex;
    justify-content: center;
}

.pagination-nav {
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    gap: 0.5rem;
    list-style: none;
    padding: 0;
    margin: 0;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
}

.pagination .page-item {
    display: inline-block;
}

.pagination .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    height: 38px;
    padding: 0.5rem 0.75rem;
    background: white;
    color: #4b5563;
    text-decoration: none;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
    line-height: 1;
}

.pagination .page-link:hover:not(.disabled) {
    background: #f3f4f6;
    color: #2563eb;
    border-color: #2563eb;
}

.pagination .page-item.active .page-link {
    background: #2563eb;
    color: white;
    border-color: #2563eb;
    cursor: default;
}

.pagination .page-item.active .page-link:hover {
    background: #2563eb;
    color: white;
}

.pagination .page-item.disabled .page-link {
    background: #f9fafb;
    color: #d1d5db;
    border-color: #e5e7eb;
    cursor: not-allowed;
    pointer-events: none;
}

.pagination .page-link svg {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .pagination {
        gap: 0.375rem;
    }
    
    .pagination .page-link {
        min-width: 34px;
        height: 34px;
        padding: 0.375rem 0.5rem;
        font-size: 0.8125rem;
    }
    
    .pagination .page-link svg {
        width: 16px;
        height: 16px;
    }
}
</style>
@endpush
@endsection

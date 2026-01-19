@extends('layouts.app')

@section('title', 'Страницы - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Управление страницами</h1>
        <a href="{{ route('admin.pages.create') }}" class="btn">+ Создать страницу</a>
    </div>
    
    @if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        {{ session('success') }}
    </div>
    @endif
    
    <div style="margin-bottom: 2rem;">
        <form method="GET" action="{{ route('admin.pages.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск по названию" 
                   style="flex: 1; min-width: 200px; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
            <select name="status" style="padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <option value="">Все статусы</option>
                <option value="publish" {{ request('status') === 'publish' ? 'selected' : '' }}>Опубликовано</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Черновик</option>
                <option value="private" {{ request('status') === 'private' ? 'selected' : '' }}>Приватная</option>
            </select>
            <button type="submit" class="btn">Поиск</button>
            @if(request()->anyFilled(['search', 'status']))
            <a href="{{ route('admin.pages.index') }}" class="btn" style="background: #6b7280;">Сбросить</a>
            @endif
        </form>
    </div>
    
    @if($pages->count() > 0)
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Название</th>
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Slug</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Статус</th>
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Родительская страница</th>
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Дата</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pages as $page)
            @php
                $isSystemPage = in_array($page->slug, ['_home', '_products_list', '_articles_list']);
                $systemPageLabels = [
                    '_home' => 'Главная',
                    '_products_list' => 'Список товаров',
                    '_articles_list' => 'Список статей'
                ];
                $systemPageRoutes = [
                    '_home' => route('home'),
                    '_products_list' => route('products.index'),
                    '_articles_list' => route('articles.index')
                ];
                $systemPageUrl = $isSystemPage ? ($systemPageRoutes[$page->slug] ?? '#') : null;
            @endphp
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.5rem;">
                    <div style="font-size: 0.875rem; font-weight: 500;">
                        @if($isSystemPage)
                            <a href="{{ $systemPageUrl }}" target="_blank" style="color: #7c3aed; text-decoration: none; font-weight: 600;">
                                {{ $systemPageLabels[$page->slug] ?? $page->title }}
                            </a>
                            <span style="font-size: 0.75rem; color: #9ca3af; margin-left: 0.5rem;">(системная)</span>
                        @else
                            <a href="{{ route('pages.show', $page->slug) }}" target="_blank" style="color: #2563eb; text-decoration: none;">
                                {{ $page->title }}
                            </a>
                        @endif
                    </div>
                    @if($page->excerpt)
                    <div style="font-size: 0.75rem; color: #6b7280; margin-top: 0.125rem;">
                        {{ Str::limit(strip_tags($page->excerpt), 50) }}
                    </div>
                    @endif
                </td>
                <td style="padding: 0.5rem; font-size: 0.875rem; color: #6b7280;">{{ $page->slug }}</td>
                <td style="padding: 0.5rem; text-align: center;">
                    @if($page->status === 'publish')
                    <span style="font-size: 1.25rem;" title="Опубликовано">✅</span>
                    @elseif($page->status === 'draft')
                    <span style="font-size: 1.25rem;" title="Черновик">❌</span>
                    @else
                    <span style="font-size: 1.25rem;" title="Приватная">🔒</span>
                    @endif
                </td>
                <td style="padding: 0.5rem; font-size: 0.875rem;">
                    @if($page->parent)
                    {{ $page->parent->title }}
                    @else
                    <span style="color: #9ca3af;">-</span>
                    @endif
                </td>
                <td style="padding: 0.5rem; font-size: 0.875rem;">{{ $page->created_at->format('d.m.Y') }}</td>
                <td style="padding: 0.5rem; text-align: center;">
                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                        <a href="{{ $isSystemPage ? $systemPageUrl : route('pages.show', $page->slug) }}" target="_blank"
                           style="padding: 0.5rem; text-decoration: none; color: #2563eb; font-size: 1.25rem;" 
                           title="Просмотр">👁️</a>
                        <a href="{{ route('admin.pages.edit', $page->id) }}" 
                           style="padding: 0.5rem; text-decoration: none; color: #2563eb; font-size: 1.25rem;" 
                           title="Редактировать">✏️</a>
                        @if(!$isSystemPage)
                        <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" style="display: inline;" 
                              onsubmit="return confirm('Вы уверены, что хотите удалить эту страницу?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    style="padding: 0.5rem; background: none; border: none; cursor: pointer; font-size: 1.25rem; color: #dc2626;" 
                                    title="Удалить">🗑️</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 2rem;">
        {{ $pages->links() }}
    </div>
    @else
    <p style="color: #6b7280; margin-top: 1rem;">Страниц не найдено</p>
    @endif
</div>
@endsection

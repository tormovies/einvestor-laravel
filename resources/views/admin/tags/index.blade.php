@extends('layouts.app')

@section('title', 'Теги - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Управление тегами</h1>
        <a href="{{ route('admin.tags.create') }}" class="btn">+ Создать тег</a>
    </div>
    
    @if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        {{ session('success') }}
    </div>
    @endif
    
    <div style="margin-bottom: 2rem;">
        <form method="GET" action="{{ route('admin.tags.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск по названию" 
                   style="flex: 1; min-width: 200px; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
            <button type="submit" class="btn">Поиск</button>
            @if(request()->has('search') && request('search'))
            <a href="{{ route('admin.tags.index') }}" class="btn" style="background: #6b7280;">Сбросить</a>
            @endif
        </form>
    </div>
    
    @if($tags->count() > 0)
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Название</th>
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Slug</th>
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Описание</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Использований</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tags as $tag)
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.5rem;">
                    <div style="font-size: 0.875rem; font-weight: 500;">
                        <a href="{{ route('tag.show', $tag->slug) }}" target="_blank" style="color: #2563eb; text-decoration: none;">
                            {{ $tag->name }}
                        </a>
                    </div>
                </td>
                <td style="padding: 0.5rem; font-size: 0.875rem; color: #6b7280;">{{ $tag->slug }}</td>
                <td style="padding: 0.5rem; font-size: 0.875rem;">
                    @if($tag->description)
                        {{ Str::limit(strip_tags($tag->description), 50) }}
                    @else
                        <span style="color: #9ca3af;">-</span>
                    @endif
                </td>
                <td style="padding: 0.5rem; text-align: center; font-size: 0.875rem;">{{ $tag->count }}</td>
                <td style="padding: 0.5rem; text-align: center;">
                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                        <a href="{{ route('tag.show', $tag->slug) }}" target="_blank"
                           style="padding: 0.5rem; text-decoration: none; color: #2563eb; font-size: 1.25rem;" 
                           title="Просмотр">👁️</a>
                        <a href="{{ route('admin.tags.edit', $tag->id) }}" 
                           style="padding: 0.5rem; text-decoration: none; color: #2563eb; font-size: 1.25rem;" 
                           title="Редактировать">✏️</a>
                        <form action="{{ route('admin.tags.destroy', $tag->id) }}" method="POST" style="display: inline;" 
                              onsubmit="return confirm('Вы уверены, что хотите удалить этот тег?');">
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
        {{ $tags->links() }}
    </div>
    @else
    <p style="color: #6b7280; margin-top: 1rem;">Тегов не найдено</p>
    @endif
</div>
@endsection

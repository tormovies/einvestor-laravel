@extends('layouts.app')

@section('title', 'Посты - Админ-панель - EInvestor')

@section('content')
<div class="content">
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
                <th style="text-align: left; padding: 1rem;">Название</th>
                <th style="text-align: center; padding: 1rem;">Статус</th>
                <th style="text-align: left; padding: 1rem;">Категории</th>
                <th style="text-align: left; padding: 1rem;">Дата</th>
                <th style="text-align: center; padding: 1rem;">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($posts as $post)
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 1rem;">
                    <strong>{{ $post->title }}</strong>
                    @if($post->excerpt)
                    <div style="font-size: 0.875rem; color: #6b7280; margin-top: 0.25rem;">
                        {{ Str::limit(strip_tags($post->excerpt), 60) }}
                    </div>
                    @endif
                </td>
                <td style="padding: 1rem; text-align: center;">
                    <span style="padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.875rem; 
                        background: {{ $post->status === 'publish' ? '#d1fae5' : '#fee2e2' }};
                        color: {{ $post->status === 'publish' ? '#065f46' : '#991b1b' }};">
                        {{ $post->status === 'publish' ? 'Опубликовано' : 'Черновик' }}
                    </span>
                </td>
                <td style="padding: 1rem;">
                    @if($post->categories->count() > 0)
                    <div style="display: flex; flex-wrap: wrap; gap: 0.25rem;">
                        @foreach($post->categories->take(3) as $category)
                        <span style="font-size: 0.75rem; background: #e5e7eb; padding: 0.25rem 0.5rem; border-radius: 4px;">
                            {{ $category->name }}
                        </span>
                        @endforeach
                        @if($post->categories->count() > 3)
                        <span style="font-size: 0.75rem; color: #6b7280;">+{{ $post->categories->count() - 3 }}</span>
                        @endif
                    </div>
                    @else
                    <span style="color: #9ca3af;">-</span>
                    @endif
                </td>
                <td style="padding: 1rem;">{{ $post->created_at->format('d.m.Y H:i') }}</td>
                <td style="padding: 1rem; text-align: center;">
                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                        <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Редактировать</a>
                        <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" style="display: inline;" 
                              onsubmit="return confirm('Вы уверены, что хотите удалить этот пост?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn" style="padding: 0.5rem 1rem; font-size: 0.875rem; background: #dc2626;">Удалить</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 2rem;">
        {{ $posts->links() }}
    </div>
    @else
    <p style="color: #6b7280; margin-top: 1rem;">Постов не найдено</p>
    @endif
</div>
@endsection

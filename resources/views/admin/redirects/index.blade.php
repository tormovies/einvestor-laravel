@extends('layouts.app')

@section('title', 'Редиректы - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Управление редиректами</h1>
        <a href="{{ route('admin.redirects.create') }}" class="btn">+ Создать редирект</a>
    </div>
    
    @if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        {{ session('success') }}
    </div>
    @endif
    
    <div style="margin-bottom: 2rem;">
        <form method="GET" action="{{ route('admin.redirects.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск по URL" 
                   style="flex: 1; min-width: 200px; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
            <select name="is_active" style="padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <option value="">Все статусы</option>
                <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>Активные</option>
                <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>Неактивные</option>
            </select>
            <button type="submit" class="btn">Поиск</button>
            @if(request()->anyFilled(['search', 'is_active']))
            <a href="{{ route('admin.redirects.index') }}" class="btn" style="background: #6b7280;">Сбросить</a>
            @endif
        </form>
    </div>
    
    @if($redirects->count() > 0)
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Старый URL</th>
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Новый URL</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Тип</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Код</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Статус</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Использований</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($redirects as $redirect)
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.5rem; font-size: 0.875rem;">
                    <a href="/{{ $redirect->old_url }}" target="_blank" style="text-decoration: none; color: inherit;">
                        <code style="background: #f3f4f6; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; display: inline-block; cursor: pointer; transition: background 0.2s;" 
                              onmouseover="this.style.background='#e5e7eb'" 
                              onmouseout="this.style.background='#f3f4f6'">
                            /{{ $redirect->old_url }}
                        </code>
                    </a>
                </td>
                <td style="padding: 0.5rem; font-size: 0.875rem;">
                    <a href="{{ $redirect->new_url }}" target="_blank" style="text-decoration: none; color: inherit;">
                        <code style="background: #eff6ff; padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.8rem; display: inline-block; cursor: pointer; transition: background 0.2s;" 
                              onmouseover="this.style.background='#dbeafe'" 
                              onmouseout="this.style.background='#eff6ff'">
                            {{ $redirect->new_url }}
                        </code>
                    </a>
                </td>
                <td style="padding: 0.5rem; text-align: center; font-size: 0.875rem;">
                    @if($redirect->type)
                        <span style="font-size: 0.75rem; background: #e5e7eb; padding: 0.25rem 0.5rem; border-radius: 4px;">
                            {{ $redirect->type }}
                        </span>
                    @else
                        <span style="color: #9ca3af;">-</span>
                    @endif
                </td>
                <td style="padding: 0.5rem; text-align: center; font-size: 0.875rem;">
                    <span style="font-weight: 600; color: {{ $redirect->status_code === 301 ? '#059669' : '#dc2626' }};">
                        {{ $redirect->status_code }}
                    </span>
                </td>
                <td style="padding: 0.5rem; text-align: center;">
                    @if($redirect->is_active)
                    <span style="font-size: 1.25rem;" title="Активен">✅</span>
                    @else
                    <span style="font-size: 1.25rem; opacity: 0.5;" title="Неактивен">❌</span>
                    @endif
                </td>
                <td style="padding: 0.5rem; text-align: center; font-size: 0.875rem;">{{ $redirect->hits }}</td>
                <td style="padding: 0.5rem; text-align: center;">
                    <div style="display: flex; gap: 0.5rem; justify-content: center;">
                        <a href="{{ route('admin.redirects.edit', $redirect->id) }}" 
                           style="padding: 0.5rem; text-decoration: none; color: #2563eb; font-size: 1.25rem;" 
                           title="Редактировать">✏️</a>
                        <form action="{{ route('admin.redirects.destroy', $redirect->id) }}" method="POST" style="display: inline;" 
                              onsubmit="return confirm('Вы уверены, что хотите удалить этот редирект?');">
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
        {{ $redirects->links() }}
    </div>
    @else
    <p style="color: #6b7280; margin-top: 1rem;">Редиректов не найдено</p>
    @endif
</div>
@endsection

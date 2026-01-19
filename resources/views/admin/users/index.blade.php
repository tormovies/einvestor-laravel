@extends('layouts.app')

@section('title', 'Пользователи - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Управление пользователями</h1>
    </div>
    
    @if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        {{ session('success') }}
    </div>
    @endif
    
    <div style="margin-bottom: 2rem;">
        <form method="GET" action="{{ route('admin.users.index') }}" style="display: flex; gap: 1rem; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск по email или имени" 
                   style="flex: 1; min-width: 200px; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
            <select name="has_orders" style="padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <option value="">Все пользователи</option>
                <option value="1" {{ request('has_orders') === '1' ? 'selected' : '' }}>С заказами</option>
            </select>
            <select name="is_admin" style="padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <option value="">Все роли</option>
                <option value="1" {{ request('is_admin') === '1' ? 'selected' : '' }}>Администраторы</option>
                <option value="0" {{ request('is_admin') === '0' ? 'selected' : '' }}>Обычные</option>
            </select>
            <button type="submit" class="btn">Поиск</button>
            @if(request()->anyFilled(['search', 'has_orders', 'is_admin']))
            <a href="{{ route('admin.users.index') }}" class="btn" style="background: #6b7280;">Сбросить</a>
            @endif
        </form>
    </div>
    
    @if($users->count() > 0)
    <table style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">ID</th>
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Email</th>
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Имя</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Роль</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Заказы</th>
                <th style="text-align: right; padding: 0.5rem; font-size: 0.875rem;">Сумма</th>
                <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Регистрация</th>
                <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 0.5rem; font-size: 0.875rem;">#{{ $user->id }}</td>
                <td style="padding: 0.5rem; font-size: 0.875rem;">{{ $user->email }}</td>
                <td style="padding: 0.5rem; font-size: 0.875rem;">{{ $user->name ?? '-' }}</td>
                <td style="padding: 0.5rem; text-align: center;">
                    @if($user->is_admin)
                    <span style="font-size: 1.25rem;" title="Администратор">👑</span>
                    @else
                    <span style="color: #9ca3af;" title="Пользователь">👤</span>
                    @endif
                </td>
                <td style="padding: 0.5rem; text-align: center; font-size: 0.875rem;">
                    {{ $user->orders_count ?? 0 }}
                </td>
                <td style="padding: 0.5rem; text-align: right; font-size: 0.875rem;">
                    {{ number_format($user->orders_sum_total ?? 0, 0, ',', ' ') }} ₽
                </td>
                <td style="padding: 0.5rem; font-size: 0.875rem;">{{ $user->created_at->format('d.m.Y') }}</td>
                <td style="padding: 0.5rem; text-align: center;">
                    <a href="{{ route('admin.users.show', $user->id) }}" 
                       style="padding: 0.5rem; text-decoration: none; color: #2563eb; font-size: 1rem;" 
                       title="Просмотр">👁️</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div style="margin-top: 2rem;">
        {{ $users->links() }}
    </div>
    @else
    <p style="color: #6b7280; margin-top: 1rem;">Пользователей не найдено</p>
    @endif
</div>
@endsection

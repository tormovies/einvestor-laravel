@extends('layouts.app')

@section('title', 'Пользователь ' . $user->email . ' - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <h1>Пользователь: {{ $user->email }}</h1>
    
    <div style="margin-top: 2rem; display: grid; grid-template-columns: 1fr 2fr; gap: 2rem;">
        <div>
            <h2>Основная информация</h2>
            <div style="background: #f9fafb; padding: 1.5rem; border-radius: 8px; margin-top: 1rem;">
                <div style="margin-bottom: 1rem; line-height: 1.8;">
                    <div><strong>ID:</strong> #{{ $user->id }}</div>
                    <div><strong>Email:</strong> {{ $user->email }}</div>
                    <div><strong>Имя:</strong> {{ $user->name ?? '-' }}</div>
                    <div><strong>Роль:</strong> 
                        @if($user->is_admin)
                        <span title="Администратор">👑 Администратор</span>
                        @else
                        <span title="Пользователь">👤 Пользователь</span>
                        @endif
                    </div>
                    <div><strong>Дата регистрации:</strong> {{ $user->created_at->format('d.m.Y H:i') }}</div>
                    @if($user->email_verified_at)
                    <div><strong>Email подтвержден:</strong> <span style="color: #16a34a;">✅</span> {{ $user->email_verified_at->format('d.m.Y H:i') }}</div>
                    @else
                    <div><strong>Email подтвержден:</strong> <span style="color: #dc2626;">❌</span> Не подтвержден</div>
                    @endif
                </div>
            </div>
            
            <div style="margin-top: 1.5rem;">
                <h3>Статистика</h3>
                <div style="background: #f9fafb; padding: 1.5rem; border-radius: 8px; margin-top: 1rem;">
                    <div style="margin-bottom: 0.75rem; line-height: 1.8;">
                        <div><strong>Всего заказов:</strong> {{ $user->orders_count ?? 0 }}</div>
                        <div><strong>Общая сумма:</strong> <span style="color: #16a34a; font-weight: bold;">{{ number_format($user->orders_sum_total ?? 0, 0, ',', ' ') }} ₽</span></div>
                        @if($user->orders_count > 0)
                        <div><strong>Средний чек:</strong> {{ number_format(($user->orders_sum_total ?? 0) / ($user->orders_count ?? 1), 0, ',', ' ') }} ₽</div>
                        <div><strong>Последний заказ:</strong> {{ $user->orders->first() ? $user->orders->first()->created_at->format('d.m.Y H:i') : '-' }}</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
        <div>
            <h2>Заказы пользователя</h2>
            @if($user->orders && $user->orders->count() > 0)
            <table style="width: 100%; margin-top: 1rem; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                        <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Номер</th>
                        <th style="text-align: right; padding: 0.5rem; font-size: 0.875rem;">Сумма</th>
                        <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Статус</th>
                        <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Оплата</th>
                        <th style="text-align: left; padding: 0.5rem; font-size: 0.875rem;">Дата</th>
                        <th style="text-align: center; padding: 0.5rem; font-size: 0.875rem;">Действия</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($user->orders as $order)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 0.5rem; font-size: 0.875rem;"><strong>{{ $order->number }}</strong></td>
                        <td style="padding: 0.5rem; text-align: right; font-size: 0.875rem;">{{ number_format($order->total, 0, ',', ' ') }} ₽</td>
                        <td style="padding: 0.5rem; text-align: center; font-size: 0.875rem;">
                            @if($order->status === 'completed')
                            <span style="color: #16a34a;" title="Завершен">✅</span>
                            @elseif($order->status === 'pending')
                            <span style="color: #f59e0b;" title="Ожидает">⏳</span>
                            @elseif($order->status === 'processing')
                            <span style="color: #2563eb;" title="Обрабатывается">🔄</span>
                            @elseif($order->status === 'cancelled')
                            <span style="color: #dc2626;" title="Отменен">❌</span>
                            @elseif($order->status === 'refunded')
                            <span style="color: #9ca3af;" title="Возврат">↩️</span>
                            @else
                            <span>{{ $order->status }}</span>
                            @endif
                        </td>
                        <td style="padding: 0.5rem; text-align: center; font-size: 0.875rem;">
                            @if($order->payment_status === 'paid')
                            <span style="color: #16a34a;" title="Оплачен">💳</span>
                            @else
                            <span style="color: #dc2626;" title="Не оплачен">⏸️</span>
                            @endif
                        </td>
                        <td style="padding: 0.5rem; font-size: 0.875rem;">{{ $order->created_at->format('d.m.Y H:i') }}</td>
                        <td style="padding: 0.5rem; text-align: center;">
                            <a href="{{ route('admin.orders.show', $order->id) }}" 
                               style="padding: 0.5rem; text-decoration: none; color: #2563eb; font-size: 1rem;" 
                               title="Просмотр заказа">👁️</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <p style="color: #6b7280; margin-top: 1rem;">У пользователя пока нет заказов</p>
            @endif
        </div>
    </div>
    
    <div style="margin-top: 2rem;">
        <a href="{{ route('admin.users.index') }}" style="color: #2563eb; text-decoration: underline;">← Вернуться к списку пользователей</a>
    </div>
</div>
@endsection

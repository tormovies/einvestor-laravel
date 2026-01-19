@extends('layouts.app')

@section('title', 'Заказы - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <h1>Управление заказами</h1>
    
    <div style="margin-top: 2rem;">
        <form method="GET" action="{{ route('admin.orders.index') }}" style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Поиск по номеру или email" 
                   style="flex: 1; min-width: 200px; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
            <select name="status" style="padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <option value="">Все статусы</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Ожидает</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Обрабатывается</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Завершен</option>
            </select>
            <select name="payment_status" style="padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <option value="">Все платежи</option>
                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Не оплачен</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Оплачен</option>
            </select>
            <button type="submit" class="btn">Поиск</button>
            @if(request()->anyFilled(['search', 'status', 'payment_status']))
            <a href="{{ route('admin.orders.index') }}" class="btn" style="background: #6b7280;">Сбросить</a>
            @endif
        </form>
        
        @if($orders->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                    <th style="text-align: left; padding: 1rem;">Номер</th>
                    <th style="text-align: left; padding: 1rem;">Email</th>
                    <th style="text-align: right; padding: 1rem;">Сумма</th>
                    <th style="text-align: center; padding: 1rem;">Статус</th>
                    <th style="text-align: center; padding: 1rem;">Оплата</th>
                    <th style="text-align: left; padding: 1rem;">Дата</th>
                    <th style="text-align: center; padding: 1rem;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 1rem;"><strong>{{ $order->number }}</strong></td>
                    <td style="padding: 1rem;">{{ $order->email }}</td>
                    <td style="padding: 1rem; text-align: right;">{{ number_format($order->total, 0, ',', ' ') }} ₽</td>
                    <td style="padding: 1rem; text-align: center;">{{ $order->status }}</td>
                    <td style="padding: 1rem; text-align: center;">{{ $order->payment_status }}</td>
                    <td style="padding: 1rem;">{{ $order->created_at->format('d.m.Y H:i') }}</td>
                    <td style="padding: 1rem; text-align: center;">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Просмотр</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 2rem;">
            {{ $orders->links() }}
        </div>
        @else
        <p style="color: #6b7280; margin-top: 1rem;">Заказов не найдено</p>
        @endif
    </div>
</div>
@endsection

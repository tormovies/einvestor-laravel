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
        
        <div class="pagination-wrapper">
            {{ $orders->links('vendor.pagination.compact') }}
        </div>
        @else
        <p style="color: #6b7280; margin-top: 1rem;">Заказов не найдено</p>
        @endif
    </div>
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

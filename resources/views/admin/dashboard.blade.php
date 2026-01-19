@extends('layouts.app')

@section('title', 'Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <h1>Админ-панель</h1>
    
    @if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        {{ session('success') }}
    </div>
    @endif
    
    <div style="display: grid; grid-template-columns: repeat(6, 1fr); gap: 1.5rem; margin-top: 2rem;">
        <div class="card" style="padding: 1rem;">
            <h3 style="margin: 0 0 0.25rem 0; font-size: 0.875rem; font-weight: 500;">Всего заказов</h3>
            <div style="font-size: 1.75rem; font-weight: bold; color: #2563eb; margin: 0;">
                {{ $stats['orders_total'] }}
            </div>
        </div>
        
        <div class="card" style="padding: 1rem;">
            <h3 style="margin: 0 0 0.25rem 0; font-size: 0.875rem; font-weight: 500;">Ожидают обработки</h3>
            <div style="font-size: 1.75rem; font-weight: bold; color: #f59e0b; margin: 0;">
                {{ $stats['orders_pending'] }}
            </div>
        </div>
        
        <div class="card" style="padding: 1rem;">
            <h3 style="margin: 0 0 0.25rem 0; font-size: 0.875rem; font-weight: 500;">Оплачено</h3>
            <div style="font-size: 1.75rem; font-weight: bold; color: #16a34a; margin: 0;">
                {{ $stats['orders_paid'] }}
            </div>
        </div>
        
        <div class="card" style="padding: 1rem;">
            <h3 style="margin: 0 0 0.25rem 0; font-size: 0.875rem; font-weight: 500;">Товаров</h3>
            <div style="font-size: 1.75rem; font-weight: bold; color: #2563eb; margin: 0;">
                {{ $stats['products_total'] }} / {{ $stats['products_published'] }}
            </div>
            <div style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem;">Всего / Опубликовано</div>
        </div>
        
        <div class="card" style="padding: 1rem;">
            <h3 style="margin: 0 0 0.25rem 0; font-size: 0.875rem; font-weight: 500;">Постов</h3>
            <div style="font-size: 1.75rem; font-weight: bold; color: #2563eb; margin: 0;">
                {{ $stats['posts_total'] }} / {{ $stats['posts_published'] }}
            </div>
            <div style="color: #6b7280; font-size: 0.75rem; margin-top: 0.25rem;">Всего / Опубликовано</div>
        </div>
        
        <div class="card" style="padding: 1rem;">
            <h3 style="margin: 0 0 0.25rem 0; font-size: 0.875rem; font-weight: 500;">Выручка за месяц</h3>
            <div style="font-size: 1.75rem; font-weight: bold; color: #16a34a; margin: 0;">
                {{ number_format($monthlyRevenue, 0, ',', ' ') }} ₽
            </div>
        </div>
    </div>
    
    <div style="margin-top: 3rem;">
        <h2>Последние заказы</h2>
        @if($recentOrders->count() > 0)
        <table style="width: 100%; margin-top: 1rem; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                    <th style="text-align: left; padding: 1rem;">Номер</th>
                    <th style="text-align: left; padding: 1rem;">Email</th>
                    <th style="text-align: right; padding: 1rem;">Сумма</th>
                    <th style="text-align: center; padding: 1rem;">Статус</th>
                    <th style="text-align: center; padding: 1rem;">Оплата</th>
                    <th style="text-align: center; padding: 1rem;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentOrders as $order)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 1rem;"><strong>{{ $order->number }}</strong></td>
                    <td style="padding: 1rem;">{{ $order->email }}</td>
                    <td style="padding: 1rem; text-align: right;">{{ number_format($order->total, 0, ',', ' ') }} ₽</td>
                    <td style="padding: 1rem; text-align: center;">{{ $order->status }}</td>
                    <td style="padding: 1rem; text-align: center;">{{ $order->payment_status }}</td>
                    <td style="padding: 1rem; text-align: center;">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="btn" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Просмотр</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <p style="color: #6b7280; margin-top: 1rem;">Заказов пока нет</p>
        @endif
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Админ-панель - EInvestor')

@section('content')
<div class="content">
    <h1>Админ-панель</h1>
    
    @if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        {{ session('success') }}
    </div>
    @endif
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
        <div class="card">
            <h3 style="margin-bottom: 0.5rem;">Всего заказов</h3>
            <div style="font-size: 2rem; font-weight: bold; color: #2563eb; margin: 1rem 0;">
                {{ $stats['orders_total'] }}
            </div>
        </div>
        
        <div class="card">
            <h3 style="margin-bottom: 0.5rem;">Ожидают обработки</h3>
            <div style="font-size: 2rem; font-weight: bold; color: #f59e0b; margin: 1rem 0;">
                {{ $stats['orders_pending'] }}
            </div>
        </div>
        
        <div class="card">
            <h3 style="margin-bottom: 0.5rem;">Оплачено</h3>
            <div style="font-size: 2rem; font-weight: bold; color: #16a34a; margin: 1rem 0;">
                {{ $stats['orders_paid'] }}
            </div>
        </div>
        
        <div class="card">
            <h3 style="margin-bottom: 0.5rem;">Товаров</h3>
            <div style="font-size: 2rem; font-weight: bold; color: #2563eb; margin: 1rem 0;">
                {{ $stats['products_total'] }} / {{ $stats['products_published'] }}
            </div>
            <div style="color: #6b7280; font-size: 0.875rem;">Всего / Опубликовано</div>
        </div>
        
        <div class="card">
            <h3 style="margin-bottom: 0.5rem;">Постов</h3>
            <div style="font-size: 2rem; font-weight: bold; color: #2563eb; margin: 1rem 0;">
                {{ $stats['posts_total'] }} / {{ $stats['posts_published'] }}
            </div>
            <div style="color: #6b7280; font-size: 0.875rem;">Всего / Опубликовано</div>
        </div>
        
        <div class="card">
            <h3 style="margin-bottom: 0.5rem;">Выручка за месяц</h3>
            <div style="font-size: 2rem; font-weight: bold; color: #16a34a; margin: 1rem 0;">
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
    
    <div style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid #e5e7eb;">
        <h2>Быстрые действия</h2>
        <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
            <a href="{{ route('admin.orders.index') }}" class="btn">Управление заказами</a>
            <a href="{{ route('admin.products.index') }}" class="btn" style="background: #16a34a;">Управление товарами</a>
            <a href="{{ route('admin.posts.index') }}" class="btn" style="background: #6b7280;">Управление постами</a>
        </div>
    </div>
</div>
@endsection

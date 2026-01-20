@extends('layouts.app')

@section('title', 'Мои заказы - EInvestor')

@section('content')
<div class="content">
    <h1>Мои заказы</h1>
    
    <div style="margin-top: 2rem; padding-bottom: 2rem; border-bottom: 2px solid #e5e7eb;">
        <h2>Быстрые ссылки</h2>
        <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
            <a href="{{ route('account.index') }}" class="btn">Главная</a>
            <a href="{{ route('account.orders') }}" class="btn" style="background: #2563eb;">Мои заказы</a>
            <a href="{{ route('account.downloads') }}" class="btn">Мои файлы</a>
            <a href="{{ route('account.profile') }}" class="btn" style="background: #6b7280;">Профиль</a>
            <a href="{{ route('products.index') }}" class="btn" style="background: #16a34a;">Перейти к товарам</a>
        </div>
    </div>
    
    <div style="margin-top: 2rem;">
        @if($orders->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                    <th style="text-align: left; padding: 1rem;">Номер заказа</th>
                    <th style="text-align: left; padding: 1rem;">Дата</th>
                    <th style="text-align: left; padding: 1rem;">Сумма</th>
                    <th style="text-align: center; padding: 1rem;">Статус</th>
                    <th style="text-align: center; padding: 1rem;">Оплата</th>
                    <th style="text-align: center; padding: 1rem;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 1rem;">
                        <strong>{{ $order->number }}</strong>
                    </td>
                    <td style="padding: 1rem;">
                        {{ $order->created_at->format('d.m.Y H:i') }}
                    </td>
                    <td style="padding: 1rem;">
                        {{ number_format($order->total, 0, ',', ' ') }} ₽
                    </td>
                    <td style="padding: 1rem; text-align: center;">
                        @if($order->status === 'pending')
                            <span style="background: #fef3c7; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">Ожидает</span>
                        @elseif($order->status === 'processing')
                            <span style="background: #dbeafe; color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">Обрабатывается</span>
                        @elseif($order->status === 'completed')
                            <span style="background: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">Завершен</span>
                        @else
                            <span style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">{{ $order->status }}</span>
                        @endif
                    </td>
                    <td style="padding: 1rem; text-align: center;">
                        @if($order->payment_status === 'paid')
                            <span style="background: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">Оплачен</span>
                        @else
                            <span style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">Не оплачен</span>
                        @endif
                    </td>
                    <td style="padding: 1rem; text-align: center;">
                        <a href="{{ route('account.order', $order->number) }}" class="btn" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Детали</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 2rem;">
            {{ $orders->links() }}
        </div>
        @else
        <div style="text-align: center; padding: 3rem 0;">
            <p style="font-size: 1.25rem; color: #6b7280; margin-bottom: 1rem;">У вас пока нет заказов</p>
            <a href="{{ route('products.index') }}" class="btn">Перейти к товарам</a>
        </div>
        @endif
    </div>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Заказ ' . $order->number . ' - EInvestor')

@section('content')
<div class="content">
    <h1>Заказ #{{ $order->number }}</h1>
    
    <div style="margin-top: 2rem; padding-bottom: 2rem; border-bottom: 2px solid #e5e7eb;">
        <h2>Быстрые ссылки</h2>
        <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
            <a href="{{ route('account.index') }}" class="btn">Главная</a>
            <a href="{{ route('account.orders') }}" class="btn">Мои заказы</a>
            <a href="{{ route('account.downloads') }}" class="btn">Мои файлы</a>
            <a href="{{ route('account.profile') }}" class="btn" style="background: #6b7280;">Профиль</a>
            <a href="{{ route('products.index') }}" class="btn" style="background: #16a34a;">Перейти к товарам</a>
        </div>
    </div>
    
    <div style="margin-top: 2rem; display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        <div>
            <h2>Товары</h2>
            <table style="width: 100%; margin-top: 1rem; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                        <th style="text-align: left; padding: 1rem;">Товар</th>
                        <th style="text-align: center; padding: 1rem;">Количество</th>
                        <th style="text-align: right; padding: 1rem;">Цена</th>
                        <th style="text-align: right; padding: 1rem;">Сумма</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 1rem;">
                            @if($item->product)
                            <a href="{{ route('products.show', $item->product->slug) }}" style="text-decoration: none; color: #333;">
                                <strong>{{ $item->product_name }}</strong>
                            </a>
                            @else
                            <strong>{{ $item->product_name }}</strong>
                            @endif
                        </td>
                        <td style="padding: 1rem; text-align: center;">
                            {{ $item->quantity }}
                        </td>
                        <td style="padding: 1rem; text-align: right;">
                            {{ number_format($item->price, 0, ',', ' ') }} ₽
                        </td>
                        <td style="padding: 1rem; text-align: right;">
                            <strong>{{ number_format($item->subtotal, 0, ',', ' ') }} ₽</strong>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" style="padding: 1rem; text-align: right;"><strong>Итого:</strong></td>
                        <td style="padding: 1rem; text-align: right;">
                            <strong style="font-size: 1.25rem; color: #2563eb;">{{ number_format($order->total, 0, ',', ' ') }} ₽</strong>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        
        <div>
            <h2>Информация о заказе</h2>
            <div style="background: #f9fafb; padding: 1.5rem; border-radius: 8px; margin-top: 1rem;">
                <div style="margin-bottom: 1rem; line-height: 1.8;">
                    <div><strong>Номер заказа:</strong> {{ $order->number }}</div>
                    <div><strong>Дата:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</div>
                    <div><strong>Email:</strong> {{ $order->email }}</div>
                    @if($order->name)
                    <div><strong>Имя:</strong> {{ $order->name }}</div>
                    @endif
                    @if($order->phone)
                    <div><strong>Телефон:</strong> {{ $order->phone }}</div>
                    @endif
                </div>
                
                <div style="padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                    <div style="margin-bottom: 0.5rem;">
                        <strong>Статус:</strong> 
                        @if($order->status === 'pending')
                            <span style="background: #fef3c7; color: #92400e; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">Ожидает</span>
                        @elseif($order->status === 'processing')
                            <span style="background: #dbeafe; color: #1e40af; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">Обрабатывается</span>
                        @elseif($order->status === 'completed')
                            <span style="background: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">Завершен</span>
                        @else
                            <span>{{ $order->status }}</span>
                        @endif
                    </div>
                    <div>
                        <strong>Оплата:</strong> 
                        @if($order->payment_status === 'paid')
                            <span style="background: #d1fae5; color: #065f46; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">Оплачен</span>
                        @else
                            <span style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.75rem; border-radius: 12px; font-size: 0.875rem;">Не оплачен</span>
                        @endif
                    </div>
                </div>
            </div>
            
            @if($order->isPaid() && $order->downloads->count() > 0)
            <div style="margin-top: 1.5rem;">
                <h3>Файлы для скачивания</h3>
                <div style="margin-top: 1rem;">
                    <a href="{{ route('account.downloads') }}" class="btn">Перейти к файлам</a>
                </div>
            </div>
            @endif
        </div>
    </div>
    
    <div style="margin-top: 2rem;">
        <a href="{{ route('account.orders') }}" style="color: #2563eb; text-decoration: underline;">← Вернуться к заказам</a>
    </div>
</div>
@endsection

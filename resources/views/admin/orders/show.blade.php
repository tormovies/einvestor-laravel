@extends('layouts.app')

@section('title', 'Заказ ' . $order->number . ' - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <h1>Заказ #{{ $order->number }}</h1>
    
    @if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        {{ session('success') }}
    </div>
    @endif
    
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
                            <strong>{{ $item->product_name }}</strong>
                        </td>
                        <td style="padding: 1rem; text-align: center;">{{ $item->quantity }}</td>
                        <td style="padding: 1rem; text-align: right;">{{ number_format($item->price, 0, ',', ' ') }} ₽</td>
                        <td style="padding: 1rem; text-align: right;"><strong>{{ number_format($item->subtotal, 0, ',', ' ') }} ₽</strong></td>
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
                    <div><strong>Номер:</strong> {{ $order->number }}</div>
                    <div><strong>Дата:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</div>
                    <div><strong>Email:</strong> {{ $order->email }}</div>
                    @if($order->name)
                    <div><strong>Имя:</strong> {{ $order->name }}</div>
                    @endif
                    @if($order->phone)
                    <div><strong>Телефон:</strong> {{ $order->phone }}</div>
                    @endif
                </div>
            </div>
            
            <div style="margin-top: 1.5rem;">
                <h3>Изменение статуса</h3>
                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST" style="margin-top: 1rem;">
                    @csrf
                    @method('PUT')
                    <select name="status" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; margin-bottom: 1rem;">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Ожидает</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Обрабатывается</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Завершен</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Отменен</option>
                        <option value="refunded" {{ $order->status === 'refunded' ? 'selected' : '' }}>Возврат</option>
                    </select>
                    <button type="submit" class="btn" style="width: 100%;">Обновить статус</button>
                </form>
                
                <div style="margin-top: 1rem; padding: 1rem; background: #fef3c7; border: 1px solid #f59e0b; border-radius: 4px;">
                    <div><strong>Статус оплаты:</strong> {{ $order->payment_status }}</div>
                    <div style="font-size: 0.875rem; color: #92400e; margin-top: 0.5rem;">
                        Статус оплаты обновляется автоматически через webhook от Робокассы
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div style="margin-top: 2rem;">
        <a href="{{ route('admin.orders.index') }}" style="color: #2563eb; text-decoration: underline;">← Вернуться к списку заказов</a>
    </div>
</div>
@endsection

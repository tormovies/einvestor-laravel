@extends('layouts.app')

@section('title', 'Заказ создан - EInvestor')

@section('content')
<div class="content">
    <div style="text-align: center; padding: 2rem 0;">
        <h1 style="color: #16a34a; margin-bottom: 1rem;">✓ Заказ создан!</h1>
        
        <div style="background: #f0fdf4; border: 2px solid #16a34a; border-radius: 8px; padding: 2rem; margin: 2rem auto; max-width: 600px;">
            <h2 style="margin-bottom: 1rem;">Номер заказа: {{ $order->number }}</h2>
            
            <p style="margin-bottom: 1rem; color: #6b7280;">
                На email <strong>{{ $order->email }}</strong> отправлена информация о заказе.
            </p>
            
            @if($order->user_id)
            <p style="margin-bottom: 1.5rem; color: #6b7280;">
                Пароль для входа в личный кабинет отправлен на ваш email.
            </p>
            @else
            <p style="margin-bottom: 1.5rem; color: #6b7280;">
                Пароль для входа в личный кабинет будет отправлен на ваш email после оплаты.
            </p>
            @endif
            
            <div style="background: #fff; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: left;">
                <h3 style="margin-bottom: 1rem;">Информация о заказе:</h3>
                <div style="line-height: 2;">
                    <div><strong>Сумма:</strong> {{ number_format($order->total, 0, ',', ' ') }} ₽</div>
                    <div><strong>Статус:</strong> {{ $order->status === 'pending' ? 'Ожидает оплаты' : $order->status }}</div>
                    <div><strong>Статус оплаты:</strong> {{ $order->payment_status === 'pending' ? 'Не оплачен' : $order->payment_status }}</div>
                </div>
            </div>
            
            <p style="color: #6b7280; margin-bottom: 2rem;">
                После оплаты вы получите доступ к файлам в личном кабинете.
            </p>
            
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('home') }}" class="btn">На главную</a>
                <a href="{{ route('products.index') }}" class="btn" style="background: #6b7280;">Продолжить покупки</a>
            </div>
        </div>
        
        @if($order->payment_status === 'pending')
        <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 8px; padding: 1.5rem; margin-top: 2rem; max-width: 600px; margin-left: auto; margin-right: auto;">
            <p style="color: #92400e; margin: 0;">
                <strong>Внимание!</strong> Оплата через Робокассу будет реализована позже. 
                Заказ сохранен в базе данных с номером {{ $order->number }}.
            </p>
        </div>
        @endif
    </div>
</div>
@endsection

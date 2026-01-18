@extends('layouts.app')

@section('title', 'Ошибка оплаты - EInvestor')

@section('content')
<div class="content">
    <div style="text-align: center; padding: 2rem 0;">
        <h1 style="color: #dc2626; margin-bottom: 1rem;">✗ Оплата не завершена</h1>
        
        <div style="background: #fef2f2; border: 2px solid #dc2626; border-radius: 8px; padding: 2rem; margin: 2rem auto; max-width: 600px;">
            <p style="margin-bottom: 1.5rem; color: #6b7280;">
                К сожалению, произошла ошибка при обработке платежа.
            </p>
            
            @if($order)
            <div style="background: #fff; padding: 1.5rem; border-radius: 8px; margin-bottom: 1.5rem; text-align: left;">
                <h3 style="margin-bottom: 1rem;">Информация о заказе:</h3>
                <div style="line-height: 2;">
                    <div><strong>Номер заказа:</strong> {{ $order->number }}</div>
                    <div><strong>Сумма:</strong> {{ number_format($order->total, 0, ',', ' ') }} ₽</div>
                </div>
            </div>
            @endif
            
            <p style="color: #6b7280; margin-bottom: 2rem;">
                Вы можете попробовать оплатить заказ снова или обратиться в службу поддержки.
            </p>
            
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="{{ route('home') }}" class="btn">На главную</a>
                @if($order)
                <a href="{{ route('checkout.index') }}" class="btn" style="background: #dc2626;">Попробовать снова</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

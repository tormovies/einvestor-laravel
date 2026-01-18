@extends('layouts.app')

@section('title', 'Корзина - EInvestor')

@section('content')
<div class="content">
    <h1>Корзина</h1>
    
    @if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        {{ session('success') }}
    </div>
    @endif
    
    @if(session('error'))
    <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        {{ session('error') }}
    </div>
    @endif
    
    @if(count($items) > 0)
    <table style="width: 100%; margin-top: 2rem; border-collapse: collapse;">
        <thead>
            <tr style="border-bottom: 2px solid #e5e7eb;">
                <th style="text-align: left; padding: 1rem;">Товар</th>
                <th style="text-align: left; padding: 1rem;">Цена</th>
                <th style="text-align: center; padding: 1rem;">Количество</th>
                <th style="text-align: right; padding: 1rem;">Сумма</th>
                <th style="text-align: center; padding: 1rem;">Действия</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $productId => $item)
            <tr style="border-bottom: 1px solid #e5e7eb;">
                <td style="padding: 1rem;">
                    <a href="{{ route('products.show', $item['product']->slug) }}" style="text-decoration: none; color: #333;">
                        <strong>{{ $item['product']->name }}</strong>
                    </a>
                </td>
                <td style="padding: 1rem;">{{ number_format($item['product']->price, 0, ',', ' ') }} ₽</td>
                <td style="padding: 1rem; text-align: center;">
                    <form action="{{ route('cart.update', $productId) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('PUT')
                        <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" style="width: 60px; padding: 0.25rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                        <button type="submit" style="margin-left: 0.5rem; padding: 0.25rem 0.5rem; background: #2563eb; color: white; border: none; border-radius: 4px; cursor: pointer;">Обновить</button>
                    </form>
                </td>
                <td style="padding: 1rem; text-align: right;">
                    <strong>{{ number_format($item['subtotal'], 0, ',', ' ') }} ₽</strong>
                </td>
                <td style="padding: 1rem; text-align: center;">
                    <form action="{{ route('cart.remove', $productId) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" style="padding: 0.5rem 1rem; background: #dc2626; color: white; border: none; border-radius: 4px; cursor: pointer;">Удалить</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="padding: 1rem; text-align: right;"><strong>Итого:</strong></td>
                <td style="padding: 1rem; text-align: right;">
                    <strong style="font-size: 1.25rem; color: #2563eb;">{{ number_format($total, 0, ',', ' ') }} ₽</strong>
                </td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    
    <div style="margin-top: 2rem; display: flex; gap: 1rem; justify-content: space-between; align-items: center;">
        <div>
            <form action="{{ route('cart.clear') }}" method="POST" style="display: inline-block;">
                @csrf
                <button type="submit" style="padding: 0.75rem 1.5rem; background: #6b7280; color: white; border: none; border-radius: 4px; cursor: pointer;">Очистить корзину</button>
            </form>
        </div>
        <div>
            <a href="{{ route('checkout.index') }}" class="btn" style="font-size: 1.1rem; padding: 0.75rem 2rem;">Оформить заказ</a>
        </div>
    </div>
    @else
    <div style="text-align: center; padding: 3rem 0;">
        <p style="font-size: 1.25rem; color: #6b7280; margin-bottom: 1rem;">Корзина пуста</p>
        <a href="{{ route('products.index') }}" class="btn">Перейти к товарам</a>
    </div>
    @endif
</div>
@endsection

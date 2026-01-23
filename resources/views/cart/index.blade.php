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
    <div class="cart-container">
        <table class="cart-table">
            <thead>
                <tr>
                    <th>Товар</th>
                    <th>Цена</th>
                    <th>Количество</th>
                    <th>Сумма</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($items as $productId => $item)
                <tr>
                    <td data-label="Товар">
                        <a href="{{ route('products.show', $item['product']->slug) }}" class="cart-product-link">
                            <strong>{{ $item['product']->name }}</strong>
                        </a>
                    </td>
                    <td data-label="Цена">{{ number_format($item['product']->price, 0, ',', ' ') }} ₽</td>
                    <td data-label="Количество">
                        <form action="{{ route('cart.update', $productId) }}" method="POST" class="cart-quantity-form">
                            @csrf
                            @method('PUT')
                            <input type="number" name="quantity" value="{{ $item['quantity'] }}" min="1" class="cart-quantity-input">
                            <button type="submit" class="cart-update-btn">Обновить</button>
                        </form>
                    </td>
                    <td data-label="Сумма">
                        <strong>{{ number_format($item['subtotal'], 0, ',', ' ') }} ₽</strong>
                    </td>
                    <td data-label="Действия">
                        <form action="{{ route('cart.remove', $productId) }}" method="POST" class="cart-remove-form">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="cart-remove-btn">Удалить</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="cart-total-label"><strong>Итого:</strong></td>
                    <td class="cart-total-amount">
                        <strong>{{ number_format($total, 0, ',', ' ') }} ₽</strong>
                    </td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
        
        <div class="cart-actions">
            <form action="{{ route('cart.clear') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-secondary">Очистить корзину</button>
            </form>
            <a href="{{ route('checkout.index') }}" class="btn btn-primary">Оформить заказ</a>
        </div>
    </div>
    @else
    <div style="text-align: center; padding: 3rem 0;">
        <p style="font-size: 1.25rem; color: #6b7280; margin-bottom: 1rem;">Корзина пуста</p>
        <a href="{{ route('products.index') }}" class="btn">Перейти к товарам</a>
    </div>
    @endif
</div>

@push('styles')
<style>
.cart-container {
    margin-top: 2rem;
}

.cart-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.cart-table thead {
    background: #f9fafb;
}

.cart-table th {
    text-align: left;
    padding: 1rem;
    font-weight: 600;
    color: #374151;
    border-bottom: 2px solid #e5e7eb;
}

.cart-table th:nth-child(3),
.cart-table th:nth-child(5) {
    text-align: center;
}

.cart-table th:nth-child(4) {
    text-align: right;
}

.cart-table td {
    padding: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.cart-table tbody tr:hover {
    background: #f9fafb;
}

.cart-table tbody tr:last-child td {
    border-bottom: none;
}

.cart-product-link {
    text-decoration: none;
    color: #333;
    transition: color 0.2s;
}

.cart-product-link:hover {
    color: #2563eb;
}

.cart-quantity-form {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    justify-content: center;
}

.cart-quantity-input {
    width: 60px;
    padding: 0.5rem;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    text-align: center;
    font-size: 0.875rem;
}

.cart-update-btn {
    padding: 0.5rem 1rem;
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.875rem;
    transition: background 0.2s;
}

.cart-update-btn:hover {
    background: #1d4ed8;
}

.cart-remove-form {
    display: inline-block;
}

.cart-remove-btn {
    padding: 0.5rem 1rem;
    background: #dc2626;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.875rem;
    transition: background 0.2s;
}

.cart-remove-btn:hover {
    background: #b91c1c;
}

.cart-table tfoot td {
    background: #f9fafb;
    font-weight: 600;
}

.cart-total-label {
    text-align: right;
}

.cart-total-amount {
    text-align: right;
    font-size: 1.25rem;
    color: #2563eb;
}

.cart-actions {
    display: flex;
    gap: 1rem;
    justify-content: space-between;
    align-items: center;
    margin-top: 2rem;
    flex-wrap: wrap;
}

.btn-secondary {
    background: #6b7280;
    color: white;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    transition: background 0.3s;
}

.btn-secondary:hover {
    background: #4b5563;
}

.btn-primary {
    background: #2563eb;
    color: white;
    padding: 0.75rem 2rem;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    text-decoration: none;
    display: inline-block;
    font-size: 1.1rem;
    transition: background 0.3s;
}

.btn-primary:hover {
    background: #1d4ed8;
}

/* Адаптивность для мобильных */
@media (max-width: 768px) {
    .cart-table {
        display: block;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }
    
    .cart-table thead {
        display: none;
    }
    
    .cart-table tbody,
    .cart-table tfoot {
        display: block;
    }
    
    .cart-table tbody tr {
        display: block;
        margin-bottom: 1rem;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 1rem;
        background: white;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    
    .cart-table tbody tr:last-child {
        margin-bottom: 0;
    }
    
    .cart-table td {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid #e5e7eb;
        text-align: right;
    }
    
    .cart-table td:last-child {
        border-bottom: none;
    }
    
    .cart-table td::before {
        content: attr(data-label);
        font-weight: 600;
        color: #6b7280;
        text-align: left;
        margin-right: 1rem;
    }
    
    .cart-table td[data-label="Количество"] {
        flex-direction: column;
        align-items: stretch;
        gap: 0.5rem;
    }
    
    .cart-table td[data-label="Количество"]::before {
        margin-bottom: 0.5rem;
    }
    
    .cart-quantity-form {
        width: 100%;
        justify-content: stretch;
    }
    
    .cart-quantity-input {
        flex: 1;
    }
    
    .cart-table tfoot tr {
        display: block;
        margin-top: 1rem;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }
    
    .cart-table tfoot td {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        border-bottom: none;
    }
    
    .cart-table tfoot td:first-child {
        font-size: 1.1rem;
    }
    
    .cart-table tfoot td:last-child {
        font-size: 1.5rem;
    }
    
    .cart-total-label {
        text-align: left;
    }
    
    .cart-total-amount {
        text-align: right;
    }
    
    .cart-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .cart-actions form,
    .cart-actions a {
        width: 100%;
        text-align: center;
    }
    
    .btn-secondary,
    .btn-primary {
        width: 100%;
        display: block;
    }
}

@media (max-width: 480px) {
    .cart-table td {
        font-size: 0.875rem;
    }
    
    .cart-quantity-input {
        width: 50px;
        font-size: 0.875rem;
    }
    
    .cart-update-btn,
    .cart-remove-btn {
        padding: 0.5rem 0.75rem;
        font-size: 0.8125rem;
    }
}
</style>
@endpush
@endsection

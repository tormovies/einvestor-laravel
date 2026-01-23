@extends('layouts.app')

@section('title', 'Оформление заказа - EInvestor')

@section('content')
<div class="content">
    <h1>Оформление заказа</h1>
    
    @if(session('error'))
    <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        {{ session('error') }}
    </div>
    @endif
    
    <div class="checkout-container">
        <div class="checkout-form-section">
            <h2>Данные для заказа</h2>
            
            <form action="{{ route('checkout.store') }}" method="POST" class="checkout-form">
                @csrf
                
                <div class="form-group">
                    <label for="email">
                        Email <span class="required">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required class="form-input">
                    @error('email')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                    <p class="form-hint">
                        На этот email придет информация о заказе и пароль для доступа к файлам
                    </p>
                </div>
                
                <div class="form-group">
                    <label for="name">Имя</label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-input">
                    @error('name')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="phone">Телефон</label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+7 (999) 123-45-67" class="form-input">
                    @error('phone')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="notes">Комментарий к заказу</label>
                    <textarea id="notes" name="notes" rows="4" class="form-textarea">{{ old('notes') }}</textarea>
                    @error('notes')
                    <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-submit">
                    Перейти к оплате
                </button>
            </form>
        </div>
        
        <div class="checkout-order-section">
            <h2>Ваш заказ</h2>
            
            <div class="order-summary">
                @foreach($items as $productId => $item)
                <div class="order-item">
                    <div class="order-item-header">
                        <span class="order-item-name"><strong>{{ $item['product']->name }}</strong></span>
                        <span class="order-item-price">{{ number_format($item['subtotal'], 0, ',', ' ') }} ₽</span>
                    </div>
                    <div class="order-item-details">
                        {{ number_format($item['product']->price, 0, ',', ' ') }} ₽ × {{ $item['quantity'] }}
                    </div>
                </div>
                @endforeach
                
                <div class="order-total">
                    <div class="order-total-row">
                        <strong>Итого:</strong>
                        <strong class="order-total-amount">{{ number_format($total, 0, ',', ' ') }} ₽</strong>
                    </div>
                </div>
            </div>
            
            <div class="checkout-back-link">
                <a href="{{ route('cart.index') }}">← Вернуться в корзину</a>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.checkout-container {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 2rem;
    margin-top: 2rem;
}

.checkout-form-section {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.checkout-order-section {
    background: white;
    padding: 2rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    height: fit-content;
    position: sticky;
    top: 20px;
}

.checkout-form {
    margin-top: 1rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: #374151;
}

.required {
    color: red;
}

.form-input,
.form-textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #e5e7eb;
    border-radius: 4px;
    font-size: 1rem;
    font-family: inherit;
    transition: border-color 0.2s;
}

.form-input:focus,
.form-textarea:focus {
    outline: none;
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 100px;
}

.error-message {
    color: red;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.form-hint {
    color: #6b7280;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.btn-submit {
    width: 100%;
    font-size: 1.1rem;
    padding: 1rem;
    background: #2563eb;
    color: white;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    transition: background 0.3s;
    font-weight: 600;
}

.btn-submit:hover {
    background: #1d4ed8;
}

.order-summary {
    background: #f9fafb;
    padding: 1.5rem;
    border-radius: 8px;
    margin-top: 1rem;
}

.order-item {
    margin-bottom: 1rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid #e5e7eb;
}

.order-item:last-of-type {
    margin-bottom: 0;
    padding-bottom: 0;
    border-bottom: none;
}

.order-item-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
    gap: 1rem;
}

.order-item-name {
    flex: 1;
    word-break: break-word;
}

.order-item-price {
    font-weight: 600;
    color: #2563eb;
    white-space: nowrap;
}

.order-item-details {
    color: #6b7280;
    font-size: 0.875rem;
}

.order-total {
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 2px solid #e5e7eb;
}

.order-total-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 1.25rem;
}

.order-total-amount {
    color: #2563eb;
}

.checkout-back-link {
    margin-top: 1rem;
    text-align: center;
}

.checkout-back-link a {
    color: #2563eb;
    text-decoration: underline;
    transition: color 0.2s;
}

.checkout-back-link a:hover {
    color: #1d4ed8;
}

/* Адаптивность */
@media (max-width: 968px) {
    .checkout-container {
        grid-template-columns: 1fr;
        gap: 2rem;
    }
    
    .checkout-order-section {
        position: static;
        order: -1;
    }
}

@media (max-width: 768px) {
    .checkout-form-section,
    .checkout-order-section {
        padding: 1.5rem;
    }
    
    .order-item-header {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .order-item-price {
        align-self: flex-end;
    }
}

@media (max-width: 480px) {
    .checkout-form-section,
    .checkout-order-section {
        padding: 1rem;
    }
    
    .form-group {
        margin-bottom: 1.25rem;
    }
    
    .form-input,
    .form-textarea {
        padding: 0.625rem;
        font-size: 0.9375rem;
    }
    
    .btn-submit {
        font-size: 1rem;
        padding: 0.875rem;
    }
    
    .order-summary {
        padding: 1rem;
    }
    
    .order-total-row {
        font-size: 1.1rem;
    }
}
</style>
@endpush
@endsection

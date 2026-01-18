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
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem; margin-top: 2rem;">
        <div>
            <h2>Данные для заказа</h2>
            
            <form action="{{ route('checkout.store') }}" method="POST" style="margin-top: 1rem;">
                @csrf
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">
                        Email <span style="color: red;">*</span>
                    </label>
                    <input type="email" id="email" name="email" value="{{ old('email') }}" required
                           style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; font-size: 1rem;">
                    @error('email')
                    <div style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                    <p style="color: #6b7280; font-size: 0.875rem; margin-top: 0.25rem;">
                        На этот email придет информация о заказе и пароль для доступа к файлам
                    </p>
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="name" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">
                        Имя
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; font-size: 1rem;">
                    @error('name')
                    <div style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="phone" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">
                        Телефон
                    </label>
                    <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+7 (999) 123-45-67"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; font-size: 1rem;">
                    @error('phone')
                    <div style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                
                <div style="margin-bottom: 1.5rem;">
                    <label for="notes" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">
                        Комментарий к заказу
                    </label>
                    <textarea id="notes" name="notes" rows="4"
                              style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; font-size: 1rem; font-family: inherit;">{{ old('notes') }}</textarea>
                    @error('notes')
                    <div style="color: red; font-size: 0.875rem; margin-top: 0.25rem;">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn" style="width: 100%; font-size: 1.1rem; padding: 1rem;">
                    Перейти к оплате
                </button>
            </form>
        </div>
        
        <div>
            <h2>Ваш заказ</h2>
            
            <div style="background: #f9fafb; padding: 1.5rem; border-radius: 8px; margin-top: 1rem;">
                @foreach($items as $productId => $item)
                <div style="margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid #e5e7eb;">
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span><strong>{{ $item['product']->name }}</strong></span>
                        <span>{{ number_format($item['subtotal'], 0, ',', ' ') }} ₽</span>
                    </div>
                    <div style="color: #6b7280; font-size: 0.875rem;">
                        {{ number_format($item['product']->price, 0, ',', ' ') }} ₽ × {{ $item['quantity'] }}
                    </div>
                </div>
                @endforeach
                
                <div style="margin-top: 1rem; padding-top: 1rem; border-top: 2px solid #e5e7eb;">
                    <div style="display: flex; justify-content: space-between; font-size: 1.25rem;">
                        <strong>Итого:</strong>
                        <strong style="color: #2563eb;">{{ number_format($total, 0, ',', ' ') }} ₽</strong>
                    </div>
                </div>
            </div>
            
            <div style="margin-top: 1rem;">
                <a href="{{ route('cart.index') }}" style="color: #2563eb; text-decoration: underline;">← Вернуться в корзину</a>
            </div>
        </div>
    </div>
</div>
@endsection

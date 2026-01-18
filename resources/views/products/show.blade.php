@extends('layouts.app')

@section('title', $product->name . ' - EInvestor')

@section('content')
<div class="content">
    <article>
        <h1>{{ $product->name }}</h1>
        
        <div style="display: flex; gap: 2rem; margin-top: 2rem;">
            <div style="flex: 1;">
                @if($product->description)
                <div style="line-height: 1.8; margin-bottom: 2rem;">
                    {!! $product->description !!}
                </div>
                @endif
                
                <div style="background: #f9fafb; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                    <div class="price" style="font-size: 2rem; margin-bottom: 1rem;">
                        {{ number_format($product->price, 0, ',', ' ') }} ₽
                    </div>
                    
                    @if($product->isInStock())
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" style="margin-top: 1rem;">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn">Добавить в корзину</button>
                    </form>
                    @else
                    <p style="color: red; margin-top: 1rem;">Товар временно недоступен</p>
                    @endif
                </div>
                
                @if($product->categories->count() > 0)
                <div style="margin-top: 2rem;">
                    <strong>Категории:</strong>
                    @foreach($product->categories as $category)
                    <a href="{{ route('category.show', $category->slug) }}" style="margin-left: 0.5rem;">{{ $category->name }}</a>@if(!$loop->last), @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </article>
    
    @if($relatedProducts->count() > 0)
    <section style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid #e5e7eb;">
        <h2>Похожие товары</h2>
        <div class="grid">
            @foreach($relatedProducts as $relatedProduct)
            <div class="card">
                <h3 class="card-title">
                    <a href="{{ route('products.show', $relatedProduct->slug) }}">{{ $relatedProduct->name }}</a>
                </h3>
                <div class="price">{{ number_format($relatedProduct->price, 0, ',', ' ') }} ₽</div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection

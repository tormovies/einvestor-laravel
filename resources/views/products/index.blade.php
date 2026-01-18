@extends('layouts.app')

@section('title', 'Товары - EInvestor')

@section('content')
<div class="content">
    <h1>Каталог товаров</h1>
    
    @if($products->count() > 0)
    <div class="grid">
        @foreach($products as $product)
        <div class="card">
            <h3 class="card-title">
                <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
            </h3>
            @if($product->short_description)
            <p>{{ Str::limit(strip_tags($product->short_description), 150) }}</p>
            @endif
            <div class="price">{{ number_format($product->price, 0, ',', ' ') }} ₽</div>
            <div class="meta" style="margin-top: 0.5rem;">
                @if($product->isInStock())
                <span style="color: green;">В наличии</span>
                @else
                <span style="color: red;">Нет в наличии</span>
                @endif
            </div>
        </div>
        @endforeach
    </div>
    
    <div style="margin-top: 2rem;">
        {{ $products->links() }}
    </div>
    @else
    <p>Товаров пока нет.</p>
    @endif
</div>
@endsection

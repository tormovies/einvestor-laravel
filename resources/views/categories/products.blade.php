@extends('layouts.app')

@section('title', $category->name . ' - Категория товаров - EInvestor')

@section('content')
<div class="content">
    <h1>Категория: {{ $category->name }}</h1>
    
    @if($category->description)
    <p style="margin-top: 1rem; color: #6b7280;">{{ $category->description }}</p>
    @endif
    
    @if($products->count() > 0)
    <div class="grid" style="margin-top: 2rem;">
        @foreach($products as $product)
        <div class="card">
            <h3 class="card-title">
                <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
            </h3>
            @if($product->short_description)
            <p>{{ Str::limit(strip_tags($product->short_description), 150) }}</p>
            @endif
            <div class="price">{{ number_format($product->price, 0, ',', ' ') }} ₽</div>
        </div>
        @endforeach
    </div>
    
    <div style="margin-top: 2rem;">
        {{ $products->links() }}
    </div>
    @else
    <p style="margin-top: 2rem;">В этой категории пока нет товаров.</p>
    @endif
</div>
@endsection

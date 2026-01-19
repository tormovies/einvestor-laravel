@extends('layouts.app')

@php
    $seoTitle = $systemPage->seo_title ?? 'Товары - EInvestor';
    $seoDescription = $systemPage->seo_description ?? 'Каталог товаров - скрипты и индикаторы для торговли';
    $seoH1 = $systemPage->seo_h1 ?? 'Каталог товаров';
@endphp

@section('seo')
    <x-seo-meta 
        :title="$seoTitle"
        :description="$seoDescription"
        :url="route('products.index')"
        type="website" />
@endsection

@section('title', $seoTitle)

@section('content')
<div class="content">
    <h1>{{ $seoH1 }}</h1>
    
    @if($systemPage && $systemPage->seo_intro_text)
    <div style="margin-top: 1rem; margin-bottom: 2rem; padding: 1rem; background: #f9fafb; border-radius: 8px; border-left: 3px solid #2563eb; font-size: 1.1rem; line-height: 1.6; color: #4b5563;">
        {{ $systemPage->seo_intro_text }}
    </div>
    @endif
    
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

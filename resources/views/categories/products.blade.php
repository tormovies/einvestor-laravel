@extends('layouts.app')

@php
    $seoTitle = $category->seo_title ?? $category->name . ' - Категория товаров - EInvestor';
    $seoDescription = $category->seo_description ?? $category->description ?? Str::limit('Товары в категории ' . $category->name, 160);
    $seoH1 = $category->seo_h1 ?? 'Категория: ' . $category->name;
@endphp

@section('seo')
    <x-seo-meta 
        :title="$seoTitle"
        :description="$seoDescription"
        :url="route('category.show', $category->slug)"
        type="website" />
@endsection

@section('title', $seoTitle)

@section('content')
<div class="content">
    <h1>{{ $seoH1 }}</h1>
    
    @if($category->seo_intro_text)
    <div style="margin-top: 1rem; margin-bottom: 2rem; padding: 1rem; background: #f9fafb; border-radius: 8px; border-left: 3px solid #2563eb; font-size: 1.1rem; line-height: 1.6; color: #4b5563;">
        {{ $category->seo_intro_text }}
    </div>
    @elseif($category->description)
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

@push('styles')
<style>
@media (max-width: 768px) {
    .content .grid {
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.5rem;
    }
}

@media (max-width: 480px) {
    .content .grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .content h1 {
        font-size: 1.75rem;
    }
}
</style>
@endpush
@endsection

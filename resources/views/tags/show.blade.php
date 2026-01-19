@extends('layouts.app')

@php
    $seoTitle = $tag->seo_title ?? $tag->name . ' - Тег - EInvestor';
    $seoDescription = $tag->seo_description ?? $tag->description ?? Str::limit('Материалы с тегом ' . $tag->name, 160);
    $seoH1 = $tag->seo_h1 ?? 'Тег: ' . $tag->name;
@endphp

@section('seo')
    <x-seo-meta 
        :title="$seoTitle"
        :description="$seoDescription"
        :url="route('tag.show', $tag->slug)"
        type="website" />
@endsection

@section('title', $seoTitle)

@section('content')
<div class="content">
    <h1>{{ $seoH1 }}</h1>
    
    @if($tag->seo_intro_text)
    <div style="margin-top: 1rem; margin-bottom: 2rem; padding: 1rem; background: #f9fafb; border-radius: 8px; border-left: 3px solid #2563eb; font-size: 1.1rem; line-height: 1.6; color: #4b5563;">
        {{ $tag->seo_intro_text }}
    </div>
    @elseif($tag->description)
    <p style="margin-top: 1rem; color: #6b7280;">{{ $tag->description }}</p>
    @endif
    
    @if($posts->count() > 0)
    <section style="margin-top: 2rem;">
        <h2>Статьи</h2>
        <div class="grid">
            @foreach($posts as $post)
            <div class="card">
                <h3 class="card-title">
                    <a href="{{ route('articles.show', $post->slug) }}">{{ $post->title }}</a>
                </h3>
            </div>
            @endforeach
        </div>
        {{ $posts->links() }}
    </section>
    @endif
    
    @if($products->count() > 0)
    <section style="margin-top: 3rem;">
        <h2>Товары</h2>
        <div class="grid">
            @foreach($products as $product)
            <div class="card">
                <h3 class="card-title">
                    <a href="{{ route('products.show', $product->slug) }}">{{ $product->name }}</a>
                </h3>
                <div class="price">{{ number_format($product->price, 0, ',', ' ') }} ₽</div>
            </div>
            @endforeach
        </div>
        {{ $products->links() }}
    </section>
    @endif
</div>
@endsection

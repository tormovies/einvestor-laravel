@extends('layouts.app')

@section('title', 'Главная - EInvestor')

@section('content')
<div class="content">
    <h1>Добро пожаловать в EInvestor</h1>
    <p>Магазин скриптов и индикаторов для торговли</p>
    
    @if($latestPosts->count() > 0)
    <section style="margin-top: 3rem;">
        <h2>Последние статьи</h2>
        <div class="grid">
            @foreach($latestPosts as $post)
            <div class="card">
                <h3 class="card-title">
                    <a href="{{ route('articles.show', $post->slug) }}">{{ $post->title }}</a>
                </h3>
                @if($post->excerpt)
                <p>{{ Str::limit(strip_tags($post->excerpt), 150) }}</p>
                @endif
                <div class="meta">
                    {{ $post->published_at?->format('d.m.Y') }}
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
    
    @if($featuredProducts->count() > 0)
    <section style="margin-top: 3rem;">
        <h2>Популярные товары</h2>
        <div class="grid">
            @foreach($featuredProducts as $product)
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
    </section>
    @endif
</div>
@endsection

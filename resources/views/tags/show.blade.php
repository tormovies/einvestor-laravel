@extends('layouts.app')

@section('title', $tag->name . ' - Тег - EInvestor')

@section('content')
<div class="content">
    <h1>Тег: {{ $tag->name }}</h1>
    
    @if($tag->description)
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

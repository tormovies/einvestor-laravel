@extends('layouts.app')

@php
    $homeSeoH1 = $systemPage->seo_h1 ?? config('app.home_seo_h1', 'EInvestor');
    $homeSeoIntroText = $systemPage->seo_intro_text ?? config('app.home_seo_intro_text', 'Магазин скриптов и индикаторов для профессиональной торговли');
    $homeSeoTitle = $systemPage->seo_title ?? config('app.home_seo_title', 'Главная - EInvestor');
    $homeSeoDescription = $systemPage->seo_description ?? config('app.home_seo_description', 'Магазин скриптов и индикаторов для торговли');
@endphp

@section('seo')
    <x-seo-meta 
        :title="$homeSeoTitle"
        :description="$homeSeoDescription"
        :url="route('home')"
        type="website" />
@endsection

@section('title', $homeSeoTitle)

@section('content')
<div class="home-page">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">{{ $homeSeoH1 }}</h1>
            @if($homeSeoIntroText)
            <p class="hero-subtitle">{{ $homeSeoIntroText }}</p>
            @endif
        </div>
        <div class="hero-decoration">
            <div class="hero-circle hero-circle-1"></div>
            <div class="hero-circle hero-circle-2"></div>
            <div class="hero-circle hero-circle-3"></div>
        </div>
    </section>

    <!-- Featured Products Section -->
    @if($featuredProducts->count() > 0)
    <section class="products-section">
        <div class="section-header">
            <h2 class="section-title">Популярные товары</h2>
            <a href="{{ route('products.index') }}" class="section-link">Все товары →</a>
        </div>
        <div class="products-grid">
            @foreach($featuredProducts as $product)
            <a href="{{ route('products.show', $product->slug) }}" class="product-card">
                <div class="product-image-wrapper">
                    @if($product->featuredImage)
                        <img src="{{ $product->featuredImage->image_url }}" alt="{{ $product->name }}" class="product-image" loading="lazy">
                    @else
                        <div class="product-image-placeholder">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <path d="M3 9h18M9 21V9"/>
                            </svg>
                        </div>
                    @endif
                    @if($product->isInStock())
                        <span class="product-badge product-badge-success">В наличии</span>
                    @else
                        <span class="product-badge product-badge-out">Нет в наличии</span>
                    @endif
                </div>
                <div class="product-info">
                    <h3 class="product-name">{{ $product->name }}</h3>
                    @if($product->short_description)
                    <p class="product-description">{{ Str::limit(strip_tags($product->short_description), 100) }}</p>
                    @endif
                    <div class="product-footer">
                        <div class="product-price">{{ number_format($product->price, 0, ',', ' ') }} ₽</div>
                        <button class="product-action-btn" onclick="event.preventDefault(); window.location.href='{{ route('products.show', $product->slug) }}'">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </section>
    @endif
    
    <!-- Latest Articles Section -->
    @if($latestPosts->count() > 0)
    <section class="articles-section">
        <div class="section-header">
            <h2 class="section-title">Последние статьи</h2>
            <a href="{{ route('articles.index') }}" class="section-link">Все статьи →</a>
        </div>
        <div class="articles-grid">
            @foreach($latestPosts as $post)
            <article class="article-card">
                @if($post->featuredImage)
                <a href="{{ route('articles.show', $post->slug) }}" class="article-image-wrapper">
                    <img src="{{ $post->featuredImage->image_url }}" alt="{{ $post->title }}" class="article-image" loading="lazy">
                </a>
                @endif
                <div class="article-content">
                    <div class="article-meta">
                        <time class="article-date">{{ $post->published_at?->format('d.m.Y') }}</time>
                        @if($post->categories->count() > 0)
                        <span class="article-category">
                            {{ $post->categories->first()->name }}
                        </span>
                        @endif
                    </div>
                    <h3 class="article-title">
                        <a href="{{ route('articles.show', $post->slug) }}">{{ $post->title }}</a>
                    </h3>
                    @if($post->excerpt)
                    <p class="article-excerpt">{{ Str::limit(strip_tags($post->excerpt), 120) }}</p>
                    @endif
                    <a href="{{ route('articles.show', $post->slug) }}" class="article-link">
                        Читать далее
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </article>
            @endforeach
        </div>
    </section>
    @endif
</div>

@push('styles')
<style>
.home-page {
    padding: 0;
}

/* Hero Section */
.hero-section {
    position: relative;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    padding: 1.5rem 1.5rem;
    margin-bottom: 2rem;
    overflow: hidden;
    min-height: 140px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.hero-content {
    position: relative;
    z-index: 2;
    text-align: center;
    color: white;
    max-width: 700px;
}

.hero-title {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 0.5rem;
    letter-spacing: -0.02em;
    line-height: 1.1;
}

.hero-subtitle {
    font-size: 1rem;
    opacity: 0.95;
    line-height: 1.5;
}

.hero-decoration {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    overflow: hidden;
}

.hero-circle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    animation: float 20s infinite ease-in-out;
}

.hero-circle-1 {
    width: 300px;
    height: 300px;
    top: -100px;
    right: -100px;
    animation-delay: 0s;
}

.hero-circle-2 {
    width: 200px;
    height: 200px;
    bottom: -50px;
    left: -50px;
    animation-delay: 5s;
}

.hero-circle-3 {
    width: 150px;
    height: 150px;
    top: 50%;
    left: 10%;
    animation-delay: 10s;
}

@keyframes float {
    0%, 100% { transform: translate(0, 0) rotate(0deg); }
    33% { transform: translate(30px, -30px) rotate(120deg); }
    66% { transform: translate(-20px, 20px) rotate(240deg); }
}

/* Section Styles */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1.25rem;
}

.section-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #1f2937;
    margin: 0;
}

.section-link {
    color: #2563eb;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-link:hover {
    color: #1d4ed8;
    gap: 0.75rem;
}

/* Products Section */
.products-section {
    margin-bottom: 3rem;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}

.product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.product-image-wrapper {
    position: relative;
    width: 100%;
    padding-top: 70%;
    background: #f9fafb;
    overflow: hidden;
}

.product-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-image-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #d1d5db;
}

.product-image-placeholder svg {
    width: 60px;
    height: 60px;
}

.product-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-badge-success {
    background: #10b981;
    color: white;
}

.product-badge-out {
    background: #ef4444;
    color: white;
}

.product-info {
    padding: 0.875rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.product-name {
    font-size: 0.95rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.375rem;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-description {
    font-size: 0.8rem;
    color: #6b7280;
    margin-bottom: 0.75rem;
    line-height: 1.4;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}

.product-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2563eb;
}

.product-action-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f3f4f6;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    color: #2563eb;
}

.product-action-btn:hover {
    background: #2563eb;
    color: white;
    transform: scale(1.1);
}

.product-action-btn svg {
    width: 16px;
    height: 16px;
}

/* Articles Section */
.articles-section {
    margin-bottom: 3rem;
}

.articles-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1rem;
}

.article-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.article-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.article-image-wrapper {
    position: relative;
    width: 100%;
    padding-top: 60%;
    background: #f9fafb;
    overflow: hidden;
}

.article-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.article-card:hover .article-image {
    transform: scale(1.05);
}

.article-content {
    padding: 1rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.article-meta {
    display: flex;
    gap: 0.75rem;
    align-items: center;
    margin-bottom: 0.5rem;
    font-size: 0.75rem;
    color: #6b7280;
}

.article-date {
    color: #6b7280;
}

.article-category {
    padding: 0.2rem 0.5rem;
    background: #eff6ff;
    color: #2563eb;
    border-radius: 8px;
    font-weight: 500;
    font-size: 0.7rem;
}

.article-title {
    font-size: 1rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    line-height: 1.3;
}

.article-title a {
    color: #1f2937;
    text-decoration: none;
    transition: color 0.3s;
}

.article-title a:hover {
    color: #2563eb;
}

.article-excerpt {
    color: #6b7280;
    line-height: 1.5;
    margin-bottom: 0.75rem;
    flex: 1;
    font-size: 0.85rem;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.article-link {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    color: #2563eb;
    text-decoration: none;
    font-weight: 600;
    font-size: 0.8rem;
    transition: gap 0.3s;
}

.article-link:hover {
    gap: 0.75rem;
}

.article-link svg {
    width: 16px;
    height: 16px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-section {
        padding: 1.25rem 1rem;
        min-height: 120px;
    }
    
    .hero-title {
        font-size: 1.75rem;
    }
    
    .hero-subtitle {
        font-size: 0.95rem;
    }
    
    .section-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .section-title {
        font-size: 1.75rem;
    }
    
    .products-grid {
        grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
        gap: 0.875rem;
    }
    
    .articles-grid {
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 0.875rem;
    }
}

@media (max-width: 480px) {
    .hero-section {
        padding: 1rem 0.875rem;
        min-height: 100px;
    }
    
    .hero-title {
        font-size: 1.5rem;
    }
    
    .hero-subtitle {
        font-size: 0.9rem;
    }
    
    .products-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .articles-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush
@endsection

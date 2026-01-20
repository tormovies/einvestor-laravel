@extends('layouts.app')

@php
    $seoTitle = $systemPage->seo_title ?? 'Статьи - EInvestor';
    $seoDescription = $systemPage->seo_description ?? 'Полезные статьи о торговле и индикаторах';
    $seoH1 = $systemPage->seo_h1 ?? 'Статьи';
@endphp

@section('seo')
    <x-seo-meta 
        :title="$seoTitle"
        :description="$seoDescription"
        :url="route('articles.index')"
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
    
    @if($posts->count() > 0)
    <div class="grid">
        @foreach($posts as $post)
        <div class="card">
            <h3 class="card-title">
                <a href="{{ route('articles.show', $post->slug) }}">{{ $post->title }}</a>
            </h3>
            @if($post->excerpt)
            <p>{{ Str::limit(strip_tags($post->excerpt), 200) }}</p>
            @endif
            <div class="meta">
                {{ $post->published_at?->format('d.m.Y') }}
                @if($post->categories->count() > 0)
                | 
                @foreach($post->categories as $category)
                <a href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a>@if(!$loop->last), @endif
                @endforeach
                @endif
            </div>
        </div>
        @endforeach
    </div>
    
    <div class="pagination-wrapper">
        {{ $posts->links('vendor.pagination.compact') }}
    </div>
    @else
    <p>Статей пока нет.</p>
    @endif
</div>

@push('styles')
<style>
.pagination-wrapper {
    margin-top: 3rem;
    display: flex;
    justify-content: center;
}

.pagination-nav {
    display: flex;
    justify-content: center;
}

.pagination {
    display: flex;
    gap: 0.5rem;
    list-style: none;
    padding: 0;
    margin: 0;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;
}

.pagination .page-item {
    display: inline-block;
}

.pagination .page-link {
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 38px;
    height: 38px;
    padding: 0.5rem 0.75rem;
    background: white;
    color: #4b5563;
    text-decoration: none;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
    line-height: 1;
}

.pagination .page-link:hover:not(.disabled):not(.disabled) {
    background: #f3f4f6;
    color: #2563eb;
    border-color: #2563eb;
}

.pagination .page-item.active .page-link {
    background: #2563eb;
    color: white;
    border-color: #2563eb;
    cursor: default;
}

.pagination .page-item.active .page-link:hover {
    background: #2563eb;
    color: white;
}

.pagination .page-item.disabled .page-link {
    background: #f9fafb;
    color: #d1d5db;
    border-color: #e5e7eb;
    cursor: not-allowed;
    pointer-events: none;
}

.pagination .page-link svg {
    width: 18px;
    height: 18px;
    flex-shrink: 0;
}

@media (max-width: 768px) {
    .pagination {
        gap: 0.375rem;
    }
    
    .pagination .page-link {
        min-width: 34px;
        height: 34px;
        padding: 0.375rem 0.5rem;
        font-size: 0.8125rem;
    }
    
    .pagination .page-link svg {
        width: 16px;
        height: 16px;
    }
}
</style>
@endpush
@endsection

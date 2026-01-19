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
    
    <div style="margin-top: 2rem;">
        {{ $posts->links() }}
    </div>
    @else
    <p>Статей пока нет.</p>
    @endif
</div>
@endsection

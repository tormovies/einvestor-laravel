@extends('layouts.app')

@php
    $seoTitle = $category->seo_title ?? $category->name . ' - Категория - EInvestor';
    $seoDescription = $category->seo_description ?? $category->description ?? Str::limit('Статьи в категории ' . $category->name, 160);
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
    
    @if($posts->count() > 0)
    <div class="grid" style="margin-top: 2rem;">
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
            </div>
        </div>
        @endforeach
    </div>
    
    <div style="margin-top: 2rem;">
        {{ $posts->links() }}
    </div>
    @else
    <p style="margin-top: 2rem;">В этой категории пока нет статей.</p>
    @endif
</div>
@endsection

@extends('layouts.app')

@php
    $seoTitle = $post->seo_title ?? $post->title . ' - EInvestor';
    $seoDescription = $post->seo_description ?? $post->excerpt ?? Str::limit(strip_tags($post->content), 160);
    $seoImage = $post->featuredImage ? $post->featuredImage->image_url : asset('images/og-default.jpg');
    $seoH1 = $post->seo_h1 ?? $post->title;
@endphp

@section('seo')
    <x-seo-meta 
        :title="$seoTitle"
        :description="$seoDescription"
        :image="$seoImage"
        :url="route('articles.show', $post->slug)"
        type="article" />
@endsection

@section('title', $seoTitle)

@section('content')
<div class="content">
    <article>
        <h1>{{ $seoH1 }}</h1>
        
        @if($post->seo_intro_text)
        <div style="margin-top: 1rem; margin-bottom: 2rem; padding: 1rem; background: #f9fafb; border-radius: 8px; border-left: 3px solid #2563eb; font-size: 1.1rem; line-height: 1.6; color: #4b5563;">
            {{ $post->seo_intro_text }}
        </div>
        @endif
        
        <div class="meta" style="margin-bottom: 2rem;">
            <span>Опубликовано: {{ $post->published_at?->format('d.m.Y H:i') }}</span>
            @if($post->categories->count() > 0)
            <span style="margin-left: 1rem;">
                Категории: 
                @foreach($post->categories as $category)
                <a href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a>@if(!$loop->last), @endif
                @endforeach
            </span>
            @endif
        </div>
        
        <div style="line-height: 1.8;">
            {!! $post->content !!}
        </div>
        
        @if($post->tags->count() > 0)
        <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
            <strong>Теги:</strong>
            @foreach($post->tags as $tag)
            <a href="{{ route('tag.show', $tag->slug) }}" style="margin-left: 0.5rem;">#{{ $tag->name }}</a>
            @endforeach
        </div>
        @endif
    </article>
    
    @if($relatedPosts->count() > 0)
    <section style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid #e5e7eb;">
        <h2>Похожие статьи</h2>
        <div class="grid">
            @foreach($relatedPosts as $relatedPost)
            <div class="card">
                <h3 class="card-title">
                    <a href="{{ route('articles.show', $relatedPost->slug) }}">{{ $relatedPost->title }}</a>
                </h3>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection

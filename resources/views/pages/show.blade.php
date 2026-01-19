@extends('layouts.app')

@php
    $seoTitle = $page->seo_title ?? $page->title . ' - EInvestor';
    $seoDescription = $page->seo_description ?? $page->excerpt ?? Str::limit(strip_tags($page->content), 160);
    $seoImage = $page->featuredImage ? $page->featuredImage->image_url : asset('images/og-default.jpg');
    $seoH1 = $page->seo_h1 ?? $page->title;
@endphp

@section('seo')
    <x-seo-meta 
        :title="$seoTitle"
        :description="$seoDescription"
        :image="$seoImage"
        :url="route('pages.show', $page->slug)"
        type="website" />
@endsection

@section('title', $seoTitle)

@section('content')
<div class="content">
    <article>
        <h1>{{ $seoH1 }}</h1>
        
        @if($page->seo_intro_text)
        <div style="margin-top: 1rem; margin-bottom: 2rem; padding: 1rem; background: #f9fafb; border-radius: 8px; border-left: 3px solid #2563eb; font-size: 1.1rem; line-height: 1.6; color: #4b5563;">
            {{ $page->seo_intro_text }}
        </div>
        @endif
        
        <div style="line-height: 1.8; margin-top: 2rem;">
            {!! $page->content !!}
        </div>
    </article>
</div>
@endsection

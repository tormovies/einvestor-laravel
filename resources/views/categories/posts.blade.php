@extends('layouts.app')

@section('title', $category->name . ' - Категория - EInvestor')

@section('content')
<div class="content">
    <h1>Категория: {{ $category->name }}</h1>
    
    @if($category->description)
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

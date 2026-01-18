@extends('layouts.app')

@section('title', 'Статьи - EInvestor')

@section('content')
<div class="content">
    <h1>Статьи</h1>
    
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

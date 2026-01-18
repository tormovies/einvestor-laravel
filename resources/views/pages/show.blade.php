@extends('layouts.app')

@section('title', $page->title . ' - EInvestor')

@section('content')
<div class="content">
    <article>
        <h1>{{ $page->title }}</h1>
        
        <div style="line-height: 1.8; margin-top: 2rem;">
            {!! $page->content !!}
        </div>
    </article>
</div>
@endsection

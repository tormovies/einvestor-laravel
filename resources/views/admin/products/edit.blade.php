@extends('layouts.app')

@section('title', 'Редактировать товар - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <h1>Редактировать товар: {{ $product->name }}</h1>
    
    @include('admin.products._form', ['product' => $product])
</div>
@endsection

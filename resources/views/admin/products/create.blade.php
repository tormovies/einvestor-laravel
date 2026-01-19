@extends('layouts.app')

@section('title', 'Создать товар - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <h1>Создать товар</h1>
    
    @include('admin.products._form')
</div>
@endsection

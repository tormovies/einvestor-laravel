@extends('layouts.app')

@section('title', 'Личный кабинет - EInvestor')

@section('content')
<div class="content">
    <h1>Личный кабинет</h1>
    
    @if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        {{ session('success') }}
    </div>
    @endif
    
    <div style="margin-top: 2rem; padding-bottom: 2rem; border-bottom: 2px solid #e5e7eb;">
        <h2>Быстрые ссылки</h2>
        <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
            <a href="{{ route('account.orders') }}" class="btn">Мои заказы</a>
            <a href="{{ route('account.downloads') }}" class="btn">Мои файлы</a>
            <a href="{{ route('account.profile') }}" class="btn" style="background: #6b7280;">Профиль</a>
            <a href="{{ route('products.index') }}" class="btn" style="background: #16a34a;">Перейти к товарам</a>
        </div>
    </div>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
        <div class="card">
            <h3 style="margin-bottom: 0.5rem;">Мои заказы</h3>
            <div style="font-size: 2rem; font-weight: bold; color: #2563eb; margin: 1rem 0;">
                {{ $ordersCount }}
            </div>
            <a href="{{ route('account.orders') }}" class="btn" style="display: block; text-align: center;">Просмотреть все</a>
        </div>
        
        <div class="card">
            <h3 style="margin-bottom: 0.5rem;">Доступные файлы</h3>
            <div style="font-size: 2rem; font-weight: bold; color: #2563eb; margin: 1rem 0;">
                {{ $downloadsCount }}
            </div>
            <a href="{{ route('account.downloads') }}" class="btn" style="display: block; text-align: center;">Скачать файлы</a>
        </div>
    </div>
</div>
@endsection

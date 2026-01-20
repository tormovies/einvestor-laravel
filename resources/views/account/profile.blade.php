@extends('layouts.app')

@section('title', 'Профиль - EInvestor')

@section('content')
<div class="content">
    <h1>Мой профиль</h1>
    
    @if(session('success'))
    <div style="background: #d1fae5; color: #065f46; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        {{ session('success') }}
    </div>
    @endif
    
    @if($errors->any())
    <div style="background: #fee2e2; color: #991b1b; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
        <ul style="margin: 0; padding-left: 1.5rem;">
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
    
    <div style="margin-top: 2rem; padding-bottom: 2rem; border-bottom: 2px solid #e5e7eb;">
        <h2>Быстрые ссылки</h2>
        <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
            <a href="{{ route('account.index') }}" class="btn">Главная</a>
            <a href="{{ route('account.orders') }}" class="btn">Мои заказы</a>
            <a href="{{ route('account.downloads') }}" class="btn">Мои файлы</a>
            <a href="{{ route('account.profile') }}" class="btn" style="background: #2563eb;">Профиль</a>
            <a href="{{ route('products.index') }}" class="btn" style="background: #16a34a;">Перейти к товарам</a>
        </div>
    </div>
    
    <div style="max-width: 600px; margin-top: 2rem;">
        <form action="{{ route('account.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 1.5rem;">
                <label for="name" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">
                    Имя
                </label>
                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; font-size: 1rem;">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">
                    Email
                </label>
                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; font-size: 1rem;">
            </div>
            
            <div style="background: #fef3c7; border: 1px solid #f59e0b; border-radius: 4px; padding: 1rem; margin-bottom: 1.5rem;">
                <p style="color: #92400e; margin: 0; font-size: 0.875rem;">
                    <strong>Важно:</strong> Для изменения пароля обратитесь в службу поддержки.
                </p>
            </div>
            
            <button type="submit" class="btn">Сохранить изменения</button>
        </form>
    </div>
</div>
@endsection

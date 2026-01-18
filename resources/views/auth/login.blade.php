@extends('layouts.app')

@section('title', 'Вход - EInvestor')

@section('content')
<div class="content">
    <h1>Вход в личный кабинет</h1>
    
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
    
    <div style="max-width: 400px; margin: 2rem auto;">
        <form action="{{ route('login') }}" method="POST">
            @csrf
            
            <div style="margin-bottom: 1.5rem;">
                <label for="email" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">
                    Email
                </label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required autofocus
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; font-size: 1rem;">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label for="password" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">
                    Пароль
                </label>
                <input type="password" id="password" name="password" required
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; font-size: 1rem;">
            </div>
            
            <div style="margin-bottom: 1.5rem;">
                <label style="display: flex; align-items: center;">
                    <input type="checkbox" name="remember" style="margin-right: 0.5rem;">
                    <span>Запомнить меня</span>
                </label>
            </div>
            
            <button type="submit" class="btn" style="width: 100%;">Войти</button>
        </form>
        
        <p style="margin-top: 2rem; color: #6b7280; font-size: 0.875rem; text-align: center;">
            Пароль был отправлен на ваш email после покупки.<br>
            Если вы не помните пароль, обратитесь в службу поддержки.
        </p>
    </div>
</div>
@endsection

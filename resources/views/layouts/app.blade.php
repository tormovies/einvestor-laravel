<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'EInvestor - Магазин скриптов и индикаторов')</title>
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        
        header {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            padding: 1rem 0;
            margin-bottom: 2rem;
        }
        
        nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .logo {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2563eb;
        }
        
        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #333;
            transition: color 0.3s;
        }
        
        .nav-links a:hover {
            color: #2563eb;
        }
        
        main {
            min-height: calc(100vh - 200px);
        }
        
        footer {
            background: #333;
            color: #fff;
            text-align: center;
            padding: 2rem 0;
            margin-top: 4rem;
        }
        
        .content {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        h2 {
            font-size: 1.5rem;
            margin-bottom: 0.75rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            background: #2563eb;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: #1d4ed8;
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }
        
        .card {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            padding: 1.5rem;
            transition: box-shadow 0.3s;
        }
        
        .card:hover {
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        
        .card-title {
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }
        
        .card-title a {
            color: #333;
            text-decoration: none;
        }
        
        .card-title a:hover {
            color: #2563eb;
        }
        
        .price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2563eb;
            margin-top: 1rem;
        }
        
        .meta {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
        
        .pagination {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            margin-top: 2rem;
        }
        
        .pagination a, .pagination span {
            padding: 0.5rem 1rem;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            text-decoration: none;
            color: #333;
        }
        
        .pagination a:hover {
            background: #f3f4f6;
        }
        
        .pagination .active span {
            background: #2563eb;
            color: #fff;
            border-color: #2563eb;
        }
    </style>
    
    @stack('styles')
</head>
<body>
    @if(!request()->is('admin*'))
        @include('components.developer-contacts')
    @endif
    
    <header>
        <div class="container">
            <nav>
                <a href="{{ route('home') }}" class="logo">EInvestor</a>
                <ul class="nav-links">
                    <li><a href="{{ route('home') }}">Главная</a></li>
                    <li><a href="{{ route('articles.index') }}">Статьи</a></li>
                    <li><a href="{{ route('products.index') }}">Товары</a></li>
                    <li><a href="{{ route('cart.index') }}">Корзина 
                        @php
                            $cartCount = \App\Http\Controllers\CartController::getCartCount();
                        @endphp
                        @if($cartCount > 0)
                            <span style="background: #2563eb; color: white; padding: 2px 6px; border-radius: 10px; font-size: 0.75rem;">{{ $cartCount }}</span>
                        @endif
                    </a></li>
                    @auth
                    <li><a href="{{ route('account.index') }}">Личный кабинет</a></li>
                    <li>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" style="background: none; border: none; color: #333; cursor: pointer; padding: 0; font-size: inherit; text-decoration: underline;">Выход</button>
                        </form>
                    </li>
                    @else
                    <li><a href="{{ route('login') }}">Вход</a></li>
                    @endauth
                </ul>
            </nav>
        </div>
    </header>
    
    <main>
        <div class="container">
            @yield('content')
        </div>
    </main>
    
    <footer>
        <div class="container">
            <p>&copy; {{ date('Y') }} EInvestor. Все права защищены.</p>
        </div>
    </footer>
    
    @stack('scripts')
</body>
</html>

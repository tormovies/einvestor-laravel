<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @hasSection('seo')
        @yield('seo')
    @else
        <title>@yield('title', 'EInvestor - Магазин скриптов и индикаторов')</title>
        <meta name="description" content="Магазин скриптов и индикаторов для торговли">
    @endif
    
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
            display: flex;
            align-items: center;
            gap: 0.25rem;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 800;
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: transform 0.3s ease;
        }
        
        .logo:hover {
            transform: translateY(-2px);
        }
        
        .logo-icon {
            width: 50px;
            height: 50px;
            flex-shrink: 0;
        }
        
        .logo-text {
            background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -0.02em;
        }
        
        @media (max-width: 768px) {
            .logo {
                font-size: 1.25rem;
            }
            
            .logo-icon {
                width: 40px;
                height: 40px;
            }
        }
        
        @media (max-width: 480px) {
            .logo-text {
                font-size: 1.1rem;
            }
            
            .logo-icon {
                width: 36px;
                height: 36px;
            }
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
    
    <!-- Yandex.Metrika counter -->
    <script type="text/javascript">
        (function(m,e,t,r,i,k,a){
            m[i]=m[i]||function(){(m[i].a=m[i].a||[]).push(arguments)};
            m[i].l=1*new Date();
            for (var j = 0; j < document.scripts.length; j++) {if (document.scripts[j].src === r) { return; }}
            k=e.createElement(t),a=e.getElementsByTagName(t)[0],k.async=1,k.src=r,a.parentNode.insertBefore(k,a)
        })(window, document,'script','https://mc.yandex.ru/metrika/tag.js?id=45968250', 'ym');
        
        ym(45968250, 'init', {ssr:true, clickmap:true, ecommerce:"dataLayer", referrer: document.referrer, url: location.href, accurateTrackBounce:true, trackLinks:true});
    </script>
    <noscript><div><img src="https://mc.yandex.ru/watch/45968250" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
    <!-- /Yandex.Metrika counter -->
</head>
<body>
    @include('components.developer-contacts')
    
    <header>
        <div class="container">
            <nav>
                <a href="{{ route('home') }}" class="logo">
                    <svg class="logo-icon" viewBox="0 0 80 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:#2563eb;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#7c3aed;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        <!-- График роста (увеличен) -->
                        <path d="M5 32 L12 24 L20 26 L28 14 L36 18 L44 10" stroke="url(#logoGradient)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        <circle cx="5" cy="32" r="3.5" fill="url(#logoGradient)"/>
                        <circle cx="44" cy="10" r="3.5" fill="url(#logoGradient)"/>
                        <!-- Буква E (увеличенная и жирная) -->
                        <path d="M52 8 L52 32 M52 8 L70 8 M52 20 L66 20 M52 32 L70 32" stroke="url(#logoGradient)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span class="logo-text">Investor</span>
                </a>
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

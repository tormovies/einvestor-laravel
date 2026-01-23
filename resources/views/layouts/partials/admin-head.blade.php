<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Админ-панель - EInvestor')</title>
    
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
    <header style="background: #fff; box-shadow: 0 2px 4px rgba(0,0,0,0.1); padding: 1rem 0; margin-bottom: 2rem;">
        <div class="container">
            <nav style="display: flex; justify-content: space-between; align-items: center;">
                <a href="{{ route('home') }}" class="logo" style="display: flex; align-items: center; gap: 0.25rem; text-decoration: none; font-size: 1.5rem; font-weight: 800; background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; transition: transform 0.3s ease;">
                    <svg class="logo-icon" viewBox="0 0 80 40" fill="none" xmlns="http://www.w3.org/2000/svg" style="width: 50px; height: 50px; flex-shrink: 0;">
                        <defs>
                            <linearGradient id="logoGradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:#2563eb;stop-opacity:1" />
                                <stop offset="100%" style="stop-color:#7c3aed;stop-opacity:1" />
                            </linearGradient>
                        </defs>
                        <path d="M5 32 L12 24 L20 26 L28 14 L36 18 L44 10" stroke="url(#logoGradient)" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" fill="none"/>
                        <circle cx="5" cy="32" r="3.5" fill="url(#logoGradient)"/>
                        <circle cx="44" cy="10" r="3.5" fill="url(#logoGradient)"/>
                        <path d="M52 8 L52 32 M52 8 L70 8 M52 20 L66 20 M52 32 L70 32" stroke="url(#logoGradient)" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <span style="background: linear-gradient(135deg, #2563eb 0%, #7c3aed 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; letter-spacing: -0.02em;">Investor</span>
                </a>
                <div style="display: flex; gap: 1rem; align-items: center;">
                    <a href="{{ route('home') }}" style="color: #333; text-decoration: none; font-weight: 500; transition: color 0.3s;" onmouseover="this.style.color='#2563eb'" onmouseout="this.style.color='#333'">На сайт</a>
                    @auth
                    <span style="color: #6b7280;">{{ Auth::user()->email }}</span>
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: #dc2626; color: white; border: none; padding: 0.5rem 1rem; border-radius: 4px; cursor: pointer; font-weight: 500; transition: background 0.3s;" onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">Выход</button>
                    </form>
                    @endauth
                </div>
            </nav>
        </div>
    </header>

<nav style="background: #f3f4f6; padding: 0.75rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #e5e7eb;">
    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap; align-items: stretch;">
        <a href="{{ route('admin.dashboard') }}" 
           style="display: flex; align-items: center; justify-content: center; min-height: 40px; padding: 0.5rem 1rem; background: {{ request()->routeIs('admin.dashboard') ? '#2563eb' : '#6b7280' }}; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.875rem; transition: background 0.2s; box-sizing: border-box; white-space: nowrap;"
           onmouseover="this.style.background='#2563eb'" 
           onmouseout="this.style.background='{{ request()->routeIs('admin.dashboard') ? '#2563eb' : '#6b7280' }}'">
            🏠 В админку
        </a>
        
        <a href="{{ route('admin.orders.index') }}" 
           style="display: flex; align-items: center; justify-content: center; min-height: 40px; padding: 0.5rem 1rem; background: {{ request()->routeIs('admin.orders.*') ? '#dc2626' : '#6b7280' }}; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.875rem; transition: background 0.2s; box-sizing: border-box; white-space: nowrap;"
           onmouseover="this.style.background='#dc2626'" 
           onmouseout="this.style.background='{{ request()->routeIs('admin.orders.*') ? '#dc2626' : '#6b7280' }}'">
            📦 Заказы
        </a>
        
        <a href="{{ route('admin.products.index') }}" 
           style="display: flex; align-items: center; justify-content: center; min-height: 40px; padding: 0.5rem 1rem; background: {{ request()->routeIs('admin.products.*') ? '#16a34a' : '#6b7280' }}; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.875rem; transition: background 0.2s; box-sizing: border-box; white-space: nowrap;"
           onmouseover="this.style.background='#16a34a'" 
           onmouseout="this.style.background='{{ request()->routeIs('admin.products.*') ? '#16a34a' : '#6b7280' }}'">
            🛍️ Товары
        </a>
        
        <a href="{{ route('admin.posts.index') }}" 
           style="display: flex; align-items: center; justify-content: center; min-height: 40px; padding: 0.5rem 1rem; background: {{ request()->routeIs('admin.posts.*') ? '#7c3aed' : '#6b7280' }}; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.875rem; transition: background 0.2s; box-sizing: border-box; white-space: nowrap;"
           onmouseover="this.style.background='#7c3aed'" 
           onmouseout="this.style.background='{{ request()->routeIs('admin.posts.*') ? '#7c3aed' : '#6b7280' }}'">
            📝 Посты
        </a>
        
        <a href="{{ route('admin.pages.index') }}" 
           style="display: flex; align-items: center; justify-content: center; min-height: 40px; padding: 0.5rem 1rem; background: {{ request()->routeIs('admin.pages.*') ? '#0891b2' : '#6b7280' }}; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.875rem; transition: background 0.2s; box-sizing: border-box; white-space: nowrap;"
           onmouseover="this.style.background='#0891b2'" 
           onmouseout="this.style.background='{{ request()->routeIs('admin.pages.*') ? '#0891b2' : '#6b7280' }}'">
            📄 Страницы
        </a>
        
        <a href="{{ route('admin.categories.index') }}" 
           style="display: flex; align-items: center; justify-content: center; min-height: 40px; padding: 0.5rem 1rem; background: {{ request()->routeIs('admin.categories.*') ? '#059669' : '#6b7280' }}; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.875rem; transition: background 0.2s; box-sizing: border-box; white-space: nowrap;"
           onmouseover="this.style.background='#059669'" 
           onmouseout="this.style.background='{{ request()->routeIs('admin.categories.*') ? '#059669' : '#6b7280' }}'">
            🗂️ Категории
        </a>
        
        <a href="{{ route('admin.tags.index') }}" 
           style="display: flex; align-items: center; justify-content: center; min-height: 40px; padding: 0.5rem 1rem; background: {{ request()->routeIs('admin.tags.*') ? '#0d9488' : '#6b7280' }}; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.875rem; transition: background 0.2s; box-sizing: border-box; white-space: nowrap;"
           onmouseover="this.style.background='#0d9488'" 
           onmouseout="this.style.background='{{ request()->routeIs('admin.tags.*') ? '#0d9488' : '#6b7280' }}'">
            🏷️ Теги
        </a>
        
        <a href="{{ route('admin.redirects.index') }}" 
           style="display: flex; align-items: center; justify-content: center; min-height: 40px; padding: 0.5rem 1rem; background: {{ request()->routeIs('admin.redirects.*') ? '#9333ea' : '#6b7280' }}; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.875rem; transition: background 0.2s; box-sizing: border-box; white-space: nowrap;"
           onmouseover="this.style.background='#9333ea'" 
           onmouseout="this.style.background='{{ request()->routeIs('admin.redirects.*') ? '#9333ea' : '#6b7280' }}'">
            🔀 Редиректы
        </a>
        
        <a href="{{ route('admin.users.index') }}" 
           style="display: flex; align-items: center; justify-content: center; min-height: 40px; padding: 0.5rem 1rem; background: {{ request()->routeIs('admin.users.*') ? '#ea580c' : '#6b7280' }}; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.875rem; transition: background 0.2s; box-sizing: border-box; white-space: nowrap;"
           onmouseover="this.style.background='#ea580c'" 
           onmouseout="this.style.background='{{ request()->routeIs('admin.users.*') ? '#ea580c' : '#6b7280' }}'">
            👥 Пользователи
        </a>
        
        <a href="{{ route('admin.settings.index') }}" 
           style="display: flex; align-items: center; justify-content: center; min-height: 40px; padding: 0.5rem 1rem; background: {{ request()->routeIs('admin.settings.*') ? '#f59e0b' : '#6b7280' }}; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.875rem; transition: background 0.2s; box-sizing: border-box; white-space: nowrap;"
           onmouseover="this.style.background='#f59e0b'" 
           onmouseout="this.style.background='{{ request()->routeIs('admin.settings.*') ? '#f59e0b' : '#6b7280' }}'">
            ⚙️ Настройки
        </a>
    </div>
</nav>

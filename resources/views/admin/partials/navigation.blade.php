<nav style="background: #f3f4f6; padding: 1rem; border-radius: 8px; margin-bottom: 2rem; border: 1px solid #e5e7eb;">
    <div style="display: grid; grid-template-columns: repeat(5, 1fr); gap: 1rem; align-items: stretch;">
        <a href="{{ route('admin.dashboard') }}" 
           style="display: flex; align-items: center; justify-content: center; min-height: 48px; padding: 0.75rem 0.5rem; background: {{ request()->routeIs('admin.dashboard') ? '#2563eb' : '#6b7280' }}; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.875rem; transition: background 0.2s; box-sizing: border-box;"
           onmouseover="this.style.background='#2563eb'" 
           onmouseout="this.style.background='{{ request()->routeIs('admin.dashboard') ? '#2563eb' : '#6b7280' }}'">
            🏠 В админку
        </a>
        
        <a href="{{ route('admin.orders.index') }}" 
           style="display: flex; align-items: center; justify-content: center; min-height: 48px; padding: 0.75rem 0.5rem; background: {{ request()->routeIs('admin.orders.*') ? '#dc2626' : '#6b7280' }}; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.875rem; transition: background 0.2s; box-sizing: border-box;"
           onmouseover="this.style.background='#dc2626'" 
           onmouseout="this.style.background='{{ request()->routeIs('admin.orders.*') ? '#dc2626' : '#6b7280' }}'">
            📦 Управление заказами
        </a>
        
        <a href="{{ route('admin.products.index') }}" 
           style="display: flex; align-items: center; justify-content: center; min-height: 48px; padding: 0.75rem 0.5rem; background: {{ request()->routeIs('admin.products.*') ? '#16a34a' : '#6b7280' }}; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.875rem; transition: background 0.2s; box-sizing: border-box;"
           onmouseover="this.style.background='#16a34a'" 
           onmouseout="this.style.background='{{ request()->routeIs('admin.products.*') ? '#16a34a' : '#6b7280' }}'">
            🛍️ Управление товарами
        </a>
        
        <a href="{{ route('admin.posts.index') }}" 
           style="display: flex; align-items: center; justify-content: center; min-height: 48px; padding: 0.75rem 0.5rem; background: {{ request()->routeIs('admin.posts.*') ? '#7c3aed' : '#6b7280' }}; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.875rem; transition: background 0.2s; box-sizing: border-box;"
           onmouseover="this.style.background='#7c3aed'" 
           onmouseout="this.style.background='{{ request()->routeIs('admin.posts.*') ? '#7c3aed' : '#6b7280' }}'">
            📝 Управление постами
        </a>
        
        <a href="{{ route('admin.users.index') }}" 
           style="display: flex; align-items: center; justify-content: center; min-height: 48px; padding: 0.75rem 0.5rem; background: {{ request()->routeIs('admin.users.*') ? '#ea580c' : '#6b7280' }}; color: white; text-decoration: none; border-radius: 4px; font-weight: 500; font-size: 0.875rem; transition: background 0.2s; box-sizing: border-box;"
           onmouseover="this.style.background='#ea580c'" 
           onmouseout="this.style.background='{{ request()->routeIs('admin.users.*') ? '#ea580c' : '#6b7280' }}'">
            👥 Пользователи
        </a>
    </div>
</nav>

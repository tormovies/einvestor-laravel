<!DOCTYPE html>
<html lang="ru">
@if(request()->is('admin*'))
    @include('layouts.partials.admin-head')
@else
    @include('layouts.partials.public-header')
@endif
    
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

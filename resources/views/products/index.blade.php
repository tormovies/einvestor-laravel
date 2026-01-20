@extends('layouts.app')

@php
    $seoTitle = $systemPage->seo_title ?? 'Товары - EInvestor';
    $seoDescription = $systemPage->seo_description ?? 'Каталог товаров - скрипты и индикаторы для торговли';
    $seoH1 = $systemPage->seo_h1 ?? 'Каталог товаров';
@endphp

@section('seo')
    <x-seo-meta 
        :title="$seoTitle"
        :description="$seoDescription"
        :url="route('products.index')"
        type="website" />
@endsection

@section('title', $seoTitle)

@section('content')
<div class="products-page">
    <h1 class="page-title">{{ $seoH1 }}</h1>
    
    @if($systemPage && $systemPage->seo_intro_text)
    <div class="intro-text">
        {{ $systemPage->seo_intro_text }}
    </div>
    @endif
    
    <!-- View Toggle -->
    @if($products->count() > 0)
    <div class="view-toggle-wrapper">
        <div class="view-toggle">
            <button class="view-toggle-btn active" data-view="grid" title="Плиткой" aria-label="Отображение плиткой">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="7" height="7"/>
                    <rect x="14" y="3" width="7" height="7"/>
                    <rect x="3" y="14" width="7" height="7"/>
                    <rect x="14" y="14" width="7" height="7"/>
                </svg>
            </button>
            <button class="view-toggle-btn" data-view="list" title="Строками" aria-label="Отображение строками">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
        </div>
    </div>
    @endif
    
    @if($products->count() > 0)
    <div class="products-grid" id="products-container">
        @foreach($products as $product)
        <a href="{{ route('products.show', $product->slug) }}" class="product-card">
            <div class="product-image-wrapper">
                @if($product->featuredImage)
                    <img src="{{ $product->featuredImage->image_url }}" alt="{{ $product->name }}" class="product-image" loading="lazy">
                @else
                    <div class="product-image-placeholder">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                            <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                            <path d="M3 9h18M9 21V9"/>
                        </svg>
                    </div>
                @endif
                @if($product->isInStock())
                    <span class="product-badge product-badge-success">В наличии</span>
                @else
                    <span class="product-badge product-badge-out">Нет в наличии</span>
                @endif
            </div>
            <div class="product-info">
                <h3 class="product-name">{{ $product->name }}</h3>
                @if($product->short_description)
                <p class="product-description">{{ Str::limit(strip_tags($product->short_description), 100) }}</p>
                @endif
                <div class="product-footer">
                    <div class="product-price">{{ number_format($product->price, 0, ',', ' ') }} ₽</div>
                    <button class="product-action-btn" onclick="event.preventDefault(); window.location.href='{{ route('products.show', $product->slug) }}'">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    
    <div class="pagination-wrapper">
        {{ $products->links() }}
    </div>
    @else
    <div class="empty-state">
        <p>Товаров пока нет.</p>
    </div>
    @endif
</div>

@push('styles')
<style>
.products-page {
    padding: 0;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 1rem;
}

.intro-text {
    margin-top: 1rem;
    margin-bottom: 2rem;
    padding: 1rem;
    background: #f9fafb;
    border-radius: 8px;
    border-left: 3px solid #2563eb;
    font-size: 1.1rem;
    line-height: 1.6;
    color: #4b5563;
}

.products-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 0.875rem;
    margin-top: 2rem;
}

.product-card {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    text-decoration: none;
    color: inherit;
    display: flex;
    flex-direction: column;
}

.product-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.product-image-wrapper {
    position: relative;
    width: 100%;
    padding-top: 75%; /* Соотношение 4:3 (1.33:1) - оптимально для товаров */
    background: #f9fafb;
    overflow: hidden;
}

.product-image {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.product-card:hover .product-image {
    transform: scale(1.05);
}

.product-image-placeholder {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #d1d5db;
}

.product-image-placeholder svg {
    width: 60px;
    height: 60px;
}

.product-badge {
    position: absolute;
    top: 8px;
    right: 8px;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.65rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.product-badge-success {
    background: #10b981;
    color: white;
}

.product-badge-out {
    background: #ef4444;
    color: white;
}

.product-info {
    padding: 0.75rem;
    display: flex;
    flex-direction: column;
    flex: 1;
}

.product-name {
    font-size: 0.875rem;
    font-weight: 600;
    color: #1f2937;
    margin-bottom: 0.375rem;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-description {
    font-size: 0.75rem;
    color: #6b7280;
    margin-bottom: 0.625rem;
    line-height: 1.4;
    flex: 1;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: auto;
}

.product-price {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2563eb;
}

.product-action-btn {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    background: #f3f4f6;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.3s;
    color: #2563eb;
}

.product-action-btn:hover {
    background: #2563eb;
    color: white;
    transform: scale(1.1);
}

.product-action-btn svg {
    width: 16px;
    height: 16px;
}

.pagination-wrapper {
    margin-top: 3rem;
    display: flex;
    justify-content: center;
}

.empty-state {
    text-align: center;
    padding: 3rem 0;
    color: #6b7280;
}

/* View Toggle */
.view-toggle-wrapper {
    display: flex;
    justify-content: flex-end;
    margin-bottom: 1.5rem;
    margin-top: -0.5rem;
}

.view-toggle {
    display: flex;
    gap: 0.5rem;
    background: white;
    padding: 0.25rem;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}

.view-toggle-btn {
    width: 40px;
    height: 40px;
    border: none;
    background: transparent;
    border-radius: 6px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6b7280;
    transition: all 0.3s;
}

.view-toggle-btn:hover {
    background: #f3f4f6;
    color: #2563eb;
}

.view-toggle-btn.active {
    background: #2563eb;
    color: white;
}

.view-toggle-btn svg {
    width: 20px;
    height: 20px;
}

/* Responsive Design */
@media (max-width: 1200px) {
    .products-grid:not(.list-view) {
        grid-template-columns: repeat(4, 1fr);
        gap: 0.875rem;
    }
}

@media (max-width: 768px) {
    .page-title {
        font-size: 1.75rem;
    }
    
    .products-grid:not(.list-view) {
        grid-template-columns: repeat(3, 1fr);
        gap: 0.875rem;
    }
}

@media (max-width: 480px) {
    .page-title {
        font-size: 1.5rem;
    }
    
    .products-grid:not(.list-view) {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .intro-text {
        font-size: 1rem;
        padding: 0.875rem;
    }
    
    .products-grid.list-view .product-image-wrapper,
    .articles-grid.list-view .article-image-wrapper {
        width: 100px;
        min-width: 100px;
        padding-top: 70px;
    }
    
    .products-grid.list-view .product-info,
    .articles-grid.list-view .article-content {
        padding: 0.5rem;
    }
}

/* List View for Products */
.products-grid.list-view {
    grid-template-columns: 1fr;
    gap: 0.75rem;
}

.products-grid.list-view .product-card {
    flex-direction: row;
    max-width: 100%;
}

.products-grid.list-view .product-image-wrapper {
    width: 140px;
    min-width: 140px;
    padding-top: 100px;
    flex-shrink: 0;
}

.products-grid.list-view .product-info {
    flex: 1;
    padding: 0.625rem 0.875rem;
    display: flex;
    flex-direction: column;
    justify-content: space-between;
}

.products-grid.list-view .product-name {
    font-size: 0.9rem;
    margin-bottom: 0.25rem;
    -webkit-line-clamp: 1;
}

.products-grid.list-view .product-description {
    font-size: 0.75rem;
    margin-bottom: 0.5rem;
    -webkit-line-clamp: 1;
}

.products-grid.list-view .product-footer {
    margin-top: 0.25rem;
}

.products-grid.list-view .product-price {
    font-size: 1.1rem;
}

.products-grid.list-view .product-action-btn {
    width: 28px;
    height: 28px;
}

.products-grid.list-view .product-action-btn svg {
    width: 14px;
    height: 14px;
}
</style>
@endpush

@push('scripts')
<script>
(function() {
    const STORAGE_KEY = 'products_view_mode';
    const productsContainer = document.getElementById('products-container');
    const toggleButtons = document.querySelectorAll('.view-toggle-btn');
    
    // Загружаем сохраненный выбор или используем 'grid' по умолчанию
    const savedView = localStorage.getItem(STORAGE_KEY) || 'grid';
    
    // Применяем сохраненный вид
    function applyView(view) {
        if (view === 'list') {
            if (productsContainer) productsContainer.classList.add('list-view');
        } else {
            if (productsContainer) productsContainer.classList.remove('list-view');
        }
        
        // Обновляем активную кнопку
        toggleButtons.forEach(btn => {
            if (btn.dataset.view === view) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    }
    
    // Применяем сохраненный вид при загрузке
    applyView(savedView);
    
    // Обработчики кликов на кнопки
    toggleButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            localStorage.setItem(STORAGE_KEY, view);
            applyView(view);
        });
    });
})();
</script>
@endpush
@endsection

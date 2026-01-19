@extends('layouts.app')

@section('title', $product->name . ' - EInvestor')

@section('content')
<div class="content">
    <article>
        <h1>{{ $product->name }}</h1>
        
        <div style="display: flex; gap: 2rem; margin-top: 2rem;">
            <div style="flex: 1;">
                @if($product->description)
                <div class="product-description" style="line-height: 1.8; margin-bottom: 2rem;">
                    {!! $product->description !!}
                </div>
                @endif
                
                <div style="background: #f9fafb; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem;">
                    <div class="price" style="font-size: 2rem; margin-bottom: 1rem;">
                        {{ number_format($product->price, 0, ',', ' ') }} ₽
                    </div>
                    
                    @if($product->isInStock())
                    <form action="{{ route('cart.add', $product->id) }}" method="POST" style="margin-top: 1rem;">
                        @csrf
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn">Добавить в корзину</button>
                    </form>
                    @else
                    <p style="color: red; margin-top: 1rem;">Товар временно недоступен</p>
                    @endif
                </div>
                
                @if($product->categories->count() > 0)
                <div style="margin-top: 2rem;">
                    <strong>Категории:</strong>
                    @foreach($product->categories as $category)
                    <a href="{{ route('category.show', $category->slug) }}" style="margin-left: 0.5rem;">{{ $category->name }}</a>@if(!$loop->last), @endif
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </article>
    
    @if($relatedProducts->count() > 0)
    <section style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid #e5e7eb;">
        <h2>Похожие товары</h2>
        <div class="grid">
            @foreach($relatedProducts as $relatedProduct)
            <div class="card">
                <h3 class="card-title">
                    <a href="{{ route('products.show', $relatedProduct->slug) }}">{{ $relatedProduct->name }}</a>
                </h3>
                <div class="price">{{ number_format($relatedProduct->price, 0, ',', ' ') }} ₽</div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>

@push('styles')
<style>
    /* Стили для изображений в описании товара */
    .product-description img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        cursor: pointer;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        display: block;
    }
    
    /* Одиночные изображения - полная ширина до 1200px */
    .product-description > p:has(> img:only-child),
    .product-description > div:has(> img:only-child),
    .product-description img:only-child {
        max-width: 1200px;
        margin: 1.5rem auto;
    }
    
    /* Галерея для изображений рядом друг с другом */
    .product-description .image-gallery {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin: 1.5rem 0;
        justify-content: center;
        align-items: flex-start;
    }
    
    .product-description .image-gallery > div {
        flex: 1 1 auto;
        min-width: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .product-description .image-gallery img {
        width: 100%;
        height: auto;
        max-width: 100%;
        margin: 0;
    }
    
    /* Если 1 изображение в галерее - полная ширина до 1200px */
    .product-description .image-gallery:has(> div:only-child) img {
        max-width: 1200px;
    }
    
    /* Если 2 изображения - по 50% */
    .product-description .image-gallery:has(> div:nth-child(2):last-child) > div {
        flex: 1 1 calc(50% - 0.5rem);
        max-width: 600px;
    }
    
    /* Если 3 изображения - по 33% */
    .product-description .image-gallery:has(> div:nth-child(3):last-child) > div {
        flex: 1 1 calc(33.333% - 0.67rem);
        max-width: 400px;
    }
    
    /* Если 4 изображения - по 25% */
    .product-description .image-gallery:has(> div:nth-child(4):last-child) > div {
        flex: 1 1 calc(25% - 0.75rem);
        max-width: 300px;
    }
    
    /* Если 5+ изображений - по 20% */
    .product-description .image-gallery:has(> div:nth-child(5)) > div {
        flex: 1 1 calc(20% - 0.8rem);
        max-width: 250px;
    }
    
    .product-description img:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }
    
    @media (max-width: 768px) {
        .product-description .image-gallery {
            flex-direction: column;
        }
        
        .product-description .image-gallery img {
            flex: 1 1 100%;
            max-width: 100%;
        }
    }
    
    /* Lightbox overlay */
    .lightbox-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.95);
        z-index: 10000;
        cursor: pointer;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    
    .lightbox-overlay.active {
        display: flex;
    }
    
    .lightbox-content {
        position: relative;
        max-width: 90%;
        max-height: 90%;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .lightbox-content img {
        max-width: 100%;
        max-height: 90vh;
        width: auto;
        height: auto;
        border-radius: 8px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.5);
        cursor: default;
    }
    
    .lightbox-nav {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(255, 255, 255, 0.9);
        border: none;
        width: 50px;
        height: 50px;
        border-radius: 50%;
        font-size: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease;
        z-index: 10001;
    }
    
    .lightbox-nav:hover {
        background: rgba(255, 255, 255, 1);
    }
    
    .lightbox-nav.prev {
        left: 20px;
    }
    
    .lightbox-nav.next {
        right: 20px;
    }
    
    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.9);
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        font-size: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease;
        z-index: 10001;
    }
    
    .lightbox-close:hover {
        background: rgba(255, 255, 255, 1);
    }
    
    .lightbox-counter {
        position: absolute;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        color: white;
        font-size: 16px;
        background: rgba(0, 0, 0, 0.5);
        padding: 8px 16px;
        border-radius: 20px;
    }
    
    @media (max-width: 768px) {
        .lightbox-nav {
            width: 40px;
            height: 40px;
            font-size: 18px;
        }
        
        .lightbox-nav.prev {
            left: 10px;
        }
        
        .lightbox-nav.next {
            right: 10px;
        }
        
        .product-description img {
            max-width: 100%;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const productDescription = document.querySelector('.product-description');
        if (!productDescription) return;
        
        // Группируем изображения, которые находятся рядом друг с другом
        function groupAdjacentImages() {
            const allImages = Array.from(productDescription.querySelectorAll('img'));
            if (allImages.length === 0) return;
            
            let currentGroup = [];
            let lastImg = null;
            
            allImages.forEach((img) => {
                const parent = img.parentElement;
                const prevSibling = img.previousElementSibling;
                const nextSibling = img.nextElementSibling;
                
                // Проверяем, находится ли изображение рядом с предыдущим
                let isAdjacent = false;
                
                if (lastImg) {
                    const lastParent = lastImg.parentElement;
                    
                    // В одном родителе
                    if (parent === lastParent) {
                        isAdjacent = true;
                    }
                    // Следующее изображение в том же параграфе
                    else if (nextSibling && nextSibling.tagName === 'IMG') {
                        isAdjacent = true;
                    }
                    // Предыдущее изображение в том же параграфе
                    else if (prevSibling && prevSibling.tagName === 'IMG') {
                        isAdjacent = true;
                    }
                    // Соседние параграфы с изображениями (без текста между ними)
                    else if (parent.tagName === 'P' && lastParent.tagName === 'P') {
                        const parentText = parent.textContent.trim().replace(/\s+/g, ' ');
                        const lastParentText = lastParent.textContent.trim().replace(/\s+/g, ' ');
                        
                        // Если в параграфах только изображения (нет текста кроме пробелов)
                        if (parentText === img.alt && lastParentText === lastImg.alt) {
                            // Проверяем, что между ними нет других элементов
                            let between = false;
                            let node = lastParent.nextSibling;
                            while (node && node !== parent) {
                                if (node.nodeType === 1) { // Element node
                                    const text = node.textContent.trim();
                                    if (text && !node.querySelector('img')) {
                                        between = true;
                                        break;
                                    }
                                } else if (node.nodeType === 3) { // Text node
                                    if (node.textContent.trim()) {
                                        between = true;
                                        break;
                                    }
                                }
                                node = node.nextSibling;
                            }
                            if (!between) {
                                isAdjacent = true;
                            }
                        }
                    }
                }
                
                if (isAdjacent && currentGroup.length > 0) {
                    currentGroup.push(img);
                } else {
                    // Создаем галерею для предыдущей группы, если в ней больше 1 изображения
                    if (currentGroup.length > 1) {
                        createGalleryGroup(currentGroup);
                    }
                    currentGroup = [img];
                }
                
                lastImg = img;
            });
            
            // Обрабатываем последнюю группу
            if (currentGroup.length > 1) {
                createGalleryGroup(currentGroup);
            }
        }
        
        // Создает контейнер галереи для группы изображений
        function createGalleryGroup(images) {
            if (images.length === 0) return;
            
            const gallery = document.createElement('div');
            gallery.className = 'image-gallery';
            
            // Сохраняем оригинальные src для lightbox
            images.forEach((img) => {
                const wrapper = document.createElement('div');
                wrapper.style.position = 'relative';
                
                // Клонируем изображение с сохранением всех атрибутов
                const clonedImg = img.cloneNode(true);
                wrapper.appendChild(clonedImg);
                gallery.appendChild(wrapper);
            });
            
            // Вставляем галерею точно на место первого изображения
            const firstImg = images[0];
            const firstParent = firstImg.parentElement;
            
            if (!firstParent) return;
            
            // Если первое изображение - единственный элемент в родителе
            if (firstParent.children.length === 1 && firstParent.textContent.trim() === firstImg.alt) {
                // Заменяем родителя на галерею
                if (firstParent.parentElement) {
                    firstParent.parentElement.insertBefore(gallery, firstParent);
                    firstParent.remove();
                }
            } else {
                // Вставляем галерею прямо перед первым изображением
                firstParent.insertBefore(gallery, firstImg);
            }
            
            // Удаляем все изображения группы
            images.forEach(img => {
                // Проверяем, не удалено ли уже изображение
                if (img.parentElement) {
                    const parent = img.parentElement;
                    img.remove();
                    
                    // Если родитель пустой (только пробелы и нет других элементов), удаляем его
                    if (parent) {
                        const hasOtherContent = Array.from(parent.childNodes).some(node => {
                            if (node.nodeType === 1) { // Element node
                                return node.tagName !== 'BR' && node.textContent.trim() !== '';
                            } else if (node.nodeType === 3) { // Text node
                                return node.textContent.trim() !== '';
                            }
                            return false;
                        });
                        
                        if (!hasOtherContent && parent.children.length === 0) {
                            parent.remove();
                        }
                    }
                }
            });
        }
        
        // Запускаем группировку
        groupAdjacentImages();
        
        // Инициализируем lightbox после группировки
        initLightbox();
    });
    
    function initLightbox() {
        const productDescription = document.querySelector('.product-description');
        if (!productDescription) return;
        
        const images = productDescription.querySelectorAll('img');
        if (images.length === 0) return;
        
        // Создаем lightbox overlay
        const lightbox = document.createElement('div');
        lightbox.className = 'lightbox-overlay';
        lightbox.innerHTML = `
            <div class="lightbox-content">
                <button class="lightbox-close" aria-label="Закрыть">&times;</button>
                <button class="lightbox-nav prev" aria-label="Предыдущее">‹</button>
                <button class="lightbox-nav next" aria-label="Следующее">›</button>
                <div class="lightbox-counter"></div>
                <img src="" alt="">
            </div>
        `;
        document.body.appendChild(lightbox);
        
        const lightboxImg = lightbox.querySelector('img');
        const lightboxClose = lightbox.querySelector('.lightbox-close');
        const lightboxPrev = lightbox.querySelector('.lightbox-nav.prev');
        const lightboxNext = lightbox.querySelector('.lightbox-nav.next');
        const lightboxCounter = lightbox.querySelector('.lightbox-counter');
        
        let currentIndex = 0;
        const imageArray = Array.from(images);
        
        // Функция для открытия lightbox
        function openLightbox(index) {
            currentIndex = index;
            const img = imageArray[index];
            lightboxImg.src = img.src;
            lightboxImg.alt = img.alt || '';
            lightbox.classList.add('active');
            updateCounter();
            document.body.style.overflow = 'hidden';
        }
        
        // Функция для закрытия lightbox
        function closeLightbox() {
            lightbox.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        // Функция для обновления счетчика
        function updateCounter() {
            if (imageArray.length > 1) {
                lightboxCounter.textContent = `${currentIndex + 1} / ${imageArray.length}`;
                lightboxCounter.style.display = 'block';
            } else {
                lightboxCounter.style.display = 'none';
            }
        }
        
        // Функция для показа предыдущего изображения
        function showPrev() {
            currentIndex = (currentIndex - 1 + imageArray.length) % imageArray.length;
            openLightbox(currentIndex);
        }
        
        // Функция для показа следующего изображения
        function showNext() {
            currentIndex = (currentIndex + 1) % imageArray.length;
            openLightbox(currentIndex);
        }
        
        // Добавляем обработчики событий на изображения
        images.forEach((img, index) => {
            img.style.cursor = 'pointer';
            img.addEventListener('click', function(e) {
                e.preventDefault();
                openLightbox(index);
            });
        });
        
        // Обработчики для lightbox
        lightboxClose.addEventListener('click', closeLightbox);
        lightboxPrev.addEventListener('click', function(e) {
            e.stopPropagation();
            showPrev();
        });
        lightboxNext.addEventListener('click', function(e) {
            e.stopPropagation();
            showNext();
        });
        
        // Закрытие по клику на overlay
        lightbox.addEventListener('click', function(e) {
            if (e.target === lightbox) {
                closeLightbox();
            }
        });
        
        // Закрытие по Escape
        document.addEventListener('keydown', function(e) {
            if (lightbox.classList.contains('active')) {
                if (e.key === 'Escape') {
                    closeLightbox();
                } else if (e.key === 'ArrowLeft') {
                    showPrev();
                } else if (e.key === 'ArrowRight') {
                    showNext();
                }
            }
        });
        
        // Предотвращаем закрытие при клике на изображение
        lightboxImg.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
</script>
@endpush
@endsection

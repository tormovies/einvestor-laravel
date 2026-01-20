@extends('layouts.app')

@php
    $seoTitle = $post->seo_title ?? $post->title . ' - EInvestor';
    $seoDescription = $post->seo_description ?? $post->excerpt ?? Str::limit(strip_tags($post->content), 160);
    $seoImage = $post->featuredImage ? $post->featuredImage->image_url : asset('images/og-default.jpg');
    $seoH1 = $post->seo_h1 ?? $post->title;
@endphp

@section('seo')
    <x-seo-meta 
        :title="$seoTitle"
        :description="$seoDescription"
        :image="$seoImage"
        :url="route('articles.show', $post->slug)"
        type="article" />
@endsection

@section('title', $seoTitle)

@section('content')
<div class="content">
    <article>
        <h1>{{ $seoH1 }}</h1>
        
        @if($post->seo_intro_text)
        <div style="margin-top: 1rem; margin-bottom: 2rem; padding: 1rem; background: #f9fafb; border-radius: 8px; border-left: 3px solid #2563eb; font-size: 1.1rem; line-height: 1.6; color: #4b5563;">
            {{ $post->seo_intro_text }}
        </div>
        @endif
        
        <div class="meta" style="margin-bottom: 2rem;">
            <span>Опубликовано: {{ $post->published_at?->format('d.m.Y H:i') }}</span>
            @if($post->categories->count() > 0)
            <span style="margin-left: 1rem;">
                Категории: 
                @foreach($post->categories as $category)
                <a href="{{ route('category.show', $category->slug) }}">{{ $category->name }}</a>@if(!$loop->last), @endif
                @endforeach
            </span>
            @endif
        </div>
        
        <div class="post-content" style="line-height: 1.8;">
            {!! $post->content !!}
            
            <!-- Прикрепленные файлы -->
            @if($post->files && $post->files->count() > 0)
            <div class="post-files-section" style="margin-top: 2rem;">
            <h2 style="font-size: 1.25rem; margin-bottom: 1rem; color: #1f2937;">Прикрепленные файлы</h2>
            <div class="post-files-grid">
                @foreach($post->files as $file)
                <div class="post-file-card">
                    <div class="post-file-icon" style="background: {{ $file->file_icon_color }}20; color: {{ $file->file_icon_color }};">
                        @if($file->file_icon === 'code')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="16 18 22 12 16 6"/>
                                <polyline points="8 6 2 12 8 18"/>
                            </svg>
                        @elseif($file->file_icon === 'executable')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                                <polyline points="9 9 12 12 9 15"/>
                                <polyline points="15 9 12 12 15 15"/>
                            </svg>
                        @elseif($file->file_icon === 'archive')
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="4" width="18" height="4" rx="1"/>
                                <path d="M3 8h18v12a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8z"/>
                                <line x1="9" y1="12" x2="15" y2="12"/>
                            </svg>
                        @else
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M13 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/>
                                <polyline points="13 2 13 9 20 9"/>
                            </svg>
                        @endif
                    </div>
                    <div class="post-file-info">
                        <div class="post-file-name">{{ $file->file_name }}</div>
                        <div class="post-file-meta">
                            @if($file->file_size)
                                <span>{{ $file->formatted_size }}</span>
                            @endif
                            <span>•</span>
                            <span>Скачиваний: <strong>{{ $file->download_count }}</strong></span>
                        </div>
                    </div>
                    <a href="{{ route('post-file.download', $file->id) }}" class="post-file-download-btn" download>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
                            <polyline points="7 10 12 15 17 10"/>
                            <line x1="12" y1="15" x2="12" y2="3"/>
                        </svg>
                        Скачать
                    </a>
                </div>
                @endforeach
            </div>
            </div>
            @endif
        </div>
        
        @if($post->tags->count() > 0)
        <div style="margin-top: 2rem; padding-top: 1rem; border-top: 1px solid #e5e7eb;">
            <strong>Теги:</strong>
            @foreach($post->tags as $tag)
            <a href="{{ route('tag.show', $tag->slug) }}" style="margin-left: 0.5rem;">#{{ $tag->name }}</a>
            @endforeach
        </div>
        @endif
    </article>
    
    @if($relatedPosts->count() > 0)
    <section style="margin-top: 3rem; padding-top: 2rem; border-top: 2px solid #e5e7eb;">
        <h2>Похожие статьи</h2>
        <div class="grid">
            @foreach($relatedPosts as $relatedPost)
            <div class="card">
                <h3 class="card-title">
                    <a href="{{ route('articles.show', $relatedPost->slug) }}">{{ $relatedPost->title }}</a>
                </h3>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>

@push('styles')
<style>
/* Стили для списков в содержании поста */
.post-content ul,
.post-content ol {
    margin: 1.5rem 0;
    padding-left: 2rem;
    line-height: 1.8;
}

.post-content ul {
    list-style-type: disc;
}

.post-content ol {
    list-style-type: decimal;
}

.post-content li {
    margin: 0.75rem 0;
    padding-left: 0.5rem;
    line-height: 1.8;
}

.post-content ul ul,
.post-content ol ol,
.post-content ul ol,
.post-content ol ul {
    margin: 0.75rem 0;
    padding-left: 1.5rem;
}

.post-content ul ul {
    list-style-type: circle;
}

.post-content ul ul ul {
    list-style-type: square;
}

.post-content ol ol {
    list-style-type: lower-alpha;
}

.post-content ol ol ol {
    list-style-type: lower-roman;
}

/* Стили для изображений в содержании поста */
.post-content img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    cursor: pointer;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
    display: block;
}

/* Одиночные изображения - ограниченная высота 320px */
.post-content img:not(.image-gallery img) {
    max-width: 100%;
    max-height: 320px;
    width: auto;
    height: auto;
    object-fit: contain;
}

/* Одиночные изображения вне галереи - ограничение по ширине контейнера */
.post-content > p:has(> img:only-child) img,
.post-content > div:has(> img:only-child) img,
.post-content > img:only-child,
.post-content p img:only-child,
.post-content div img:only-child {
    max-width: 100% !important;
    max-height: 320px !important;
    width: auto !important;
    height: auto !important;
    object-fit: contain;
    margin: 1.5rem auto;
    display: block;
}

/* Обертка для одиночных изображений */
.post-content > p:has(> img:only-child),
.post-content > div:has(> img:only-child) {
    text-align: center;
    margin: 1.5rem 0;
}

/* Галерея для изображений рядом друг с другом */
.post-content {
    width: 100%;
    max-width: 100%;
    overflow-x: hidden;
    box-sizing: border-box;
}

.post-content .image-gallery {
    display: flex;
    flex-wrap: wrap;
    gap: 1rem;
    margin: 1.5rem 0;
    justify-content: center;
    align-items: flex-start;
    width: 100%;
    max-width: 100%;
    box-sizing: border-box;
    overflow: hidden;
}

.post-content .image-gallery > div {
    flex: 1 1 auto;
    min-width: 0;
    max-width: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-sizing: border-box;
    overflow: hidden;
    position: relative;
}

.post-content .image-gallery img {
    width: 100%;
    max-width: 100%;
    height: auto;
    margin: 0;
    object-fit: contain;
    display: block;
    box-sizing: border-box;
}

/* Если 1 изображение в галерее - полная ширина контейнера */
.post-content .image-gallery:has(> div:only-child) {
    max-width: 100%;
}

.post-content .image-gallery:has(> div:only-child) > div {
    max-width: 100%;
    width: 100%;
}

.post-content .image-gallery:has(> div:only-child) img {
    max-width: 100%;
    width: auto;
    height: auto;
}

/* Если 2 изображения - по 50% */
.post-content .image-gallery:has(> div:nth-child(2):last-child) > div {
    flex: 1 1 calc(50% - 0.5rem);
    max-width: calc(50% - 0.5rem);
    min-width: 0;
    width: calc(50% - 0.5rem);
}

/* Если 3 изображения - по 33% */
.post-content .image-gallery:has(> div:nth-child(3):last-child) > div {
    flex: 1 1 calc(33.333% - 0.67rem);
    max-width: calc(33.333% - 0.67rem);
    min-width: 0;
    width: calc(33.333% - 0.67rem);
}

/* Если 4 изображения - по 25% */
.post-content .image-gallery:has(> div:nth-child(4):last-child) > div {
    flex: 1 1 calc(25% - 0.75rem);
    max-width: calc(25% - 0.75rem);
    min-width: 0;
    width: calc(25% - 0.75rem);
}

/* Если 5+ изображений - по 20% */
.post-content .image-gallery:has(> div:nth-child(5)) > div {
    flex: 1 1 calc(20% - 0.8rem);
    max-width: calc(20% - 0.8rem);
    min-width: 0;
    width: calc(20% - 0.8rem);
}

.post-content img:hover {
    transform: scale(1.02);
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
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

.post-files-section {
    margin-top: 2rem;
}

.post-files-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.post-file-card {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    transition: all 0.3s;
}

.post-file-card:hover {
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.post-file-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    flex-shrink: 0;
}

.post-file-icon svg {
    width: 24px;
    height: 24px;
}

.post-file-info {
    flex: 1;
    min-width: 0;
}

.post-file-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 0.9375rem;
    margin-bottom: 0.25rem;
    word-break: break-word;
}

.post-file-meta {
    font-size: 0.8125rem;
    color: #6b7280;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.post-file-download-btn {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    background: #2563eb;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.2s;
    flex-shrink: 0;
}

.post-file-download-btn:hover {
    background: #1d4ed8;
    transform: scale(1.05);
}

.post-file-download-btn svg {
    width: 16px;
    height: 16px;
}

@media (max-width: 768px) {
    .post-files-grid {
        grid-template-columns: 1fr;
    }
    
    .post-file-card {
        flex-direction: column;
        align-items: stretch;
    }
    
    .post-file-download-btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 768px) {
    .post-content {
        width: 100%;
        max-width: 100%;
        overflow-x: hidden;
    }
    
    .post-content .image-gallery {
        flex-direction: column !important;
        gap: 1rem;
        width: 100%;
        max-width: 100%;
        margin-left: 0;
        margin-right: 0;
    }
    
    .post-content .image-gallery > div {
        flex: 1 1 100% !important;
        max-width: 100% !important;
        min-width: 100% !important;
        width: 100% !important;
        min-width: 0 !important;
    }
    
    .post-content .image-gallery img {
        width: 100% !important;
        max-width: 100% !important;
        height: auto !important;
        margin: 0 !important;
        object-fit: contain !important;
    }
    
    .post-content img {
        max-width: 100% !important;
        width: auto !important;
        height: auto !important;
    }
    
    .post-content ul,
    .post-content ol {
        padding-left: 1.5rem;
        margin: 1rem 0;
    }
    
    .post-content li {
        margin: 0.5rem 0;
    }
    
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
}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const postContent = document.querySelector('.post-content');
        if (!postContent) return;
        
        // Группируем изображения, которые находятся рядом друг с другом
        function groupAdjacentImages() {
            const allImages = Array.from(postContent.querySelectorAll('img'));
            if (allImages.length === 0) return;
            
            let currentGroup = [];
            let lastImg = null;
            
            allImages.forEach((img) => {
                const parent = img.parentElement;
                const prevSibling = img.previousElementSibling;
                const nextSibling = img.nextElementSibling;
                
                let isAdjacent = false;
                
                if (lastImg) {
                    const lastParent = lastImg.parentElement;
                    
                    if (parent === lastParent) {
                        isAdjacent = true;
                    } else if (nextSibling && nextSibling.tagName === 'IMG') {
                        isAdjacent = true;
                    } else if (prevSibling && prevSibling.tagName === 'IMG') {
                        isAdjacent = true;
                    } else {
                        const lastRect = lastImg.getBoundingClientRect();
                        const currentRect = img.getBoundingClientRect();
                        const verticalDistance = Math.abs(currentRect.top - lastRect.bottom);
                        if (verticalDistance < 50) {
                            isAdjacent = true;
                        }
                    }
                }
                
                if (isAdjacent && lastImg) {
                    currentGroup.push(img);
                } else {
                    if (currentGroup.length > 0) {
                        createGalleryGroup(currentGroup);
                    }
                    currentGroup = [img];
                }
                
                lastImg = img;
            });
            
            if (currentGroup.length > 0) {
                createGalleryGroup(currentGroup);
            }
        }
        
        function createGalleryGroup(images) {
            // Если только одно изображение, не создаем галерею - оставляем как есть
            if (images.length < 2) return;
            
            const gallery = document.createElement('div');
            gallery.className = 'image-gallery';
            
            images.forEach((img) => {
                const wrapper = document.createElement('div');
                wrapper.appendChild(img.cloneNode(true));
                gallery.appendChild(wrapper);
            });
            
            const firstImg = images[0];
            const firstParent = firstImg.parentElement;
            
            if (firstParent.tagName === 'P' || firstParent.tagName === 'DIV') {
                if (firstParent.children.length === 1 && (firstParent.tagName === 'P' || firstParent.tagName === 'DIV')) {
                    firstParent.parentElement.insertBefore(gallery, firstParent);
                    firstParent.remove();
                } else {
                    firstParent.insertBefore(gallery, firstImg);
                    firstImg.remove();
                    images.slice(1).forEach(img => img.remove());
                }
            } else {
                firstParent.insertBefore(gallery, firstImg);
                firstImg.remove();
                images.slice(1).forEach(img => img.remove());
            }
        }
        
        // Инициализация галереи (только для групп из 2+ изображений)
        groupAdjacentImages();
        
        // Lightbox функциональность
        const overlay = document.createElement('div');
        overlay.className = 'lightbox-overlay';
        overlay.innerHTML = `
            <button class="lightbox-close">&times;</button>
            <button class="lightbox-nav prev">&#8249;</button>
            <button class="lightbox-nav next">&#8250;</button>
            <div class="lightbox-content"></div>
            <div class="lightbox-counter"></div>
        `;
        document.body.appendChild(overlay);
        
        const lightboxContent = overlay.querySelector('.lightbox-content');
        const lightboxClose = overlay.querySelector('.lightbox-close');
        const lightboxPrev = overlay.querySelector('.lightbox-nav.prev');
        const lightboxNext = overlay.querySelector('.lightbox-nav.next');
        const lightboxCounter = overlay.querySelector('.lightbox-counter');
        
        let imageArray = [];
        let currentIndex = 0;
        
        function updateLightbox() {
            const img = imageArray[currentIndex];
            lightboxContent.innerHTML = `<img src="${img.src}" alt="${img.alt || ''}">`;
            lightboxCounter.textContent = `${currentIndex + 1} / ${imageArray.length}`;
        }
        
        function openLightbox(index) {
            const allImages = Array.from(postContent.querySelectorAll('img'));
            imageArray = allImages;
            currentIndex = index;
            overlay.classList.add('active');
            updateLightbox();
            document.body.style.overflow = 'hidden';
        }
        
        function closeLightbox() {
            overlay.classList.remove('active');
            document.body.style.overflow = '';
        }
        
        function nextImage() {
            currentIndex = (currentIndex + 1) % imageArray.length;
            updateLightbox();
        }
        
        function prevImage() {
            currentIndex = (currentIndex - 1 + imageArray.length) % imageArray.length;
            updateLightbox();
        }
        
        // Обработчики событий
        postContent.addEventListener('click', function(e) {
            if (e.target.tagName === 'IMG') {
                const allImages = Array.from(postContent.querySelectorAll('img'));
                const index = allImages.indexOf(e.target);
                if (index !== -1) {
                    openLightbox(index);
                }
            }
        });
        
        lightboxClose.addEventListener('click', closeLightbox);
        lightboxPrev.addEventListener('click', (e) => {
            e.stopPropagation();
            prevImage();
        });
        lightboxNext.addEventListener('click', (e) => {
            e.stopPropagation();
            nextImage();
        });
        
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) {
                closeLightbox();
            }
        });
        
        // Навигация клавиатурой
        document.addEventListener('keydown', function(e) {
            if (!overlay.classList.contains('active')) return;
            
            if (e.key === 'Escape') {
                closeLightbox();
            } else if (e.key === 'ArrowLeft') {
                prevImage();
            } else if (e.key === 'ArrowRight') {
                nextImage();
            }
        });
    });
</script>
@endpush
@endsection

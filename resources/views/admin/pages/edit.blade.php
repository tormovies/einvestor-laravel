@extends('layouts.app')

@section('title', 'Редактировать страницу - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <h1>Редактировать страницу: {{ $page->title }}</h1>
    
    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST" style="max-width: 800px; margin-top: 2rem;">
        @csrf
        @method('PUT')
        
        <div style="display: grid; gap: 1.5rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Заголовок *</label>
                <input type="text" name="title" value="{{ old('title', $page->title) }}" required
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                @error('title') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            @php
                $isSystemPage = in_array($page->slug, ['_home', '_products_list', '_articles_list']);
            @endphp
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Slug (URL)</label>
                @if($isSystemPage)
                    <input type="text" value="{{ $page->slug }}" disabled
                           style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; background: #f3f4f6; color: #6b7280;">
                    <span style="font-size: 0.875rem; color: #6b7280;">Системная страница - slug нельзя изменить</span>
                @else
                    <input type="text" name="slug" value="{{ old('slug', $page->slug) }}"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                    @error('slug') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
                @endif
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Краткое описание</label>
                <textarea name="excerpt" rows="3"
                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">{{ old('excerpt', $page->excerpt) }}</textarea>
                @error('excerpt') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Содержание</label>
                <textarea name="content" rows="20"
                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; font-family: monospace;">{{ old('content', $page->content) }}</textarea>
                <span style="font-size: 0.875rem; color: #6b7280;">HTML поддерживается</span>
                @error('content') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Статус *</label>
                    <select name="status" required style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                        <option value="publish" {{ old('status', $page->status) === 'publish' ? 'selected' : '' }}>Опубликовано</option>
                        <option value="draft" {{ old('status', $page->status) === 'draft' ? 'selected' : '' }}>Черновик</option>
                        <option value="private" {{ old('status', $page->status) === 'private' ? 'selected' : '' }}>Приватная</option>
                    </select>
                    @error('status') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Родительская страница</label>
                    <select name="parent_id" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                        <option value="">Нет (главная страница)</option>
                        @foreach($parentPages as $parentPage)
                        <option value="{{ $parentPage->id }}" {{ old('parent_id', $page->parent_id) == $parentPage->id ? 'selected' : '' }}>
                            {{ $parentPage->title }}
                        </option>
                        @endforeach
                    </select>
                    @error('parent_id') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Порядок в меню</label>
                <input type="number" name="menu_order" value="{{ old('menu_order', $page->menu_order ?? 0) }}" min="0"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <span style="font-size: 0.875rem; color: #6b7280;">Меньшее число = выше в меню (0 по умолчанию)</span>
                @error('menu_order') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <!-- SEO настройки -->
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #e5e7eb;">
                <h2 style="font-size: 1.25rem; margin-bottom: 1rem; color: #374151;">SEO настройки</h2>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">SEO Title (Meta Title)</label>
                <input type="text" name="seo_title" value="{{ old('seo_title', $page->seo_title) }}" maxlength="255"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <span style="font-size: 0.875rem; color: #6b7280;">Заголовок страницы для поисковых систем (50-60 символов). Если не указан, будет использован заголовок страницы.</span>
                @error('seo_title') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">SEO Description (Meta Description)</label>
                <textarea name="seo_description" rows="3" maxlength="320"
                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">{{ old('seo_description', $page->seo_description) }}</textarea>
                <span style="font-size: 0.875rem; color: #6b7280;">Описание страницы для поисковых систем (150-160 символов). Если не указано, будет использовано краткое описание.</span>
                @error('seo_description') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">H1 Заголовок</label>
                <input type="text" name="seo_h1" value="{{ old('seo_h1', $page->seo_h1) }}" maxlength="255"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <span style="font-size: 0.875rem; color: #6b7280;">Основной заголовок страницы (H1). Если не указан, будет использован заголовок страницы.</span>
                @error('seo_h1') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Текст под H1</label>
                <textarea name="seo_intro_text" rows="3"
                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">{{ old('seo_intro_text', $page->seo_intro_text) }}</textarea>
                <span style="font-size: 0.875rem; color: #6b7280;">Вводный текст, который будет отображаться сразу под основным заголовком H1.</span>
                @error('seo_intro_text') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn">Сохранить изменения</button>
                <a href="{{ route('admin.pages.index') }}" class="btn" style="background: #6b7280;">Отмена</a>
            </div>
        </div>
    </form>
</div>
@endsection

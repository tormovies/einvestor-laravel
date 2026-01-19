@extends('layouts.app')

@section('title', 'Редактировать пост - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <h1>Редактировать пост: {{ $post->title }}</h1>
    
    <form action="{{ route('admin.posts.update', $post->id) }}" method="POST" style="max-width: 800px; margin-top: 2rem;">
        @csrf
        @method('PUT')
        
        <div style="display: grid; gap: 1.5rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Заголовок *</label>
                <input type="text" name="title" value="{{ old('title', $post->title) }}" required
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                @error('title') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Slug (URL)</label>
                <input type="text" name="slug" value="{{ old('slug', $post->slug) }}"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                @error('slug') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Краткое описание</label>
                <textarea name="excerpt" rows="3"
                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">{{ old('excerpt', $post->excerpt) }}</textarea>
                @error('excerpt') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Содержание</label>
                <textarea name="content" rows="20"
                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; font-family: monospace;">{{ old('content', $post->content) }}</textarea>
                <span style="font-size: 0.875rem; color: #6b7280;">HTML поддерживается</span>
                @error('content') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Статус *</label>
                <select name="status" required style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                    <option value="publish" {{ old('status', $post->status) === 'publish' ? 'selected' : '' }}>Опубликовано</option>
                    <option value="draft" {{ old('status', $post->status) === 'draft' ? 'selected' : '' }}>Черновик</option>
                </select>
                @error('status') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Категории</label>
                <select name="categories[]" multiple style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; min-height: 150px;">
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $post->categories->contains($category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('categories') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Теги</label>
                <select name="tags[]" multiple style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; min-height: 150px;">
                    @foreach($tags as $tag)
                    <option value="{{ $tag->id }}" {{ $post->tags->contains($tag->id) ? 'selected' : '' }}>{{ $tag->name }}</option>
                    @endforeach
                </select>
                @error('tags') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <!-- SEO настройки -->
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #e5e7eb;">
                <h2 style="font-size: 1.25rem; margin-bottom: 1rem; color: #374151;">SEO настройки</h2>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">SEO Title (Meta Title)</label>
                <input type="text" name="seo_title" value="{{ old('seo_title', $post->seo_title) }}" maxlength="255"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <span style="font-size: 0.875rem; color: #6b7280;">Заголовок страницы для поисковых систем (50-60 символов). Если не указан, будет использован заголовок статьи.</span>
                @error('seo_title') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">SEO Description (Meta Description)</label>
                <textarea name="seo_description" rows="3" maxlength="320"
                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">{{ old('seo_description', $post->seo_description) }}</textarea>
                <span style="font-size: 0.875rem; color: #6b7280;">Описание страницы для поисковых систем (150-160 символов). Если не указано, будет использовано краткое описание.</span>
                @error('seo_description') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">H1 Заголовок</label>
                <input type="text" name="seo_h1" value="{{ old('seo_h1', $post->seo_h1) }}" maxlength="255"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <span style="font-size: 0.875rem; color: #6b7280;">Основной заголовок страницы (H1). Если не указан, будет использован заголовок статьи.</span>
                @error('seo_h1') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Текст под H1</label>
                <textarea name="seo_intro_text" rows="3"
                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">{{ old('seo_intro_text', $post->seo_intro_text) }}</textarea>
                <span style="font-size: 0.875rem; color: #6b7280;">Вводный текст, который будет отображаться сразу под основным заголовком H1.</span>
                @error('seo_intro_text') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn">Сохранить изменения</button>
                <a href="{{ route('admin.posts.index') }}" class="btn" style="background: #6b7280;">Отмена</a>
            </div>
        </div>
    </form>
</div>
@endsection

@extends('layouts.app')

@section('title', 'Создать категорию - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <h1>Создать категорию</h1>
    
    <form action="{{ route('admin.categories.store') }}" method="POST" style="max-width: 800px; margin-top: 2rem;">
        @csrf
        
        <div style="display: grid; gap: 1.5rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Название *</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                @error('name') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Slug (URL)</label>
                <input type="text" name="slug" value="{{ old('slug') }}"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <span style="font-size: 0.875rem; color: #6b7280;">Оставьте пустым для автоматической генерации</span>
                @error('slug') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Описание</label>
                <textarea name="description" rows="4"
                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">{{ old('description') }}</textarea>
                @error('description') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Тип *</label>
                    <select name="type" required style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                        <option value="post" {{ old('type') === 'post' ? 'selected' : '' }}>Статьи</option>
                        <option value="product" {{ old('type') === 'product' ? 'selected' : '' }}>Товары</option>
                    </select>
                    @error('type') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Родительская категория</label>
                    <select name="parent_id" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                        <option value="">Нет (главная категория)</option>
                        @foreach($parentCategories as $parentCategory)
                        <option value="{{ $parentCategory->id }}" {{ old('parent_id') == $parentCategory->id ? 'selected' : '' }}>
                            {{ $parentCategory->name }}
                        </option>
                        @endforeach
                    </select>
                    @error('parent_id') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <!-- SEO настройки -->
            <div style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #e5e7eb;">
                <h2 style="font-size: 1.25rem; margin-bottom: 1rem; color: #374151;">SEO настройки</h2>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">SEO Title (Meta Title)</label>
                <input type="text" name="seo_title" value="{{ old('seo_title') }}" maxlength="255"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <span style="font-size: 0.875rem; color: #6b7280;">Заголовок страницы для поисковых систем (50-60 символов). Если не указан, будет использовано название категории.</span>
                @error('seo_title') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">SEO Description (Meta Description)</label>
                <textarea name="seo_description" rows="3" maxlength="320"
                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">{{ old('seo_description') }}</textarea>
                <span style="font-size: 0.875rem; color: #6b7280;">Описание страницы для поисковых систем (150-160 символов). Если не указано, будет использовано описание категории.</span>
                @error('seo_description') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">H1 Заголовок</label>
                <input type="text" name="seo_h1" value="{{ old('seo_h1') }}" maxlength="255"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <span style="font-size: 0.875rem; color: #6b7280;">Основной заголовок страницы (H1). Если не указан, будет использовано название категории.</span>
                @error('seo_h1') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Текст под H1</label>
                <textarea name="seo_intro_text" rows="3"
                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">{{ old('seo_intro_text') }}</textarea>
                <span style="font-size: 0.875rem; color: #6b7280;">Вводный текст, который будет отображаться сразу под основным заголовком H1.</span>
                @error('seo_intro_text') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn">Создать категорию</button>
                <a href="{{ route('admin.categories.index') }}" class="btn" style="background: #6b7280;">Отмена</a>
            </div>
        </div>
    </form>
</div>
@endsection

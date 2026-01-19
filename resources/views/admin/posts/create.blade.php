@extends('layouts.app')

@section('title', 'Создать пост - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <h1>Создать пост</h1>
    
    <form action="{{ route('admin.posts.store') }}" method="POST" style="max-width: 800px; margin-top: 2rem;">
        @csrf
        
        <div style="display: grid; gap: 1.5rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Заголовок *</label>
                <input type="text" name="title" value="{{ old('title') }}" required
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                @error('title') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Slug (URL)</label>
                <input type="text" name="slug" value="{{ old('slug') }}"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <span style="font-size: 0.875rem; color: #6b7280;">Оставьте пустым для автоматической генерации</span>
                @error('slug') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Краткое описание</label>
                <textarea name="excerpt" rows="3"
                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">{{ old('excerpt') }}</textarea>
                @error('excerpt') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Содержание</label>
                <textarea name="content" rows="20"
                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; font-family: monospace;">{{ old('content') }}</textarea>
                <span style="font-size: 0.875rem; color: #6b7280;">HTML поддерживается</span>
                @error('content') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Статус *</label>
                <select name="status" required style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                    <option value="publish" {{ old('status') === 'publish' ? 'selected' : '' }}>Опубликовано</option>
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Черновик</option>
                </select>
                @error('status') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Категории</label>
                <select name="categories[]" multiple style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; min-height: 150px;">
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
                <span style="font-size: 0.875rem; color: #6b7280;">Удерживайте Ctrl для выбора нескольких</span>
                @error('categories') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Теги</label>
                <select name="tags[]" multiple style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; min-height: 150px;">
                    @foreach($tags as $tag)
                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                    @endforeach
                </select>
                <span style="font-size: 0.875rem; color: #6b7280;">Удерживайте Ctrl для выбора нескольких</span>
                @error('tags') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn">Создать пост</button>
                <a href="{{ route('admin.posts.index') }}" class="btn" style="background: #6b7280;">Отмена</a>
            </div>
        </div>
    </form>
</div>
@endsection

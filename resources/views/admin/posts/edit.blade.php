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
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn">Сохранить изменения</button>
                <a href="{{ route('admin.posts.index') }}" class="btn" style="background: #6b7280;">Отмена</a>
            </div>
        </div>
    </form>
</div>
@endsection

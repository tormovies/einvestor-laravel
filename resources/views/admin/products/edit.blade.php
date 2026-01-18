@extends('layouts.app')

@section('title', 'Редактировать товар - Админ-панель - EInvestor')

@section('content')
<div class="content">
    <h1>Редактировать товар: {{ $product->name }}</h1>
    
    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" style="max-width: 800px; margin-top: 2rem;">
        @csrf
        @method('PUT')
        
        <div style="display: grid; gap: 1.5rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Название *</label>
                <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                @error('name') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Slug (URL)</label>
                <input type="text" name="slug" value="{{ old('slug', $product->slug) }}"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                @error('slug') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Краткое описание</label>
                <textarea name="short_description" rows="3"
                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">{{ old('short_description', $product->short_description) }}</textarea>
                @error('short_description') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Описание</label>
                <textarea name="description" rows="10"
                          style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">{{ old('description', $product->description) }}</textarea>
                @error('description') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Цена *</label>
                    <input type="number" name="price" step="0.01" min="0" value="{{ old('price', $product->price) }}" required
                           style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                    @error('price') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                    @error('sku') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Статус *</label>
                    <select name="status" required style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                        <option value="publish" {{ old('status', $product->status) === 'publish' ? 'selected' : '' }}>Опубликовано</option>
                        <option value="draft" {{ old('status', $product->status) === 'draft' ? 'selected' : '' }}>Черновик</option>
                    </select>
                    @error('status') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Наличие *</label>
                    <select name="stock_status" required style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                        <option value="in_stock" {{ old('stock_status', $product->stock_status) === 'in_stock' ? 'selected' : '' }}>В наличии</option>
                        <option value="out_of_stock" {{ old('stock_status', $product->stock_status) === 'out_of_stock' ? 'selected' : '' }}>Нет в наличии</option>
                    </select>
                    @error('stock_status') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Количество на складе</label>
                <input type="number" name="stock_quantity" min="0" value="{{ old('stock_quantity', $product->stock_quantity) }}"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                    @error('stock_quantity') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Файл для скачивания</label>
                @if($product->file_path)
                <div style="background: #f9fafb; padding: 1rem; border-radius: 4px; margin-bottom: 0.5rem;">
                    <strong>Текущий файл:</strong> {{ $product->file_name ?: basename($product->file_path) }}
                    @if($product->file_size)
                    <span style="color: #6b7280;">({{ number_format($product->file_size / 1024, 2) }} KB)</span>
                    @endif
                </div>
                @endif
                <input type="file" name="file" accept=".zip,.rar,.exe,.dll,.mq4,.mq5"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <span style="font-size: 0.875rem; color: #6b7280;">Выберите новый файл, чтобы заменить текущий</span>
                @error('file') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Категории</label>
                <select name="categories[]" multiple style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; min-height: 150px;">
                    @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ $product->categories->contains($category->id) ? 'selected' : '' }}>{{ $category->name }}</option>
                    @endforeach
                </select>
                @error('categories') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Теги</label>
                <select name="tags[]" multiple style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px; min-height: 150px;">
                    @foreach($tags as $tag)
                    <option value="{{ $tag->id }}" {{ $product->tags->contains($tag->id) ? 'selected' : '' }}>{{ $tag->name }}</option>
                    @endforeach
                </select>
                @error('tags') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn">Сохранить изменения</button>
                <a href="{{ route('admin.products.index') }}" class="btn" style="background: #6b7280;">Отмена</a>
            </div>
        </div>
    </form>
</div>
@endsection

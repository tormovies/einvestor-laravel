@extends('layouts.app')

@section('title', 'Создать редирект - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <h1>Создать редирект</h1>
    
    <form action="{{ route('admin.redirects.store') }}" method="POST" style="max-width: 800px; margin-top: 2rem;">
        @csrf
        
        <div style="display: grid; gap: 1.5rem;">
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Старый URL *</label>
                <input type="text" name="old_url" value="{{ old('old_url') }}" required
                       placeholder="staryj-url"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <span style="font-size: 0.875rem; color: #6b7280;">URL без начального слеша. Введите точно тот URL, который был на старом сайте (может быть URL-encoded: %d0%b8%d0%bd... или обычный текст)</span>
                @error('old_url') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Новый URL *</label>
                <input type="text" name="new_url" value="{{ old('new_url') }}" required
                       placeholder="/novyj-url"
                       style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                <span style="font-size: 0.875rem; color: #6b7280;">Полный URL с начальным слешем (например: /novyj-url или /articles/novaia-statia)</span>
                @error('new_url') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Тип</label>
                    <select name="type" style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                        <option value="">Не указан</option>
                        <option value="post" {{ old('type') === 'post' ? 'selected' : '' }}>Статья</option>
                        <option value="page" {{ old('type') === 'page' ? 'selected' : '' }}>Страница</option>
                        <option value="product" {{ old('type') === 'product' ? 'selected' : '' }}>Товар</option>
                        <option value="category" {{ old('type') === 'category' ? 'selected' : '' }}>Категория</option>
                        <option value="tag" {{ old('type') === 'tag' ? 'selected' : '' }}>Тег</option>
                    </select>
                    @error('type') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500;">HTTP код ответа *</label>
                    <select name="status_code" required style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 4px;">
                        <option value="301" {{ old('status_code', 301) == 301 ? 'selected' : '' }}>301 - Постоянный редирект (рекомендуется)</option>
                        <option value="302" {{ old('status_code') == 302 ? 'selected' : '' }}>302 - Временный редирект</option>
                        <option value="307" {{ old('status_code') == 307 ? 'selected' : '' }}>307 - Временный редирект (с сохранением метода)</option>
                        <option value="308" {{ old('status_code') == 308 ? 'selected' : '' }}>308 - Постоянный редирект (с сохранением метода)</option>
                    </select>
                    @error('status_code') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <div>
                <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                    <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                           style="width: 1.25rem; height: 1.25rem;">
                    <span style="font-weight: 500;">Активен</span>
                </label>
                <span style="font-size: 0.875rem; color: #6b7280; margin-left: 1.75rem;">Неактивные редиректы не будут работать</span>
                @error('is_active') <span style="color: red; font-size: 0.875rem;">{{ $message }}</span> @enderror
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                <button type="submit" class="btn">Создать редирект</button>
                <a href="{{ route('admin.redirects.index') }}" class="btn" style="background: #6b7280;">Отмена</a>
            </div>
        </div>
    </form>
</div>
@endsection

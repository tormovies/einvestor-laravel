@extends('layouts.app')

@section('title', 'Редактировать страницу - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <h1>Редактировать страницу: {{ $page->title }}</h1>
    
    <style>
        .page-form {
            max-width: 1000px;
            margin: 0 auto;
            padding: 1.5rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .form-grid .full-width {
                grid-column: 1 / -1;
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
            font-size: 0.875rem;
        }

        .form-group input[type="text"],
        .form-group input[type="number"],
        .form-group input[type="file"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.875rem;
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
        }

        .form-group input:disabled {
            background: #f3f4f6;
            color: #6b7280;
            cursor: not-allowed;
        }

        .form-group .help-text {
            font-size: 0.75rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        .form-group .error {
            color: #dc2626;
            font-size: 0.75rem;
            margin-top: 0.25rem;
        }

        /* Визуальный редактор */
        .editor-container {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            overflow: hidden;
            background: #fff;
        }

        .editor-content {
            min-height: 300px;
            max-height: 480px;
            overflow: hidden;
        }

        .editor-content .ql-editor {
            min-height: 300px;
            max-height: 480px;
            font-size: 0.875rem;
            line-height: 1.6;
            overflow-y: auto;
            padding: 1rem;
        }

        .editor-content .ql-editor ol,
        .editor-content .ql-editor ul {
            padding-left: 1.5em;
            margin: 1em 0;
        }

        .editor-content .ql-editor ol {
            list-style-type: decimal;
        }

        .editor-content .ql-editor ul {
            list-style-type: disc;
        }

        .editor-content .ql-editor li {
            margin: 0.5em 0;
        }

        .editor-content .ql-editor ol li,
        .editor-content .ql-editor ul li {
            display: list-item;
            padding-left: 0.25em;
        }

        .editor-content .ql-container {
            font-family: inherit;
        }

        .editor-content .ql-snow .ql-stroke {
            stroke: #374151;
        }

        .editor-content .ql-snow .ql-fill {
            fill: #374151;
        }

        /* Кнопки формы */
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #2563eb;
            color: #fff;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #6b7280;
            color: #fff;
        }

        .btn-secondary:hover {
            background: #4b5563;
        }

        @media (max-width: 767px) {
            .page-form {
                padding: 1rem;
            }

            .form-grid {
                gap: 1rem;
            }
        }
    </style>

    <form action="{{ route('admin.pages.update', $page->id) }}" method="POST" class="page-form">
        @csrf
        @method('PUT')
        
        <div class="form-grid">
            <div class="form-group full-width">
                <label for="title">Заголовок *</label>
                <input type="text" name="title" id="title" value="{{ old('title', $page->title) }}" required>
                @error('title') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            @php
                $isSystemPage = in_array($page->slug, ['_home', '_products_list', '_articles_list']);
            @endphp
            <div class="form-group full-width">
                <label for="slug">Slug (URL)</label>
                @if($isSystemPage)
                    <input type="text" value="{{ $page->slug }}" disabled>
                    <span class="help-text">Системная страница - slug нельзя изменить</span>
                @else
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $page->slug) }}">
                    @error('slug') <span class="error">{{ $message }}</span> @enderror
                @endif
            </div>
            
            <div class="form-group full-width">
                <label for="excerpt">Краткое описание</label>
                <textarea name="excerpt" id="excerpt" rows="3">{{ old('excerpt', $page->excerpt) }}</textarea>
                @error('excerpt') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group full-width">
                <label for="content">Содержание</label>
                <div class="editor-container">
                    <div id="editor-content" class="editor-content"></div>
                </div>
                <textarea name="content" id="content" style="display: none;">{{ old('content', $page->content) }}</textarea>
                @error('content') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group">
                <label for="status">Статус *</label>
                <select name="status" id="status" required>
                    <option value="publish" {{ old('status', $page->status) === 'publish' ? 'selected' : '' }}>Опубликовано</option>
                    <option value="draft" {{ old('status', $page->status) === 'draft' ? 'selected' : '' }}>Черновик</option>
                    <option value="private" {{ old('status', $page->status) === 'private' ? 'selected' : '' }}>Приватная</option>
                </select>
                @error('status') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group">
                <label for="parent_id">Родительская страница</label>
                <select name="parent_id" id="parent_id">
                    <option value="">Нет (главная страница)</option>
                    @foreach($parentPages as $parentPage)
                    <option value="{{ $parentPage->id }}" {{ old('parent_id', $page->parent_id) == $parentPage->id ? 'selected' : '' }}>
                        {{ $parentPage->title }}
                    </option>
                    @endforeach
                </select>
                @error('parent_id') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group">
                <label for="menu_order">Порядок в меню</label>
                <input type="number" name="menu_order" id="menu_order" value="{{ old('menu_order', $page->menu_order ?? 0) }}" min="0">
                <span class="help-text">Меньшее число = выше в меню (0 по умолчанию)</span>
                @error('menu_order') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <!-- SEO настройки -->
            <div class="form-group full-width" style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #e5e7eb;">
                <h2 style="font-size: 1.25rem; margin-bottom: 1rem; color: #374151;">SEO настройки</h2>
            </div>
            
            <div class="form-group full-width">
                <label for="seo_title">SEO Title (Meta Title)</label>
                <input type="text" name="seo_title" id="seo_title" value="{{ old('seo_title', $page->seo_title) }}" maxlength="255">
                <span class="help-text">Заголовок страницы для поисковых систем (50-60 символов). Если не указан, будет использован заголовок страницы.</span>
                @error('seo_title') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group full-width">
                <label for="seo_description">SEO Description (Meta Description)</label>
                <textarea name="seo_description" id="seo_description" rows="3" maxlength="320">{{ old('seo_description', $page->seo_description) }}</textarea>
                <span class="help-text">Описание страницы для поисковых систем (150-160 символов). Если не указано, будет использовано краткое описание.</span>
                @error('seo_description') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group full-width">
                <label for="seo_h1">H1 Заголовок</label>
                <input type="text" name="seo_h1" id="seo_h1" value="{{ old('seo_h1', $page->seo_h1) }}" maxlength="255">
                <span class="help-text">Основной заголовок страницы (H1). Если не указан, будет использован заголовок страницы.</span>
                @error('seo_h1') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group full-width">
                <label for="seo_intro_text">Текст под H1</label>
                <textarea name="seo_intro_text" id="seo_intro_text" rows="3">{{ old('seo_intro_text', $page->seo_intro_text) }}</textarea>
                <span class="help-text">Вводный текст, который будет отображаться сразу под основным заголовком H1.</span>
                @error('seo_intro_text') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Сохранить изменения</button>
            <a href="{{ route('admin.pages.index') }}" class="btn btn-secondary">Отмена</a>
        </div>
    </form>
</div>

<!-- Quill.js для визуального редактора -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Инициализация визуального редактора
        const quill = new Quill('#editor-content', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                    [{ 'color': [] }, { 'background': [] }],
                    ['link', 'image'],
                    ['clean']
                ]
            }
        });

        // Загрузка существующего контента
        const contentTextarea = document.getElementById('content');
        if (contentTextarea && contentTextarea.value) {
            try {
                quill.setText('');
                quill.clipboard.dangerouslyPasteHTML(0, contentTextarea.value);
            } catch (e) {
                console.error('Error loading content:', e);
                quill.root.innerHTML = contentTextarea.value;
            }
        }

        // Сохранение контента в textarea перед отправкой формы
        const form = document.querySelector('.page-form');
        if (form && contentTextarea) {
            form.addEventListener('submit', function(e) {
                const quillContent = quill.root.innerHTML;
                contentTextarea.value = quillContent;
            });
        }
    });
</script>
@endsection

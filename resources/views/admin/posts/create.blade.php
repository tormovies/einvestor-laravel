@extends('layouts.app')

@section('title', 'Создать пост - Админ-панель - EInvestor')

@section('content')
<div class="content">
    @include('admin.partials.navigation')
    <h1>Создать пост</h1>
    
    <style>
        .post-form {
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

        /* Чипсы для категорий и тегов */
        .chips-wrapper {
            position: relative;
        }

        .chips-container {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            padding: 0.5rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            min-height: 48px;
            background: #fff;
            align-items: center;
        }

        .chip {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.75rem;
            background: #2563eb;
            color: #fff;
            border-radius: 9999px;
            font-size: 0.875rem;
        }

        .chip .chip-remove {
            cursor: pointer;
            margin-left: 0.25rem;
            font-weight: bold;
            opacity: 0.8;
        }

        .chip .chip-remove:hover {
            opacity: 1;
        }

        .chips-input-wrapper {
            position: relative;
            flex: 1;
            min-width: 150px;
        }

        .chips-input {
            width: 100%;
            border: none;
            outline: none;
            padding: 0.5rem;
            font-size: 0.875rem;
            background: transparent;
        }

        .chips-dropdown {
            position: absolute;
            top: calc(100% + 0.25rem);
            left: 0;
            right: 0;
            background: #fff;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-height: 400px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .chips-dropdown.show {
            display: block;
        }

        .chips-dropdown-item {
            padding: 0.75rem;
            cursor: pointer;
            border-bottom: 1px solid #f3f4f6;
        }

        .chips-dropdown-item:hover {
            background: #f3f4f6;
        }

        .chips-dropdown-item:last-child {
            border-bottom: none;
        }

        .chips-dropdown-item.disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .chips-dropdown-item.create-new {
            background: #eff6ff;
            color: #2563eb;
            font-weight: 500;
        }

        .chips-dropdown-item.create-new:hover {
            background: #dbeafe;
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

        /* Стили для файлов */
        .files-section {
            background: #f9fafb;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .file-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem;
            background: white;
            border-radius: 4px;
            margin-bottom: 0.25rem;
            border: 1px solid #e5e7eb;
        }

        @media (max-width: 767px) {
            .post-form {
                padding: 1rem;
            }

            .form-grid {
                gap: 1rem;
            }

            .chips-container {
                min-height: 56px;
            }
        }
    </style>

    <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data" class="post-form">
        @csrf
        
        <div class="form-grid">
            <div class="form-group full-width">
                <label for="title">Заголовок *</label>
                <input type="text" name="title" id="title" value="{{ old('title') }}" required>
                @error('title') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group full-width">
                <label for="slug">Slug (URL)</label>
                <input type="text" name="slug" id="slug" value="{{ old('slug') }}">
                <span class="help-text">Оставьте пустым для автоматической генерации</span>
                @error('slug') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group full-width">
                <label for="excerpt">Краткое описание</label>
                <textarea name="excerpt" id="excerpt" rows="3">{{ old('excerpt') }}</textarea>
                @error('excerpt') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group full-width">
                <label for="content">Содержание</label>
                <div class="editor-container">
                    <div id="editor-content" class="editor-content"></div>
                </div>
                <textarea name="content" id="content" style="display: none;">{{ old('content') }}</textarea>
                @error('content') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group">
                <label for="status">Статус *</label>
                <select name="status" id="status" required>
                    <option value="publish" {{ old('status') === 'publish' ? 'selected' : '' }}>Опубликовано</option>
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Черновик</option>
                </select>
                @error('status') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <!-- Категории (чипсы с поиском) -->
            <div class="form-group full-width">
                <label>Категории</label>
                <div class="chips-wrapper">
                    <div class="chips-container" id="categories-container">
                        <div class="chips-input-wrapper">
                            <input type="text" class="chips-input" id="categories-input" placeholder="Начните вводить для поиска...">
                            <div class="chips-dropdown" id="categories-dropdown"></div>
                        </div>
                    </div>
                </div>
                <div id="categories-hidden-inputs"></div>
                @error('categories') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <!-- Теги (чипсы с поиском) -->
            <div class="form-group full-width">
                <label>Теги</label>
                <div class="chips-wrapper">
                    <div class="chips-container" id="tags-container">
                        <div class="chips-input-wrapper">
                            <input type="text" class="chips-input" id="tags-input" placeholder="Начните вводить для поиска...">
                            <div class="chips-dropdown" id="tags-dropdown"></div>
                        </div>
                    </div>
                </div>
                <div id="tags-hidden-inputs"></div>
                @error('tags') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <!-- Прикрепленные файлы -->
            <div class="form-group full-width" style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #e5e7eb;">
                <h2 style="font-size: 1.25rem; margin-bottom: 1rem; color: #374151;">Прикрепленные файлы</h2>
                
                <div class="form-group">
                    <label for="files">Загрузить файлы</label>
                    <input type="file" name="files[]" id="files" multiple accept=".mq4,.ex4,.mq5,.ex5,.zip,.rar,.7z">
                    <span class="help-text">Можно выбрать несколько файлов. Типы: MQ4, EX4, MQ5, EX5, архивы (ZIP, RAR, 7Z). Максимум 10MB каждый.</span>
                    @error('files.*') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
            
            <!-- SEO настройки -->
            <div class="form-group full-width" style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #e5e7eb;">
                <h2 style="font-size: 1.25rem; margin-bottom: 1rem; color: #374151;">SEO настройки</h2>
            </div>
            
            <div class="form-group full-width">
                <label for="seo_title">SEO Title (Meta Title)</label>
                <input type="text" name="seo_title" id="seo_title" value="{{ old('seo_title') }}" maxlength="255">
                <span class="help-text">Заголовок страницы для поисковых систем (50-60 символов). Если не указан, будет использован заголовок статьи.</span>
                @error('seo_title') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group full-width">
                <label for="seo_description">SEO Description (Meta Description)</label>
                <textarea name="seo_description" id="seo_description" rows="3" maxlength="320">{{ old('seo_description') }}</textarea>
                <span class="help-text">Описание страницы для поисковых систем (150-160 символов). Если не указано, будет использовано краткое описание.</span>
                @error('seo_description') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group full-width">
                <label for="seo_h1">H1 Заголовок</label>
                <input type="text" name="seo_h1" id="seo_h1" value="{{ old('seo_h1') }}" maxlength="255">
                <span class="help-text">Основной заголовок страницы (H1). Если не указан, будет использован заголовок статьи.</span>
                @error('seo_h1') <span class="error">{{ $message }}</span> @enderror
            </div>
            
            <div class="form-group full-width">
                <label for="seo_intro_text">Текст под H1</label>
                <textarea name="seo_intro_text" id="seo_intro_text" rows="3">{{ old('seo_intro_text') }}</textarea>
                <span class="help-text">Вводный текст, который будет отображаться сразу под основным заголовком H1.</span>
                @error('seo_intro_text') <span class="error">{{ $message }}</span> @enderror
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Создать пост</button>
            <a href="{{ route('admin.posts.index') }}" class="btn btn-secondary">Отмена</a>
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
        const form = document.querySelector('.post-form');
        if (form && contentTextarea) {
            form.addEventListener('submit', function(e) {
                const quillContent = quill.root.innerHTML;
                contentTextarea.value = quillContent;
            });
        }
    });

    // Данные для категорий и тегов
    const categoriesData = @json($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name]));
    const tagsData = @json($tags->map(fn($t) => ['id' => $t->id, 'name' => $t->name]));

    // Выбранные элементы
    @php
        $selectedCategories = old('categories') ?? [];
        $selectedTags = old('tags') ?? [];
    @endphp
    let selectedCategories = @json($selectedCategories);
    let selectedTags = @json($selectedTags);

    // Функция для создания чипса
    function createChip(id, name, type) {
        const chip = document.createElement('div');
        chip.className = 'chip';
        chip.dataset.id = id;
        chip.innerHTML = `
            <span>${name}</span>
            <span class="chip-remove" onclick="removeChip(${id}, '${type}')">×</span>
        `;
        return chip;
    }

    // Функция для удаления чипса
    function removeChip(id, type) {
        if (type === 'categories') {
            selectedCategories = selectedCategories.filter(catId => catId != id);
            updateChips('categories', selectedCategories, categoriesData);
        } else {
            selectedTags = selectedTags.filter(tagId => tagId != id);
            updateChips('tags', selectedTags, tagsData);
        }
    }

    // Функция для обновления чипсов
    function updateChips(type, selected, data) {
        const container = document.getElementById(`${type}-container`);
        const hiddenInputs = document.getElementById(`${type}-hidden-inputs`);
        
        // Сохраняем поле ввода перед очисткой
        const inputWrapper = container.querySelector('.chips-input-wrapper');
        const inputWrapperClone = inputWrapper.cloneNode(true);
        
        // Очищаем контейнер
        container.innerHTML = '';
        
        // Добавляем выбранные чипсы
        selected.forEach(id => {
            const item = data.find(d => d.id == id);
            if (item) {
                const chip = createChip(item.id, item.name, type);
                container.appendChild(chip);
            }
        });
        
        // Добавляем поле ввода обратно
        container.appendChild(inputWrapperClone);
        
        // Восстанавливаем обработчики событий для нового поля ввода
        const newInput = inputWrapperClone.querySelector('.chips-input');
        if (type === 'categories') {
            newInput.addEventListener('input', function(e) {
                const query = e.target.value;
                showDropdown('categories', query, categoriesData, selectedCategories, false);
            });
            newInput.addEventListener('focus', function(e) {
                const query = e.target.value;
                showDropdown('categories', query, categoriesData, selectedCategories, true);
            });
        } else {
            newInput.addEventListener('input', function(e) {
                const query = e.target.value;
                showDropdown('tags', query, tagsData, selectedTags, false);
            });
            newInput.addEventListener('focus', function(e) {
                const query = e.target.value;
                showDropdown('tags', query, tagsData, selectedTags, true);
            });
        }
        
        // Обновляем скрытые input'ы
        hiddenInputs.innerHTML = '';
        selected.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = `${type}[]`;
            input.value = id;
            hiddenInputs.appendChild(input);
        });
    }

    // Функция для фильтрации и отображения выпадающего списка
    function showDropdown(type, query, data, selected, showAll = false) {
        const dropdown = document.getElementById(`${type}-dropdown`);
        dropdown.innerHTML = '';
        
        let filtered;
        if (showAll || query.length === 0) {
            // Показываем все невыбранные элементы
            filtered = data.filter(item => !selected.includes(item.id));
        } else {
            // Фильтруем по запросу
            filtered = data.filter(item => {
                const matches = item.name.toLowerCase().includes(query.toLowerCase());
                const notSelected = !selected.includes(item.id);
                return matches && notSelected;
            });
        }
        
        if (filtered.length === 0 && query.length > 0) {
            dropdown.innerHTML = '<div class="chips-dropdown-item disabled">Ничего не найдено</div>';
        } else {
            filtered.forEach(item => {
                const itemEl = document.createElement('div');
                itemEl.className = 'chips-dropdown-item';
                itemEl.textContent = item.name;
                itemEl.onclick = () => {
                    if (type === 'categories') {
                        selectedCategories.push(item.id);
                        updateChips('categories', selectedCategories, categoriesData);
                    } else {
                        selectedTags.push(item.id);
                        updateChips('tags', selectedTags, tagsData);
                    }
                    document.getElementById(`${type}-input`).value = '';
                    dropdown.classList.remove('show');
                };
                dropdown.appendChild(itemEl);
            });
        }
        
        dropdown.classList.add('show');
    }

    // Инициализация категорий
    updateChips('categories', selectedCategories, categoriesData);
    const categoriesInput = document.getElementById('categories-input');
    categoriesInput.addEventListener('input', function(e) {
        const query = e.target.value;
        showDropdown('categories', query, categoriesData, selectedCategories, false);
    });
    categoriesInput.addEventListener('focus', function(e) {
        const query = e.target.value;
        showDropdown('categories', query, categoriesData, selectedCategories, true);
    });

    // Инициализация тегов
    updateChips('tags', selectedTags, tagsData);
    const tagsInput = document.getElementById('tags-input');
    tagsInput.addEventListener('input', function(e) {
        const query = e.target.value;
        showDropdown('tags', query, tagsData, selectedTags, false);
    });
    tagsInput.addEventListener('focus', function(e) {
        const query = e.target.value;
        showDropdown('tags', query, tagsData, selectedTags, true);
    });

    // Закрытие выпадающих списков при клике вне
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.chips-wrapper')) {
            document.querySelectorAll('.chips-dropdown').forEach(dropdown => {
                dropdown.classList.remove('show');
            });
        }
    });
</script>
@endsection

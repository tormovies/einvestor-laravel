@php
    $isEdit = isset($product);
    $product = $product ?? null;
@endphp

<style>
    .product-form {
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
        max-height: 200px;
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

    /* Превью изображения внутри инпута */
    .image-upload-wrapper {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        background: #fff;
    }

    .image-preview-container {
        flex-shrink: 0;
        width: 120px;
        height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f9fafb;
        border: 1px solid #e5e7eb;
        border-radius: 6px;
        overflow: hidden;
    }

    .image-preview-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .image-preview-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 2rem;
        background: #f3f4f6;
    }

    .image-input-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .image-input-wrapper input[type="file"] {
        padding: 0.5rem;
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

    /* Стили для списков в редакторе */
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

    /* Убеждаемся, что Quill стили применяются */
    .editor-content .ql-container {
        font-family: inherit;
    }

    .editor-content .ql-snow .ql-stroke {
        stroke: #374151;
    }

    .editor-content .ql-snow .ql-fill {
        fill: #374151;
    }

    /* Дополнительные стили для правильного отображения списков */
    .editor-content .ql-editor ol,
    .editor-content .ql-editor ul {
        margin-top: 0.5em;
        margin-bottom: 0.5em;
    }

    .editor-content .ql-editor ol > li,
    .editor-content .ql-editor ul > li {
        margin-top: 0.25em;
        margin-bottom: 0.25em;
    }

    /* Убеждаемся, что списки видны */
    .editor-content .ql-editor ol {
        list-style-position: outside;
        list-style-type: decimal;
    }

    .editor-content .ql-editor ul {
        list-style-position: outside;
        list-style-type: disc;
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
        .product-form {
            padding: 1rem;
        }

        .form-grid {
            gap: 1rem;
        }

        .chips-container {
            min-height: 56px;
        }

        .image-upload-wrapper {
            flex-direction: column;
        }

        .image-preview-container {
            width: 100%;
            max-width: 200px;
            height: 200px;
        }
    }
</style>

<form action="{{ $isEdit ? route('admin.products.update', $product->id) : route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="product-form">
    @csrf
    @if($isEdit)
        @method('PUT')
    @endif
    
    <div class="form-grid">
        <!-- Название -->
        <div class="form-group full-width">
            <label for="name">Название *</label>
            <input type="text" name="name" id="name" value="{{ old('name', $product->name ?? '') }}" required>
            @error('name') <span class="error">{{ $message }}</span> @enderror
        </div>
        
        <!-- Slug -->
        <div class="form-group full-width">
            <label for="slug">Slug (URL)</label>
            <input type="text" name="slug" id="slug" value="{{ old('slug', $product->slug ?? '') }}">
            <span class="help-text">Оставьте пустым для автоматической генерации</span>
            @error('slug') <span class="error">{{ $message }}</span> @enderror
        </div>
        
        <!-- Краткое описание -->
        <div class="form-group full-width">
            <label for="short_description">Краткое описание</label>
            <textarea name="short_description" id="short_description" rows="3">{{ old('short_description', $product->short_description ?? '') }}</textarea>
            @error('short_description') <span class="error">{{ $message }}</span> @enderror
        </div>
        
        <!-- Описание с визуальным редактором -->
        <div class="form-group full-width">
            <label for="description">Описание</label>
            <div class="editor-container">
                <div id="editor-content" class="editor-content"></div>
            </div>
            <textarea name="description" id="description" style="display: none;">{{ old('description', $product->description ?? '') }}</textarea>
            @error('description') <span class="error">{{ $message }}</span> @enderror
        </div>
        
        <!-- Цена и SKU -->
        <div class="form-group">
            <label for="price">Цена *</label>
            <input type="number" name="price" id="price" step="0.01" min="0" value="{{ old('price', $product->price ?? 0) }}" required>
            @error('price') <span class="error">{{ $message }}</span> @enderror
        </div>
        
        <div class="form-group">
            <label for="sku">SKU</label>
            <input type="text" name="sku" id="sku" value="{{ old('sku', $product->sku ?? '') }}">
            @error('sku') <span class="error">{{ $message }}</span> @enderror
        </div>
        
        <!-- Статус и Наличие -->
        <div class="form-group">
            <label for="status">Статус *</label>
            <select name="status" id="status" required>
                <option value="publish" {{ old('status', $product->status ?? 'publish') === 'publish' ? 'selected' : '' }}>Опубликовано</option>
                <option value="draft" {{ old('status', $product->status ?? '') === 'draft' ? 'selected' : '' }}>Черновик</option>
            </select>
            @error('status') <span class="error">{{ $message }}</span> @enderror
        </div>
        
        <div class="form-group">
            <label for="stock_status">Наличие *</label>
            <select name="stock_status" id="stock_status" required>
                <option value="in_stock" {{ old('stock_status', $product->stock_status ?? 'in_stock') === 'in_stock' ? 'selected' : '' }}>В наличии</option>
                <option value="out_of_stock" {{ old('stock_status', $product->stock_status ?? '') === 'out_of_stock' ? 'selected' : '' }}>Нет в наличии</option>
            </select>
            @error('stock_status') <span class="error">{{ $message }}</span> @enderror
        </div>
        
        <!-- Количество на складе -->
        <div class="form-group">
            <label for="stock_quantity">Количество на складе</label>
            <input type="number" name="stock_quantity" id="stock_quantity" min="0" value="{{ old('stock_quantity', $product->stock_quantity ?? '') }}">
            @error('stock_quantity') <span class="error">{{ $message }}</span> @enderror
        </div>
        
        <!-- Изображение товара -->
        <div class="form-group full-width">
            <label for="image">Изображение товара</label>
            <div class="image-upload-wrapper">
                <div class="image-preview-container" id="image-preview-container">
                    @if($isEdit && $product && $product->featuredImage)
                        <img id="preview-img" src="{{ $product->featuredImage->image_url }}" alt="{{ $product->name }}">
                    @else
                        <div class="image-preview-placeholder" id="image-placeholder">
                            📷
                        </div>
                    @endif
                </div>
                <div class="image-input-wrapper">
                    <input type="file" name="image" id="image" accept="image/*" onchange="previewImage(this)">
                    <span class="help-text">Рекомендуемый размер: 800x600px. Максимум 5MB</span>
                    @error('image') <span class="error">{{ $message }}</span> @enderror
                </div>
            </div>
        </div>
        
        <!-- Файл для скачивания -->
        <div class="form-group full-width">
            <label for="file">Файл для скачивания</label>
            @if($isEdit && $product && $product->file_path)
                <div style="background: #f9fafb; padding: 1rem; border-radius: 6px; margin-bottom: 0.5rem;">
                    <strong>Текущий файл:</strong> {{ $product->file_name ?: basename($product->file_path) }}
                    @if($product->file_size)
                        <span style="color: #6b7280;">({{ number_format($product->file_size / 1024, 2) }} KB)</span>
                    @endif
                </div>
            @endif
            <input type="file" name="file" id="file" accept=".zip,.rar,.exe,.dll,.mq4,.mq5">
            <span class="help-text">Максимум 10MB</span>
            @error('file') <span class="error">{{ $message }}</span> @enderror
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
        
        <!-- SEO настройки -->
        <div class="form-group full-width" style="margin-top: 2rem; padding-top: 2rem; border-top: 2px solid #e5e7eb;">
            <h2 style="font-size: 1.25rem; margin-bottom: 1rem; color: #374151;">SEO настройки</h2>
        </div>
        
        <!-- SEO Title -->
        <div class="form-group full-width">
            <label for="seo_title">SEO Title (Meta Title)</label>
            <input type="text" name="seo_title" id="seo_title" value="{{ old('seo_title', $product->seo_title ?? '') }}" maxlength="255">
            <span class="help-text">Заголовок страницы для поисковых систем (рекомендуется 50-60 символов). Если не указан, будет использовано название товара.</span>
            @error('seo_title') <span class="error">{{ $message }}</span> @enderror
        </div>
        
        <!-- SEO Description -->
        <div class="form-group full-width">
            <label for="seo_description">SEO Description (Meta Description)</label>
            <textarea name="seo_description" id="seo_description" rows="3" maxlength="320">{{ old('seo_description', $product->seo_description ?? '') }}</textarea>
            <span class="help-text">Описание страницы для поисковых систем (рекомендуется 150-160 символов). Если не указано, будет использовано краткое описание.</span>
            @error('seo_description') <span class="error">{{ $message }}</span> @enderror
        </div>
        
        <!-- SEO H1 -->
        <div class="form-group full-width">
            <label for="seo_h1">H1 Заголовок</label>
            <input type="text" name="seo_h1" id="seo_h1" value="{{ old('seo_h1', $product->seo_h1 ?? '') }}" maxlength="255">
            <span class="help-text">Основной заголовок страницы (H1). Если не указан, будет использовано название товара.</span>
            @error('seo_h1') <span class="error">{{ $message }}</span> @enderror
        </div>
        
        <!-- SEO Intro Text -->
        <div class="form-group full-width">
            <label for="seo_intro_text">Текст под H1</label>
            <textarea name="seo_intro_text" id="seo_intro_text" rows="3">{{ old('seo_intro_text', $product->seo_intro_text ?? '') }}</textarea>
            <span class="help-text">Вводный текст, который будет отображаться сразу под основным заголовком H1.</span>
            @error('seo_intro_text') <span class="error">{{ $message }}</span> @enderror
        </div>
    </div>
    
    <!-- Кнопки действий -->
    <div class="form-actions">
        <button type="submit" class="btn btn-primary">{{ $isEdit ? 'Сохранить изменения' : 'Создать товар' }}</button>
        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">Отмена</a>
    </div>
</form>

<!-- Quill.js для визуального редактора -->
<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
    // Ждем полной загрузки DOM
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
        const descriptionTextarea = document.getElementById('description');
        if (descriptionTextarea && descriptionTextarea.value) {
            // Используем dangerouslyPasteHTML для правильной загрузки HTML, включая списки
            try {
                // Очищаем редактор перед загрузкой
                quill.setText('');
                // Загружаем HTML контент
                quill.clipboard.dangerouslyPasteHTML(0, descriptionTextarea.value);
            } catch (e) {
                console.error('Error loading content:', e);
                // Если не получается, используем обычный innerHTML
                quill.root.innerHTML = descriptionTextarea.value;
            }
        }

        // Сохранение контента в textarea перед отправкой формы
        const form = document.querySelector('.product-form');
        if (form && descriptionTextarea) {
            form.addEventListener('submit', function(e) {
                // Получаем HTML с правильным форматированием из Quill
                const quillContent = quill.root.innerHTML;
                
                // Сохраняем контент в textarea
                descriptionTextarea.value = quillContent;
                
                // Дополнительная проверка - убеждаемся, что значение установлено
                if (descriptionTextarea.value !== quillContent) {
                    descriptionTextarea.value = quillContent;
                }
                
                // Логирование для отладки (можно удалить после проверки)
                console.log('Description saved:', descriptionTextarea.value.substring(0, 100) + '...');
            });
        }
    });

    // Превью изображения
    function previewImage(input) {
        const container = document.getElementById('image-preview-container');
        const placeholder = document.getElementById('image-placeholder');
        let previewImg = document.getElementById('preview-img');
        
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // Если изображения еще нет, создаем его
                if (!previewImg || !previewImg.tagName || previewImg.tagName !== 'IMG') {
                    previewImg = document.createElement('img');
                    previewImg.id = 'preview-img';
                    container.innerHTML = '';
                    container.appendChild(previewImg);
                }
                
                previewImg.src = e.target.result;
                previewImg.alt = 'Превью изображения';
                
                // Скрываем заглушку, если она есть
                if (placeholder) {
                    placeholder.style.display = 'none';
                }
            };
            reader.readAsDataURL(input.files[0]);
        } else {
            // Если файл не выбран, показываем заглушку или текущее изображение
            if (!previewImg || !previewImg.src || previewImg.src === '') {
                if (placeholder) {
                    placeholder.style.display = 'flex';
                }
            }
        }
    }

    // Данные для категорий и тегов
    const categoriesData = @json($categories->map(fn($c) => ['id' => $c->id, 'name' => $c->name]));
    const tagsData = @json($tags->map(fn($t) => ['id' => $t->id, 'name' => $t->name]));

    // Выбранные элементы
    @php
        $selectedCategories = [];
        if ($isEdit && $product && $product->categories) {
            $selectedCategories = $product->categories->pluck('id')->toArray();
        }
        if (old('categories')) {
            $selectedCategories = old('categories');
        }
        
        $selectedTags = [];
        if ($isEdit && $product && $product->tags) {
            $selectedTags = $product->tags->pluck('id')->toArray();
        }
        if (old('tags')) {
            $selectedTags = old('tags');
        }
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
        
        if (filtered.length === 0 && query.length > 0 && type === 'tags') {
            // Для тегов показываем опцию создания нового тега
            const createItem = document.createElement('div');
            createItem.className = 'chips-dropdown-item create-new';
            createItem.innerHTML = `<strong>Создать новый тег:</strong> "${query}"`;
            createItem.onclick = () => {
                createNewTag(query);
            };
            dropdown.appendChild(createItem);
        } else if (filtered.length === 0 && query.length > 0) {
            dropdown.innerHTML = '<div class="chips-dropdown-item disabled">Ничего не найдено</div>';
        } else {
            filtered.slice(0, 10).forEach(item => {
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

    // Функция для создания нового тега
    function createNewTag(name) {
        const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        fetch('{{ route("admin.tags.create") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token
            },
            body: JSON.stringify({ name: name })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Добавляем новый тег в данные
                tagsData.push({
                    id: data.tag.id,
                    name: data.tag.name
                });
                
                // Добавляем тег в выбранные
                selectedTags.push(data.tag.id);
                updateChips('tags', selectedTags, tagsData);
                
                // Очищаем поле ввода
                document.getElementById('tags-input').value = '';
                document.getElementById('tags-dropdown').classList.remove('show');
            } else {
                alert('Ошибка при создании тега');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ошибка при создании тега');
        });
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

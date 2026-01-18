# 📚 Быстрая справка по проекту

## 🚀 Быстрый запуск

```bash
cd c:\projects\einvestor-laravel
php artisan serve --port=3000
```

Откройте: **http://localhost:3000**

---

## 📁 Важные файлы

- `START_SERVER.bat` - Запуск сервера (Windows)
- `START_SERVER.ps1` - Запуск сервера (PowerShell)
- `PROJECT_STATUS_FINAL.md` - Полный статус проекта
- `.env` - Конфигурация (база данных, настройки)

---

## 🔧 Полезные команды

### Очистка кэша
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### Импорт данных из WordPress
```bash
php artisan import:wordpress --path=../einvestor.ru/wordpress-export
```

### Миграции
```bash
php artisan migrate           # Применить миграции
php artisan migrate:fresh     # Пересоздать БД
php artisan migrate:rollback  # Откатить миграции
```

### Проверка роутов
```bash
php artisan route:list
```

---

## 🌐 URL структура

| URL | Описание |
|-----|----------|
| `/` | Главная страница |
| `/articles` | Список статей |
| `/articles/{slug}` | Статья |
| `/products` | Список товаров |
| `/products/{slug}` | Товар |
| `/category/{slug}` | Категория |
| `/tag/{slug}` | Тег |
| `/{slug}` | Страница |

---

## 📊 Модели и отношения

### Post
- `categories()` - BelongsToMany
- `tags()` - BelongsToMany
- `comments()` - MorphMany
- `featuredImage()` - BelongsTo (Media)

### Product
- `categories()` - BelongsToMany
- `tags()` - BelongsToMany
- `orders()` - BelongsToMany
- `featuredImage()` - BelongsTo (Media)

### Category
- `posts()` - BelongsToMany
- `products()` - BelongsToMany
- `children()` - HasMany
- `parent()` - BelongsTo

---

## 🔄 Редиректы

Все старые URL из WordPress автоматически редиректятся на новые через `RedirectMiddleware`.

Таблица `redirects` содержит 153 записи с 301 редиректами.

---

## ⚠️ Известные ограничения

1. Корзина еще не реализована (ссылка закомментирована в views)
2. Оплата через Робокассу не настроена
3. Личный кабинет не создан
4. Админ-панель не создана
5. Защита файлов не реализована

---

**Последнее обновление:** 18 января 2026

# Команды для деплоя на продакшен

**Сервер:** `adminfeg@narnia`  
**Путь:** `~/einvestor.ru/laravel`  
**PHP:** `php8.3`

## Команды для деплоя (выполнять построчно):

### 1. Подключение к серверу
```bash
ssh adminfeg@narnia
```

### 2. Переход в директорию проекта
```bash
cd ~/einvestor.ru/laravel
```

### 3. Получение последних изменений
```bash
git pull origin main
```

### 4. Установка зависимостей (если нужно)
```bash
php8.3 $(which composer) install --no-dev --optimize-autoloader
```

### 5. Применение миграций (если есть новые)
```bash
php8.3 artisan migrate --force
```

### 6. Создание символической ссылки (если еще не создана)
```bash
php8.3 artisan storage:link
```

### 7. Очистка кеша
```bash
php8.3 artisan optimize:clear
```

### 8. Кеширование конфигурации
```bash
php8.3 artisan config:cache
```

### 9. Кеширование маршрутов
```bash
php8.3 artisan route:cache
```

### 10. Кеширование представлений
```bash
php8.3 artisan view:cache
```

## Дополнительные команды (если нужно):

### Исправление ссылок на изображения в описаниях товаров
```bash
php8.3 artisan products:fix-images-in-description
```

### Исправить внешние ссылки на локальные
```bash
php8.3 artisan links:fix-external
```

## 8. Проверка прав доступа

```bash
chmod -R 755 storage
chmod -R 755 bootstrap/cache
```

## 9. Проверка логов (если есть ошибки)

```bash
tail -f storage/logs/laravel.log
```

## Полный скрипт одной командой:

```bash
ssh adminfeg@narnia "cd ~/einvestor.ru/laravel && git pull origin main && php8.3 \$(which composer) install --no-dev --optimize-autoloader && php8.3 artisan migrate --force && php8.3 artisan storage:link && php8.3 artisan optimize:clear && php8.3 artisan config:cache && php8.3 artisan route:cache && php8.3 artisan view:cache"
```

## Быстрая команда для деплоя (копировать целиком):

```bash
ssh adminfeg@narnia "cd ~/einvestor.ru/laravel && git pull origin main && php8.3 artisan optimize:clear && php8.3 artisan config:cache && php8.3 artisan route:cache && php8.3 artisan view:cache"
```

## После деплоя проверьте:

1. Откройте сайт: `https://einvestor.ru`
2. Попробуйте отредактировать товар
3. Вставьте изображение в описание
4. Сохраните товар
5. Проверьте логи, если ошибка повторится: `tail -f storage/logs/laravel.log`

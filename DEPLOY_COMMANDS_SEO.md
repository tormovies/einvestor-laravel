## Команды для деплоя на продакшн

### 1. Подключение к серверу
ssh adminfeg@narnia.beget.app
cd ~/einvestor.ru/laravel

### 2. Получение изменений
git pull origin main

### 3. Установка зависимостей
php8.3 $(which composer) install --no-dev --optimize-autoloader

### 4. Применение миграций
php8.3 artisan migrate --force

### 5. Очистка кеша
php8.3 artisan config:clear
php8.3 artisan cache:clear
php8.3 artisan route:clear
php8.3 artisan view:clear

### 6. Кеширование для продакшена
php8.3 artisan config:cache
php8.3 artisan route:cache
php8.3 artisan view:cache

# 🚀 Стратегия развертывания на продакшене

**Проект:** EInvestor Laravel  
**Дата:** 18 января 2026

---

## 📋 Цели

1. ✅ Безопасная замена старого WordPress сайта на новый Laravel
2. ✅ Минимальный downtime
3. ✅ Возможность быстрого отката при проблемах
4. ✅ Простое обновление в будущем

---

## 🎯 Стратегия развертывания

### Этап 1: Подготовка (До развертывания)

#### 1.1 Настройка нового сервера/директории

**Вариант A: Отдельная директория (рекомендуется)**

```
/var/www/
├── einvestor.ru/          # Старый WordPress (сохранить)
├── einvestor-laravel/     # Новый Laravel (развернуть сюда)
└── einvestor-backup/      # Резервная копия WordPress
```

**Вариант B: Поддомен для тестирования**

```
/var/www/
├── einvestor.ru/          # Старый WordPress
└── new.einvestor.ru/      # Новый Laravel (тестирование)
```

#### 1.2 Настройка окружения Laravel

**Создать `.env.production`:**

```env
APP_NAME=EInvestor
APP_ENV=production
APP_KEY=base64:... # Генерировать: php artisan key:generate
APP_DEBUG=false
APP_URL=https://einvestor.ru

LOG_CHANNEL=daily
LOG_LEVEL=error

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=einvestor_prod
DB_USERNAME=einvestor_user
DB_PASSWORD=strong_password_here

# Робокасса (продакшн)
ROBOKASSA_MERCHANT_LOGIN=your_production_login
ROBOKASSA_PASSWORD1=your_production_password1
ROBOKASSA_PASSWORD2=your_production_password2
ROBOKASSA_HASH_TYPE=md5
ROBOKASSA_IS_TEST=false

# Email (настроить для уведомлений)
MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=noreply@einvestor.ru
MAIL_PASSWORD=email_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@einvestor.ru
MAIL_FROM_NAME="${APP_NAME}"

SESSION_DRIVER=file
SESSION_LIFETIME=120

CACHE_DRIVER=file
QUEUE_CONNECTION=sync
```

#### 1.3 Миграция базы данных

**Импорт данных из WordPress:**

```bash
# На продакшене
cd /var/www/einvestor-laravel
php artisan import:wordpress /path/to/wordpress-export.json
```

**Или прямой импорт из MySQL:**

```bash
# Экспорт из старой БД
mysqldump -u root -p adminfeg_einvest > wordpress_backup.sql

# Импорт в новую БД (создать скрипт импорта)
php artisan import:wordpress --database=old_mysql
```

#### 1.4 Загрузка файлов товаров

```bash
# Копирование файлов из WordPress в Laravel storage
cp -r /var/www/einvestor.ru/wp-content/uploads/woocommerce_uploads/* \
      /var/www/einvestor-laravel/storage/app/products/
```

---

### Этап 2: Развертывание (Поэтапное)

#### Вариант A: Blue-Green Deployment (рекомендуется)

**Шаг 1:** Развернуть Laravel в отдельной директории
```bash
/var/www/einvestor-laravel/
```

**Шаг 2:** Настроить Nginx для маршрутизации по поддомену или IP
```nginx
server {
    listen 80;
    server_name new.einvestor.ru;
    root /var/www/einvestor-laravel/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
}
```

**Шаг 3:** Протестировать новый сайт на `new.einvestor.ru`

**Шаг 4:** Переключение DNS/роутинг на новый сайт

**Шаг 5:** Старый сайт оставить как backup на поддомене

#### Вариант B: Прямая замена (быстрее, но рискованнее)

**Шаг 1:** Создать резервную копию старого сайта
```bash
# Бэкап
tar -czf einvestor-wordpress-backup-$(date +%Y%m%d).tar.gz \
    /var/www/einvestor.ru/

# Бэкап БД
mysqldump -u root -p adminfeg_einvest > \
    einvestor-db-backup-$(date +%Y%m%d).sql
```

**Шаг 2:** Переименовать старую директорию
```bash
mv /var/www/einvestor.ru /var/www/einvestor-backup
```

**Шаг 3:** Развернуть новый Laravel
```bash
# Скопировать проект
cp -r /path/to/einvestor-laravel /var/www/einvestor.ru

# Установить права
chown -R www-data:www-data /var/www/einvestor.ru
chmod -R 755 /var/www/einvestor.ru
chmod -R 775 /var/www/einvestor.ru/storage
chmod -R 775 /var/www/einvestor.ru/bootstrap/cache
```

**Шаг 4:** Настроить Nginx/Apache для Laravel
```nginx
server {
    listen 80;
    server_name einvestor.ru www.einvestor.ru;
    root /var/www/einvestor.ru/public;
    
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    
    index index.php;
    
    charset utf-8;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

**Шаг 5:** Применить миграции и оптимизировать
```bash
cd /var/www/einvestor.ru
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

---

### Этап 3: Проверка после развертывания

- [ ] Главная страница открывается
- [ ] Все товары отображаются
- [ ] Корзина работает
- [ ] Оформление заказа работает
- [ ] Робокасса настроена (не тестовый режим)
- [ ] 301 редиректы работают (старые URL → новые)
- [ ] Админ-панель доступна
- [ ] Защита файлов работает

---

## 🔄 Стратегия обновлений

### Способ 1: Git-based deployment (рекомендуется)

**Настройка:**

```bash
# На сервере создать bare репозиторий
mkdir -p /var/repos/einvestor.git
cd /var/repos/einvestor.git
git init --bare

# Настроить post-receive hook
cat > hooks/post-receive << 'EOF'
#!/bin/bash
TARGET="/var/www/einvestor.ru"
GIT_DIR="/var/repos/einvestor.git"
BRANCH="main"

while read oldrev newrev refname
do
    if [[ $refname = "refs/heads/$BRANCH" ]];
    then
        echo "Deploying $BRANCH to $TARGET..."
        git --work-tree=$TARGET --git-dir=$GIT_DIR checkout -f $BRANCH
        
        cd $TARGET
        composer install --no-dev --optimize-autoloader
        php artisan migrate --force
        php artisan config:cache
        php artisan route:cache
        php artisan view:cache
        php artisan optimize
        
        echo "Deployment complete!"
    fi
done
EOF

chmod +x hooks/post-receive
```

**Локальная машина:**

```bash
# Добавить remote
git remote add production ssh://user@einvestor.ru/var/repos/einvestor.git

# Деплой
git push production main
```

---

### Способ 2: Скрипт обновления (проще для начинающих)

**Создать `deploy.sh`:**

```bash
#!/bin/bash
# deploy.sh - Скрипт обновления Laravel на продакшене

set -e

PROJECT_DIR="/var/www/einvestor.ru"
BACKUP_DIR="/var/backups/einvestor"

echo "🚀 Начало обновления..."

# 1. Бэкап базы данных
echo "📦 Создание бэкапа БД..."
mysqldump -u root -p einvestor_prod > \
    $BACKUP_DIR/db-backup-$(date +%Y%m%d-%H%M%S).sql

# 2. Бэкап директории (опционально, если изменяются файлы)
echo "📦 Создание бэкапа файлов..."
tar -czf $BACKUP_DIR/files-backup-$(date +%Y%m%d-%H%M%S).tar.gz \
    $PROJECT_DIR/storage/app/products/

# 3. Перейти в директорию проекта
cd $PROJECT_DIR

# 4. Обновить код из Git
echo "📥 Обновление кода..."
git pull origin main

# 5. Обновить зависимости
echo "📦 Обновление зависимостей..."
composer install --no-dev --optimize-autoloader

# 6. Применить миграции
echo "🗄️ Применение миграций..."
php artisan migrate --force

# 7. Очистить и пересоздать кеш
echo "🧹 Очистка кеша..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# 8. Оптимизация
echo "⚡ Оптимизация..."
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# 9. Права доступа
echo "🔐 Установка прав доступа..."
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache

echo "✅ Обновление завершено!"
```

**Использование:**

```bash
# Загрузить на сервер
scp deploy.sh user@einvestor.ru:/var/www/einvestor.ru/

# На сервере
chmod +x deploy.sh
./deploy.sh
```

---

### Способ 3: CI/CD (GitHub Actions / GitLab CI)

**Пример `.github/workflows/deploy.yml`:**

```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v3
    
    - name: Deploy to server
      uses: appleboy/ssh-action@master
      with:
        host: ${{ secrets.PROD_HOST }}
        username: ${{ secrets.PROD_USER }}
        key: ${{ secrets.PROD_SSH_KEY }}
        script: |
          cd /var/www/einvestor.ru
          git pull origin main
          composer install --no-dev --optimize-autoloader
          php artisan migrate --force
          php artisan config:cache
          php artisan route:cache
          php artisan view:cache
          php artisan optimize
```

---

## 📦 Структура для простых обновлений

### Рекомендуемая структура на сервере:

```
/var/www/einvestor.ru/
├── .env                    # Конфигурация (НЕ коммитить в Git!)
├── .env.example            # Шаблон конфигурации
├── storage/                # Файлы (товары, логи)
│   ├── app/
│   │   └── products/       # Файлы товаров
│   └── logs/
├── bootstrap/cache/        # Кеш Bootstrap
├── deploy.sh               # Скрипт обновления
└── [остальные файлы Laravel]
```

---

## 🔒 Безопасность

### После развертывания:

1. **Убедиться, что `.env` не в Git:**
   ```bash
   echo ".env" >> .gitignore
   ```

2. **Настроить права доступа:**
   ```bash
   chmod 600 .env
   chown www-data:www-data .env
   ```

3. **Отключить debug в продакшене:**
   ```env
   APP_DEBUG=false
   APP_ENV=production
   ```

4. **Настроить HTTPS:**
   ```nginx
   # Перенаправление HTTP → HTTPS
   server {
       listen 80;
       server_name einvestor.ru www.einvestor.ru;
       return 301 https://$server_name$request_uri;
   }
   
   server {
       listen 443 ssl http2;
       server_name einvestor.ru www.einvestor.ru;
       
       ssl_certificate /path/to/cert.pem;
       ssl_certificate_key /path/to/key.pem;
       
       # ... остальная конфигурация
   }
   ```

---

## 🔄 Откат при проблемах

### Быстрый откат (если старый сайт сохранен):

```bash
# 1. Переключить Nginx обратно на старый сайт
# В /etc/nginx/sites-available/einvestor.ru
root /var/www/einvestor-backup;

# 2. Перезагрузить Nginx
systemctl reload nginx

# 3. Откатить БД (если нужно)
mysql -u root -p einvestor_prod < /var/backups/einvestor/db-backup-TIMESTAMP.sql
```

### Откат через Git:

```bash
cd /var/www/einvestor.ru
git log --oneline  # Найти предыдущий коммит
git checkout <previous-commit-hash>
php artisan migrate:rollback --step=1  # Откатить последнюю миграцию
php artisan config:cache
php artisan route:cache
```

---

## 📝 Чеклист развертывания

### Перед развертыванием:
- [ ] Резервная копия старого сайта создана
- [ ] Резервная копия БД создана
- [ ] `.env.production` настроен
- [ ] Тестовый запуск на поддомене/тестовом сервере
- [ ] Все миграции применены
- [ ] Файлы товаров скопированы
- [ ] Робокасса настроена (продакшн)

### Во время развертывания:
- [ ] Nginx/Apache настроен
- [ ] Права доступа установлены
- [ ] Кеш очищен и пересоздан
- [ ] Оптимизация выполнена

### После развертывания:
- [ ] Главная страница работает
- [ ] Товары отображаются
- [ ] Корзина работает
- [ ] Оформление заказа работает
- [ ] Админ-панель доступна
- [ ] HTTPS настроен
- [ ] Мониторинг настроен (логи, алерты)

---

## 🎯 Рекомендации

1. **Используйте Git для версионирования** - самый простой способ обновлений
2. **Создавайте бэкапы перед обновлениями** - автоматизировать через cron
3. **Тестируйте обновления на staging** - копия продакшена для тестов
4. **Мониторинг логов** - `tail -f storage/logs/laravel.log`
5. **Автоматизация бэкапов** - ежедневные бэкапы БД

---

**Следующий шаг:** Выбрать стратегию развертывания и подготовить скрипты для вашего окружения.

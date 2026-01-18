# 🚀 Настройка нового сервера для Laravel

**Стратегия:** Новый Laravel на новом сервере, старый WordPress остается на старом сервере как бэкап.

---

## ✅ Преимущества этого подхода

- ✅ Старый сайт продолжает работать (нет downtime)
- ✅ Можно тестировать новый сайт без риска
- ✅ Старый сайт = автоматический бэкап
- ✅ Можно переключить DNS когда будете готовы
- ✅ Легкий откат (просто вернуть DNS на старый сервер)

---

## 📋 Пошаговая инструкция

### Этап 1: Подготовка нового сервера

#### 1.1 Требования к серверу

**Минимальные требования:**
- PHP 8.2 или выше
- Composer (менеджер зависимостей PHP)
- MySQL 5.7+ или MariaDB 10.3+
- Nginx или Apache с mod_rewrite
- Git
- OpenSSL PHP Extension
- PDO PHP Extension
- Mbstring PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- Ctype PHP Extension
- JSON PHP Extension

**Рекомендуется:**
- 2GB RAM минимум
- 20GB дискового пространства
- SSL сертификат (Let's Encrypt бесплатный)

---

### Этап 2: Установка Laravel на новый сервер

#### 2.1 Клонирование репозитория

```bash
# На новом сервере
cd /var/www
git clone https://github.com/tormovies/einvestor-laravel.git einvestor.ru

# Или через SSH (если настроен):
# git clone git@github.com:tormovies/einvestor-laravel.git einvestor.ru
```

#### 2.2 Установка зависимостей

```bash
cd /var/www/einvestor.ru

# Установить зависимости Composer
composer install --no-dev --optimize-autoloader

# Проверить, что Composer установлен (если нет):
# curl -sS https://getcomposer.org/installer | php
# sudo mv composer.phar /usr/local/bin/composer
```

#### 2.3 Настройка окружения

```bash
# Скопировать .env.example в .env
cp .env.example .env

# Сгенерировать APP_KEY
php artisan key:generate

# Отредактировать .env (см. ниже)
nano .env
```

**Настройка `.env` для продакшена:**

```env
APP_NAME=EInvestor
APP_ENV=production
APP_KEY=base64:... # Сгенерируется автоматически
APP_DEBUG=false
APP_URL=https://einvestor.ru

LOG_CHANNEL=daily
LOG_LEVEL=error

# База данных (создать новую БД на новом сервере)
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=einvestor_prod
DB_USERNAME=einvestor_user
DB_PASSWORD=strong_password_here

# Робокасса (продакшн данные!)
ROBOKASSA_MERCHANT_LOGIN=your_production_login
ROBOKASSA_PASSWORD1=your_production_password1
ROBOKASSA_PASSWORD2=your_production_password2
ROBOKASSA_HASH_TYPE=md5
ROBOKASSA_IS_TEST=false

# Email (настроить)
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

---

### Этап 3: Настройка базы данных

#### 3.1 Создание базы данных

```bash
# Войти в MySQL
mysql -u root -p

# Создать базу данных
CREATE DATABASE einvestor_prod CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# Создать пользователя
CREATE USER 'einvestor_user'@'localhost' IDENTIFIED BY 'strong_password_here';

# Дать права
GRANT ALL PRIVILEGES ON einvestor_prod.* TO 'einvestor_user'@'localhost';
FLUSH PRIVILEGES;

# Выйти
EXIT;
```

#### 3.2 Применение миграций

```bash
cd /var/www/einvestor.ru

# Применить миграции
php artisan migrate --force

# Или с бэкапом:
php artisan migrate --force --pretend  # Сначала проверить
php artisan migrate --force            # Применить
```

#### 3.3 Импорт данных из WordPress

**Вариант A: Импорт из JSON (если экспортировали)**

```bash
# Загрузить JSON файл на сервер
scp wordpress-export.json user@new-server:/tmp/

# На сервере
php artisan import:wordpress /tmp/wordpress-export.json
```

**Вариант B: Прямой импорт из старой БД**

```bash
# На старом сервере - экспорт БД
mysqldump -u root -p adminfeg_einvest > einvestor-export.sql

# Переместить файл на новый сервер
scp einvestor-export.sql user@new-server:/tmp/

# На новом сервере - создать скрипт для парсинга SQL и импорта
# (или использовать команду импорта с параметром --database)
```

---

### Этап 4: Копирование файлов товаров

#### 4.1 Создать папку для товаров

```bash
# На новом сервере
mkdir -p /var/www/einvestor.ru/storage/app/products
chown -R www-data:www-data /var/www/einvestor.ru/storage/app/products
chmod -R 775 /var/www/einvestor.ru/storage/app/products
```

#### 4.2 Копирование файлов со старого сервера

**Через SCP (рекомендуется):**

```bash
# На вашем компьютере (или на старом сервере)
scp -r old-server:/var/www/einvestor.ru/wp-content/uploads/woocommerce_uploads/* \
      new-server:/var/www/einvestor.ru/storage/app/products/
```

**Или через rsync (если установлен):**

```bash
rsync -avz old-server:/var/www/einvestor.ru/wp-content/uploads/woocommerce_uploads/ \
            new-server:/var/www/einvestor.ru/storage/app/products/
```

---

### Этап 5: Настройка веб-сервера

#### 5.1 Настройка Nginx

**Создать конфигурацию `/etc/nginx/sites-available/einvestor.ru`:**

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name einvestor.ru www.einvestor.ru;
    
    # Перенаправление на HTTPS
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name einvestor.ru www.einvestor.ru;
    
    root /var/www/einvestor.ru/public;
    index index.php;
    
    # SSL сертификаты (Let's Encrypt)
    ssl_certificate /etc/letsencrypt/live/einvestor.ru/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/einvestor.ru/privkey.pem;
    
    # SSL настройки
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers on;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512;
    
    # Безопасность
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    
    charset utf-8;
    
    # Laravel routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }
    
    error_page 404 /index.php;
    
    # PHP обработка
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    
    # Запретить доступ к скрытым файлам
    location ~ /\.(?!well-known).* {
        deny all;
    }
    
    # Защита файлов из storage
    location ~ ^/storage/ {
        deny all;
    }
    
    # Максимальный размер загружаемых файлов
    client_max_body_size 20M;
}
```

**Активировать конфигурацию:**

```bash
# Создать символическую ссылку
sudo ln -s /etc/nginx/sites-available/einvestor.ru /etc/nginx/sites-enabled/

# Проверить конфигурацию
sudo nginx -t

# Перезагрузить Nginx
sudo systemctl reload nginx
```

#### 5.2 Настройка Apache (если используется Apache)

**Создать виртуальный хост `/etc/apache2/sites-available/einvestor.ru.conf`:**

```apache
<VirtualHost *:80>
    ServerName einvestor.ru
    ServerAlias www.einvestor.ru
    
    Redirect permanent / https://einvestor.ru/
</VirtualHost>

<VirtualHost *:443>
    ServerName einvestor.ru
    ServerAlias www.einvestor.ru
    DocumentRoot /var/www/einvestor.ru/public
    
    SSLEngine on
    SSLCertificateFile /etc/letsencrypt/live/einvestor.ru/fullchain.pem
    SSLCertificateKeyFile /etc/letsencrypt/live/einvestor.ru/privkey.pem
    
    <Directory /var/www/einvestor.ru/public>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/einvestor-error.log
    CustomLog ${APACHE_LOG_DIR}/einvestor-access.log combined
</VirtualHost>
```

**Активировать:**

```bash
sudo a2ensite einvestor.ru.conf
sudo a2enmod rewrite ssl
sudo systemctl reload apache2
```

---

### Этап 6: Установка SSL сертификата

**Использовать Let's Encrypt (бесплатно):**

```bash
# Установить Certbot
sudo apt-get update
sudo apt-get install certbot python3-certbot-nginx

# Для Nginx:
sudo certbot --nginx -d einvestor.ru -d www.einvestor.ru

# Для Apache:
# sudo certbot --apache -d einvestor.ru -d www.einvestor.ru

# Автоматическое обновление (уже настроено)
sudo certbot renew --dry-run
```

---

### Этап 7: Права доступа

```bash
cd /var/www/einvestor.ru

# Установить владельца
sudo chown -R www-data:www-data .

# Установить права
sudo find . -type f -exec chmod 644 {} \;
sudo find . -type d -exec chmod 755 {} \;

# Особые права для storage и cache
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

---

### Этап 8: Оптимизация Laravel

```bash
cd /var/www/einvestor.ru

# Очистить кеш
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Создать кеш для продакшена
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize

# Оптимизировать автозагрузку Composer
composer dump-autoload --optimize --no-dev
```

---

### Этап 9: Тестирование нового сайта

**Проверить доступность по IP или временному домену:**

```bash
# Если есть временный домен или IP:
# http://NEW_SERVER_IP или http://new.einvestor.ru

# Проверить логи при ошибках:
tail -f /var/www/einvestor.ru/storage/logs/laravel.log
```

**Проверить:**
- [ ] Главная страница открывается
- [ ] Товары отображаются
- [ ] Корзина работает
- [ ] Оформление заказа работает
- [ ] Админ-панель доступна
- [ ] HTTPS работает (SSL сертификат)

---

### Этап 10: Переключение DNS

**Когда новый сайт полностью протестирован:**

1. **Войти в панель управления DNS** (где настраивали домен)

2. **Изменить A-запись:**
   ```
   Старая A-запись: einvestor.ru → OLD_SERVER_IP
   Новая A-запись:  einvestor.ru → NEW_SERVER_IP
   ```

3. **Ждать распространения DNS (1-48 часов, обычно 5-30 минут)**

4. **Проверить:**
   ```bash
   # Проверить текущий IP домена
   nslookup einvestor.ru
   # Должен показать новый IP сервера
   ```

5. **Старый сайт оставить как есть** (бэкап, на случай отката)

---

## 🔒 Безопасность на новом сервере

### После развертывания:

```bash
# 1. Убедиться, что .env защищен
chmod 600 /var/www/einvestor.ru/.env
chown www-data:www-data /var/www/einvestor.ru/.env

# 2. Настроить firewall (если не настроен)
sudo ufw allow 80/tcp
sudo ufw allow 443/tcp
sudo ufw allow 22/tcp  # SSH
sudo ufw enable

# 3. Регулярные обновления
sudo apt-get update && sudo apt-get upgrade
```

---

## 🔄 Обновления на новом сервере

**Использовать Git для обновлений:**

```bash
cd /var/www/einvestor.ru

# Обновить код
git pull origin main

# Обновить зависимости (если composer.json изменился)
composer install --no-dev --optimize-autoloader

# Применить миграции (если есть новые)
php artisan migrate --force

# Очистить и пересоздать кеш
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan optimize
```

**Или использовать скрипт `deploy.sh` из `DEPLOYMENT_STRATEGY.md`**

---

## 📝 Чеклист настройки нового сервера

### Подготовка:
- [ ] Сервер настроен (PHP, MySQL, Nginx/Apache)
- [ ] Репозиторий клонирован
- [ ] Зависимости установлены (composer install)

### База данных:
- [ ] MySQL база данных создана
- [ ] Пользователь БД создан с правами
- [ ] Миграции применены
- [ ] Данные импортированы из WordPress

### Файлы:
- [ ] Файлы товаров скопированы в `storage/app/products/`
- [ ] Права доступа установлены (775 для storage)

### Конфигурация:
- [ ] `.env` настроен (продакшн данные)
- [ ] `APP_KEY` сгенерирован
- [ ] Робокасса настроена (продакшн режим)
- [ ] Nginx/Apache настроен
- [ ] SSL сертификат установлен

### Тестирование:
- [ ] Сайт открывается по IP/временному домену
- [ ] Все функции работают
- [ ] Админ-панель доступна
- [ ] HTTPS работает

### Переключение:
- [ ] DNS изменен на новый сервер
- [ ] Старый сайт сохранен (бэкап)
- [ ] Мониторинг настроен

---

## ⚠️ Важные замечания

1. **Старый сервер не трогать** - оставить как есть для бэкапа
2. **Тестировать на новом сервере** перед переключением DNS
3. **DNS может распространяться до 48 часов** - быть терпеливым
4. **Мониторить логи** после переключения: `tail -f storage/logs/laravel.log`
5. **Иметь план отката** - если что-то пойдет не так, вернуть DNS на старый сервер

---

## 🔄 Откат при проблемах

**Если после переключения DNS что-то пошло не так:**

1. **Вернуть DNS на старый сервер:**
   ```
   A-запись: einvestor.ru → OLD_SERVER_IP
   ```

2. **Новый сервер оставить как есть** (можно исправить проблемы)

3. **После исправлений** - повторить переключение DNS

---

**Стратегия "новый сервер" - самый безопасный способ миграции! ✅**

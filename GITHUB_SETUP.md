# 📦 Настройка GitHub для проекта EInvestor Laravel

**Важно:** Файлы товаров и конфигурация НЕ должны попадать в Git!

---

## ✅ Шаг 1: Обновить .gitignore

Убедитесь, что в `.gitignore` есть следующие правила:

```gitignore
# Файлы конфигурации (содержат пароли и ключи!)
.env
.env.backup
.env.production
.env.local

# Файлы товаров (не должны быть в Git!)
/storage/app/products/
/storage/app/products/*
!storage/app/products/.gitkeep

# Остальные файлы Laravel
/storage/logs/
/storage/framework/cache/
/storage/framework/sessions/
/storage/framework/views/

# База данных (если используете SQLite локально)
/database/*.sqlite
/database/*.sqlite-journal

# Загруженные файлы
/public/storage

# Кеш и временные файлы
/bootstrap/cache/*
!/bootstrap/cache/.gitignore

# Vendor (зависимости)
/vendor/

# IDE
/.idea
/.vscode
/.nova

# Документация (опционально - можно коммитить)
# *.md
```

---

## 🔧 Шаг 2: Создать файл .gitkeep для папки товаров

Чтобы папка `storage/app/products/` существовала, но игнорировала содержимое:

```bash
# Создать .gitkeep файл (если папки нет)
touch storage/app/products/.gitkeep
```

Или через PowerShell:
```powershell
New-Item -ItemType File -Path storage\app\products\.gitkeep -Force
```

---

## 📤 Шаг 3: Инициализация Git (если еще не сделано)

```bash
cd c:\projects\einvestor-laravel

# Инициализировать репозиторий
git init

# Добавить все файлы (кроме игнорируемых)
git add .

# Проверить, что файлы товаров не добавлены
git status
# Не должно быть файлов из storage/app/products/ (кроме .gitkeep)

# Создать первый коммит
git commit -m "Initial commit: EInvestor Laravel project"
```

---

## 🔍 Шаг 4: Проверить перед коммитом

**Важно проверить, что НЕ попадут в Git:**

```bash
# Проверить статус
git status

# Проверить, что .env не добавлен
git status | grep .env
# Ничего не должно быть

# Проверить, что файлы товаров не добавлены
git status | grep products
# Только .gitkeep должен быть

# Посмотреть, что будет добавлено
git diff --cached --name-only
```

---

## 🚀 Шаг 5: Создать репозиторий на GitHub

1. **Зайти на GitHub** и создать новый репозиторий:
   - Название: `einvestor-laravel` (или любое другое)
   - **НЕ** создавать README, .gitignore, лицензию (они уже есть)

2. **Добавить remote и отправить:**

```bash
# Добавить remote (замените YOUR_USERNAME на ваш GitHub username)
git remote add origin https://github.com/YOUR_USERNAME/einvestor-laravel.git

# Или через SSH:
# git remote add origin git@github.com:YOUR_USERNAME/einvestor-laravel.git

# Переименовать ветку в main (если нужно)
git branch -M main

# Отправить код на GitHub
git push -u origin main
```

---

## ⚠️ Что НЕ должно попасть в GitHub:

### ❌ НЕ коммитить:
- `.env` файлы (содержат пароли, ключи API)
- `storage/app/products/*` (файлы товаров - могут быть большими)
- `storage/logs/*` (логи)
- `database/*.sqlite` (локальная БД, если есть)
- `vendor/` (зависимости - уже в .gitignore)

### ✅ МОЖНО коммитить:
- Код приложения (controllers, models, views)
- Миграции БД
- Конфигурационные файлы (без паролей)
- `.env.example` (шаблон конфигурации)
- Документацию (*.md)
- `composer.json`, `package.json` и т.д.

---

## 🛡️ Безопасность: Проверка перед push

**Перед первым push обязательно проверьте:**

```bash
# 1. Посмотреть все файлы, которые будут отправлены
git ls-files

# 2. Проверить, что .env НЕ в списке
git ls-files | grep .env

# 3. Проверить, что файлы товаров НЕ в списке
git ls-files | grep "storage/app/products"

# 4. Посмотреть размер того, что будет отправлено
git count-objects -vH
```

---

## 📝 Если уже добавили файлы по ошибке

**Если случайно добавили `.env` или файлы товаров:**

```bash
# Удалить из индекса (но оставить на диске)
git rm --cached .env
git rm --cached -r storage/app/products/*

# Убедиться, что они в .gitignore
# (должны быть уже)

# Перекоммитить
git commit -m "Remove .env and product files from Git"

# Отправить
git push
```

---

## 🔄 Структура .gitignore для проекта

**Создать/обновить `.gitignore` в корне проекта:**

```gitignore
# Laravel стандартные исключения
*.log
.DS_Store
.env
.env.backup
.env.production
.env.local
.phpactor.json
.phpunit.result.cache
/.fleet
/.idea
/.nova
/.phpunit.cache
/.vscode
/.zed
/auth.json
/node_modules
/public/build
/public/hot
/public/storage
/storage/*.key
/storage/pail
/vendor
Homestead.json
Homestead.yaml
Thumbs.db

# Ваши кастомные исключения
# Файлы товаров (важно!)
/storage/app/products/*
!storage/app/products/.gitkeep

# Локальная БД SQLite (если используете)
/database/*.sqlite
/database/*.sqlite-journal

# Логи
/storage/logs/*
!storage/logs/.gitignore

# Кеш
/bootstrap/cache/*
!/bootstrap/cache/.gitignore
```

---

## ✅ Итоговый чеклист перед push на GitHub

- [ ] `.env` добавлен в `.gitignore` и НЕ в Git
- [ ] `storage/app/products/` добавлена в `.gitignore`
- [ ] Файлы товаров НЕ показываются в `git status`
- [ ] `.env.example` создан (шаблон конфигурации)
- [ ] Проверили `git ls-files` - нет секретных файлов
- [ ] Первый коммит создан
- [ ] Remote добавлен
- [ ] Готовы к `git push`

---

## 📚 Создать .env.example для команды

**Создать `.env.example` (можно коммитить):**

```env
APP_NAME=EInvestor
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:3000

DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/database.sqlite

ROBOKASSA_MERCHANT_LOGIN=
ROBOKASSA_PASSWORD1=
ROBOKASSA_PASSWORD2=
ROBOKASSA_HASH_TYPE=md5
ROBOKASSA_IS_TEST=true
```

Этот файл можно безопасно коммитить в Git - он служит шаблоном для `.env`.

---

**После выполнения этих шагов проект можно безопасно загружать на GitHub! 🚀**

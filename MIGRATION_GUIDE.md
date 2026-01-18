# Пошаговая инструкция по миграции данных

## Шаг 1: Создание базы данных

Откройте MySQL и выполните:

```sql
CREATE DATABASE einvestor_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Или через командную строку:
```bash
mysql -u root -p -e "CREATE DATABASE einvestor_laravel CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

## Шаг 2: Настройка .env файла

Откройте файл `c:\projects\einvestor-laravel\.env` и настройте подключение к БД:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=einvestor_laravel
DB_USERNAME=root
DB_PASSWORD=fRAxngtck8um
```

## Шаг 3: Запуск миграций

Откройте терминал в папке проекта и выполните:

```bash
cd c:\projects\einvestor-laravel
php artisan migrate
```

Это создаст все таблицы в базе данных.

## Шаг 4: Импорт данных из WordPress

После успешного выполнения миграций, импортируйте данные:

```bash
php artisan import:wordpress --path=../einvestor.ru/wordpress-export
```

Или если папка wordpress-export находится в другом месте:

```bash
php artisan import:wordpress --path=C:\projects\einvestor.ru\wordpress-export
```

## Шаг 5: Проверка результата

Проверьте, что данные импортированы:

```bash
# Проверка постов
php artisan tinker
>>> App\Models\Post::count()
>>> App\Models\Product::count()
>>> App\Models\Redirect::count()
```

## Что будет импортировано

- ✅ 38 постов → таблица `posts`
- ✅ 17 страниц → таблица `pages`
- ✅ 14 товаров → таблица `products`
- ✅ 23 категории → таблица `categories`
- ✅ 58 тегов → таблица `tags`
- ✅ 5 категорий товаров → таблица `categories` (type='product')
- ✅ 158 медиафайлов → таблица `media`
- ✅ Все редиректы → таблица `redirects` (старые URL → новые URL)

## Структура новых URL

После импорта все старые URL будут автоматически редиректиться:

- Старый: `/idei-na-fondovom-rynke-ot-20-05-2014.html`
- Новый: `/articles/idei-na-fondovom-rynke-ot-20-05-2014`

- Старый: `/product/my-product/`
- Новый: `/products/my-product`

## Возможные проблемы

### Ошибка подключения к БД
- Проверьте настройки в `.env`
- Убедитесь, что MySQL запущен
- Проверьте права доступа пользователя

### Ошибка при импорте
- Убедитесь, что папка `wordpress-export` существует
- Проверьте, что JSON файлы не повреждены
- Проверьте логи: `storage/logs/laravel.log`

### Foreign key constraints
Если возникнут ошибки с foreign keys, можно временно отключить проверку в миграциях.

## После миграции

1. ✅ Данные импортированы
2. ✅ Редиректы настроены
3. ⏳ Нужно создать views (Blade шаблоны)
4. ⏳ Настроить платежи (Робокасса)
5. ⏳ Создать корзину и заказы

## Следующие команды

После успешной миграции можно запустить сервер:

```bash
php artisan serve
```

Сайт будет доступен на http://localhost:8000

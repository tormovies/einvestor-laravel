@echo off
cd /d "%~dp0"
echo Запуск Laravel сервера на порту 8000...
echo Если порт 8000 занят, будет использован свободный порт
php artisan serve --port=8000
pause

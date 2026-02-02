@echo off
cd /d "%~dp0\public"
echo Запуск Laravel сервера напрямую через PHP...
echo URL: http://localhost:8000
echo Для остановки нажмите Ctrl+C
echo.
php -S 127.0.0.1:8000

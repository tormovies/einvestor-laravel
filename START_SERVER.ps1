# Скрипт для запуска Laravel сервера на порту 3000

Write-Host "Проверка порта 3000..." -ForegroundColor Yellow

# Проверяем, занят ли порт 3000
$portCheck = netstat -ano | findstr ":3000"
if ($portCheck) {
    Write-Host "Порт 3000 занят. Освобождаю..." -ForegroundColor Yellow
    $connections = netstat -ano | findstr ":3000"
    $pids = $connections | ForEach-Object { 
        $parts = $_ -split '\s+'
        $parts[-1]
    } | Select-Object -Unique
    
    foreach ($pid in $pids) {
        if ($pid -match '^\d+$') {
            try {
                Stop-Process -Id $pid -Force -ErrorAction SilentlyContinue
                Write-Host "Процесс $pid остановлен" -ForegroundColor Green
            } catch {
                Write-Host "Не удалось остановить процесс $pid" -ForegroundColor Red
            }
        }
    }
    Start-Sleep -Seconds 2
}

Write-Host "Запуск Laravel сервера на порту 3000..." -ForegroundColor Green
Write-Host "URL: http://localhost:3000" -ForegroundColor Cyan
Write-Host "Для остановки нажмите Ctrl+C" -ForegroundColor Yellow
Write-Host ""

cd $PSScriptRoot
php artisan serve --port=3000

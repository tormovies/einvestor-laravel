<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Отладочная информация - Робокасса</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 1200px;
            margin: 40px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .debug-container {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #e74c3c;
            margin-top: 0;
        }
        .debug-section {
            margin: 20px 0;
            padding: 15px;
            background: #f9f9f9;
            border-left: 4px solid #3498db;
            border-radius: 4px;
        }
        .debug-section h2 {
            margin-top: 0;
            color: #2c3e50;
            font-size: 18px;
        }
        pre {
            background: #2c3e50;
            color: #ecf0f1;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            font-size: 14px;
            line-height: 1.5;
        }
        .param-item {
            margin: 10px 0;
            padding: 8px;
            background: white;
            border-radius: 4px;
        }
        .param-key {
            font-weight: bold;
            color: #2980b9;
        }
        .param-value {
            color: #27ae60;
            word-break: break-all;
        }
        .warning {
            background: #fff3cd;
            border-left-color: #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <div class="debug-container">
        <h1>🔍 Отладочная информация - Робокасса</h1>
        
        <div class="warning">
            <strong>⚠️ Внимание:</strong> Это отладочная страница. CSRF проверка была пропущена для запроса от Робокассы.
        </div>

        @if(!isset($debug))
            <div class="warning">
                <strong>Ошибка:</strong> Отладочная информация недоступна.
            </div>
        @else
        <div class="debug-section">
            <h2>Основная информация</h2>
            <div class="param-item">
                <span class="param-key">Путь:</span> 
                <span class="param-value">{{ $debug['path'] ?? 'не указан' }}</span>
            </div>
            <div class="param-item">
                <span class="param-key">Метод:</span> 
                <span class="param-value">{{ $debug['method'] }}</span>
            </div>
            <div class="param-item">
                <span class="param-key">Полный URL:</span> 
                <span class="param-value">{{ $debug['full_url'] ?? $debug['url'] ?? 'не указан' }}</span>
            </div>
            <div class="param-item">
                <span class="param-key">IP адрес:</span> 
                <span class="param-value">{{ $debug['ip'] ?? 'не указан' }}</span>
            </div>
            <div class="param-item">
                <span class="param-key">User-Agent:</span> 
                <span class="param-value">{{ $debug['user_agent'] ?? 'не указан' }}</span>
            </div>
        </div>

        <div class="debug-section">
            <h2>GET параметры (Query String)</h2>
            @if(!empty($debug['query_params']))
                <pre>{{ json_encode($debug['query_params'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            @else
                <p>Нет GET параметров</p>
            @endif
        </div>

        <div class="debug-section">
            <h2>POST параметры</h2>
            @if(!empty($debug['post_params']))
                <pre>{{ json_encode($debug['post_params'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            @else
                <p>Нет POST параметров</p>
            @endif
        </div>

        <div class="debug-section">
            <h2>Все параметры (GET + POST)</h2>
            @if(!empty($debug['all_params']))
                <pre>{{ json_encode($debug['all_params'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
            @else
                <p>Нет параметров</p>
            @endif
        </div>

        <div class="debug-section">
            <h2>Заголовки запроса</h2>
            <pre>{{ json_encode($debug['headers'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
        </div>

        <div class="debug-section">
            <h2>Информация о сессии</h2>
            <div class="param-item">
                <span class="param-key">Сессия существует:</span> 
                <span class="param-value">{{ $debug['has_session'] ? 'Да' : 'Нет' }}</span>
            </div>
            @if($debug['has_session'])
                <div class="param-item">
                    <span class="param-key">ID сессии:</span> 
                    <span class="param-value">{{ $debug['session_id'] }}</span>
                </div>
            @endif
            <div class="param-item">
                <span class="param-key">CSRF токен:</span> 
                <span class="param-value">{{ $debug['csrf_token'] }}</span>
            </div>
            <div class="param-item">
                <span class="param-key">X-CSRF-TOKEN заголовок:</span> 
                <span class="param-value">{{ $debug['x_csrf_token_header'] ?? 'отсутствует' }}</span>
            </div>
            <div class="param-item">
                <span class="param-key">X-XSRF-TOKEN заголовок:</span> 
                <span class="param-value">{{ $debug['x_xsrf_token_header'] ?? 'отсутствует' }}</span>
            </div>
        </div>
        @endif
    </div>
</body>
</html>

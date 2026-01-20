<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Добро пожаловать на eInvestor.ru</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f9fafb; padding: 30px; border-radius: 8px;">
        <h1 style="color: #1f2937; margin-bottom: 20px;">Добро пожаловать на eInvestor.ru!</h1>
        
        <p style="margin-bottom: 20px;">
            Здравствуйте, {{ $user->name }}!
        </p>
        
        <p style="margin-bottom: 20px;">
            Для вас был создан аккаунт на сайте eInvestor.ru. Вы можете использовать следующие данные для входа:
        </p>
        
        <div style="background: #ffffff; border: 2px solid #e5e7eb; border-radius: 6px; padding: 20px; margin: 20px 0;">
            <p style="margin: 0 0 10px 0;"><strong>Email:</strong> {{ $user->email }}</p>
            <p style="margin: 0;"><strong>Пароль:</strong> <code style="background: #f3f4f6; padding: 4px 8px; border-radius: 4px; font-size: 14px;">{{ $password }}</code></p>
        </div>
        
        <p style="margin-bottom: 20px;">
            Вы можете войти в личный кабинет на странице: <a href="{{ route('login') }}" style="color: #2563eb;">{{ route('login') }}</a>
        </p>
        
        <div style="background: #fef3c7; border-left: 4px solid #f59e0b; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0; color: #92400e;">
                <strong>⚠️ Важно:</strong> Сохраните этот пароль в надежном месте. Для безопасности рекомендуем изменить пароль после первого входа.
            </p>
        </div>
        
        <p style="margin-top: 30px; color: #6b7280; font-size: 14px;">
            Если у вас возникнут вопросы, свяжитесь с нашей службой поддержки.
        </p>
        
        <p style="margin-top: 20px; color: #6b7280; font-size: 14px;">
            С уважением,<br>
            Команда eInvestor.ru
        </p>
    </div>
</body>
</html>

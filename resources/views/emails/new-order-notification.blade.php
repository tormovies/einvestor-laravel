<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новый заказ #{{ $order->number }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f9fafb; padding: 30px; border-radius: 8px;">
        <h1 style="color: #1f2937; margin-bottom: 20px;">Новый заказ на eInvestor.ru</h1>
        
        <div style="background: #ffffff; border: 2px solid #e5e7eb; border-radius: 6px; padding: 20px; margin: 20px 0;">
            <h2 style="color: #2563eb; margin-top: 0;">Заказ #{{ $order->number }}</h2>
            
            <p style="margin: 10px 0;"><strong>Дата создания:</strong> {{ $order->created_at->format('d.m.Y H:i') }}</p>
            <p style="margin: 10px 0;"><strong>Сумма:</strong> {{ number_format($order->total, 0, ',', ' ') }} ₽</p>
            <p style="margin: 10px 0;"><strong>Статус:</strong> {{ $order->status }}</p>
            <p style="margin: 10px 0;"><strong>Статус оплаты:</strong> {{ $order->payment_status }}</p>
        </div>
        
        <div style="background: #ffffff; border: 2px solid #e5e7eb; border-radius: 6px; padding: 20px; margin: 20px 0;">
            <h3 style="color: #1f2937; margin-top: 0;">Данные покупателя</h3>
            <p style="margin: 10px 0;"><strong>Имя:</strong> {{ $order->name ?? 'Не указано' }}</p>
            <p style="margin: 10px 0;"><strong>Email:</strong> {{ $order->email }}</p>
            @if($order->phone)
            <p style="margin: 10px 0;"><strong>Телефон:</strong> {{ $order->phone }}</p>
            @endif
        </div>
        
        @if($order->items->count() > 0)
        <div style="background: #ffffff; border: 2px solid #e5e7eb; border-radius: 6px; padding: 20px; margin: 20px 0;">
            <h3 style="color: #1f2937; margin-top: 0;">Товары в заказе</h3>
            <table style="width: 100%; border-collapse: collapse; margin-top: 15px;">
                <thead>
                    <tr style="border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                        <th style="text-align: left; padding: 10px;">Товар</th>
                        <th style="text-align: right; padding: 10px;">Количество</th>
                        <th style="text-align: right; padding: 10px;">Цена</th>
                        <th style="text-align: right; padding: 10px;">Сумма</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->items as $item)
                    <tr style="border-bottom: 1px solid #e5e7eb;">
                        <td style="padding: 10px;">{{ $item->product_name }}</td>
                        <td style="text-align: right; padding: 10px;">{{ $item->quantity }}</td>
                        <td style="text-align: right; padding: 10px;">{{ number_format($item->price, 0, ',', ' ') }} ₽</td>
                        <td style="text-align: right; padding: 10px;"><strong>{{ number_format($item->subtotal, 0, ',', ' ') }} ₽</strong></td>
                    </tr>
                    @endforeach
                    <tr style="border-top: 2px solid #e5e7eb; background: #f9fafb;">
                        <td colspan="3" style="padding: 10px; text-align: right;"><strong>Итого:</strong></td>
                        <td style="text-align: right; padding: 10px;"><strong>{{ number_format($order->total, 0, ',', ' ') }} ₽</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        @endif
        
        @if($order->notes)
        <div style="background: #ffffff; border: 2px solid #e5e7eb; border-radius: 6px; padding: 20px; margin: 20px 0;">
            <h3 style="color: #1f2937; margin-top: 0;">Примечания</h3>
            <p style="white-space: pre-wrap; margin: 0;">{{ $order->notes }}</p>
        </div>
        @endif
        
        <div style="background: #dbeafe; border-left: 4px solid #2563eb; padding: 15px; margin: 20px 0; border-radius: 4px;">
            <p style="margin: 0;">
                <a href="{{ route('admin.orders.show', $order->id) }}" style="color: #2563eb; text-decoration: none; font-weight: bold;">
                    Просмотреть заказ в админ-панели →
                </a>
            </p>
        </div>
        
        <p style="margin-top: 30px; color: #6b7280; font-size: 14px;">
            Это автоматическое уведомление о новом заказе на сайте eInvestor.ru
        </p>
    </div>
</body>
</html>
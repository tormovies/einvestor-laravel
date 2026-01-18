@extends('layouts.app')

@section('title', 'Мои файлы - EInvestor')

@section('content')
<div class="content">
    <h1>Мои файлы для скачивания</h1>
    
    <div style="margin-top: 2rem;">
        @if($downloads->count() > 0)
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="border-bottom: 2px solid #e5e7eb; background: #f9fafb;">
                    <th style="text-align: left; padding: 1rem;">Товар</th>
                    <th style="text-align: left; padding: 1rem;">Заказ</th>
                    <th style="text-align: center; padding: 1rem;">Скачиваний</th>
                    <th style="text-align: center; padding: 1rem;">Лимит</th>
                    <th style="text-align: center; padding: 1rem;">Срок действия</th>
                    <th style="text-align: center; padding: 1rem;">Действия</th>
                </tr>
            </thead>
            <tbody>
                @foreach($downloads as $download)
                <tr style="border-bottom: 1px solid #e5e7eb;">
                    <td style="padding: 1rem;">
                        @if($download->orderItem && $download->orderItem->product)
                        <strong>{{ $download->orderItem->product->name }}</strong>
                        @elseif($download->orderItem)
                        <strong>{{ $download->orderItem->product_name }}</strong>
                        @else
                        <strong>Товар недоступен</strong>
                        @endif
                    </td>
                    <td style="padding: 1rem;">
                        <a href="{{ route('account.order', $download->order->number) }}" style="color: #2563eb;">
                            {{ $download->order->number }}
                        </a>
                    </td>
                    <td style="padding: 1rem; text-align: center;">
                        {{ $download->download_count }} / {{ $download->download_limit }}
                    </td>
                    <td style="padding: 1rem; text-align: center;">
                        @if($download->download_count >= $download->download_limit)
                            <span style="color: #dc2626;">Превышен</span>
                        @else
                            <span style="color: #16a34a;">Доступно</span>
                        @endif
                    </td>
                    <td style="padding: 1rem; text-align: center;">
                        @if($download->expires_at)
                            @if($download->expires_at->isPast())
                                <span style="color: #dc2626;">Истек</span>
                            @else
                                {{ $download->expires_at->format('d.m.Y') }}
                            @endif
                        @else
                            <span style="color: #6b7280;">—</span>
                        @endif
                    </td>
                    <td style="padding: 1rem; text-align: center;">
                        @if($download->canDownload())
                        <a href="{{ route('download', $download->download_token) }}" class="btn" style="padding: 0.5rem 1rem; font-size: 0.875rem;">Скачать</a>
                        @else
                        <span style="color: #6b7280; font-size: 0.875rem;">Недоступно</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div style="margin-top: 2rem;">
            {{ $downloads->links() }}
        </div>
        @else
        <div style="text-align: center; padding: 3rem 0;">
            <p style="font-size: 1.25rem; color: #6b7280; margin-bottom: 1rem;">У вас нет доступных файлов для скачивания</p>
            <p style="color: #6b7280; margin-bottom: 1rem;">Файлы станут доступны после оплаты заказа</p>
            <a href="{{ route('products.index') }}" class="btn">Перейти к товарам</a>
        </div>
        @endif
    </div>
</div>
@endsection

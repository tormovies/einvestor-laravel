<?php

namespace App\Http\Controllers;

use App\Models\OrderDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    /**
     * Скачивание файла по токену
     * GET /download/{token}
     */
    public function download($token)
    {
        // Находим запись OrderDownload по токену
        $orderDownload = OrderDownload::where('download_token', $token)->first();

        if (!$orderDownload) {
            abort(404, 'Файл не найден или ссылка недействительна');
        }

        // Проверяем права доступа
        if (!$orderDownload->canDownload()) {
            abort(403, 'Срок действия ссылки истек или превышен лимит скачиваний');
        }

        // Получаем товар через orderItem
        $orderItem = $orderDownload->orderItem;
        if (!$orderItem) {
            abort(404, 'Товар не найден');
        }

        $product = $orderItem->product;
        if (!$product || !$product->file_path) {
            abort(404, 'Файл для этого товара не найден');
        }

        // Проверяем, что заказ оплачен
        if (!$orderDownload->order || $orderDownload->order->payment_status !== 'paid') {
            abort(403, 'Заказ еще не оплачен');
        }

        // Формируем путь к файлу
        // file_path может быть относительным (от storage/app) или абсолютным
        $filePath = $product->file_path;
        $absolutePath = null;

        // Пробуем разные варианты расположения файла
        // 1. Проверяем как путь Laravel Storage (относительно storage/app)
        if (Storage::disk('local')->exists($filePath)) {
            $absolutePath = Storage::disk('local')->path($filePath);
        }
        // 2. Проверяем как полный путь к storage/app
        elseif (file_exists(storage_path('app/' . ltrim($filePath, '/')))) {
            $absolutePath = storage_path('app/' . ltrim($filePath, '/'));
        }
        // 3. Проверяем как абсолютный путь
        elseif (file_exists($filePath) && (strpos($filePath, '/') === 0 || preg_match('/^[A-Z]:\\\\/i', $filePath))) {
            $absolutePath = $filePath;
        }
        // 4. Проверяем в public (для совместимости)
        elseif (file_exists(public_path(ltrim($filePath, '/')))) {
            $absolutePath = public_path(ltrim($filePath, '/'));
        }
        
        // Если файл не найден
        if (!$absolutePath || !file_exists($absolutePath)) {
            abort(404, 'Файл не найден на сервере');
        }
        
        $filePath = $absolutePath;

        // Увеличиваем счетчик скачиваний
        $orderDownload->incrementDownloadCount();

        // Получаем имя файла для скачивания
        $fileName = $product->file_name ?? basename($filePath);

        // Возвращаем файл для скачивания
        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}

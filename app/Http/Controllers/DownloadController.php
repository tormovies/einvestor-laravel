<?php

namespace App\Http\Controllers;

use App\Models\OrderDownload;
use App\Models\ProductFile;
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
        if (!$product) {
            abort(404, 'Товар не найден');
        }

        // Проверяем, что заказ оплачен
        if (!$orderDownload->order || $orderDownload->order->payment_status !== 'paid') {
            abort(403, 'Заказ еще не оплачен');
        }

        // Определяем какой файл скачивать
        $filePath = null;
        $fileName = null;
        
        if ($orderDownload->product_file_id) {
            // Файл из таблицы product_files
            $productFile = $orderDownload->productFile;
            if (!$productFile || $productFile->product_id != $product->id) {
                abort(404, 'Файл не найден');
            }
            $filePath = $productFile->file_path;
            $fileName = $productFile->file_name;
        } else {
            // Проверяем есть ли вообще файлы у товара
            $product->load('files');
            if ($product->files->isEmpty()) {
                abort(404, 'Файлы для этого товара не найдены');
            }
            // Если файлов несколько, но не указан product_file_id, берем первый
            $productFile = $product->files->first();
            $filePath = $productFile->file_path;
            $fileName = $productFile->file_name;
        }
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

        // Если имя файла не определено, берем из пути
        if (!$fileName) {
            $fileName = basename($filePath);
        }

        // Возвращаем файл для скачивания
        return response()->download($filePath, $fileName, [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }
}

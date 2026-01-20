<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\RobokassaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class RobokassaController extends Controller
{
    /**
     * Обработка уведомления от Робокассы (Result URL)
     * POST запрос с параметрами: OutSum, InvId, SignatureValue
     */
    public function result(Request $request, RobokassaService $robokassaService)
    {
        Log::info('Robokassa result webhook', $request->all());

        $outSum = $request->input('OutSum');
        $invId = $request->input('InvId');
        $signature = $request->input('SignatureValue');

        if (!$outSum || !$invId || !$signature) {
            Log::error('Robokassa: Missing required parameters', $request->all());
            return response('ERROR: Missing required parameters', 400);
        }

        // Находим заказ
        $order = Order::find($invId);
        if (!$order) {
            Log::error('Robokassa: Order not found', ['invId' => $invId]);
            return response('ERROR: Order not found', 404);
        }

        // Проверяем подпись
        if (!$robokassaService->verifyResultSignature((float)$outSum, (int)$invId, $signature)) {
            Log::error('Robokassa: Invalid signature', [
                'order_id' => $order->id,
                'expected' => $robokassaService->generateResultSignature((float)$outSum, (int)$invId),
                'received' => $signature,
            ]);
            return response('ERROR: Invalid signature', 400);
        }

        // Обновляем статус заказа
        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
            'payment_id' => $request->input('PaymentMethod') ?? 'robokassa',
        ]);

        Log::info('Robokassa: Order payment confirmed', [
            'order_id' => $order->id,
            'order_number' => $order->number,
        ]);

        // Ответ должен быть в формате OK<InvId>
        return response('OK' . $invId, 200);
    }

    /**
     * Страница успешной оплаты (Success URL)
     * Робокасса может отправлять как GET, так и POST
     */
    public function success(Request $request)
    {
        // Отладочная информация - сначала логируем все детали
        $debugInfo = [
            'method' => $request->method(),
            'path' => $request->path(),
            'full_url' => $request->fullUrl(),
            'all_params' => $request->all(),
            'query_params' => $request->query(),
            'post_params' => $request->post(),
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'has_session' => $request->hasSession(),
            'session_id' => $request->hasSession() ? session()->getId() : null,
        ];
        
        Log::info('Robokassa success URL called', $debugInfo);
        
        $invId = $request->input('InvId');
        
        if (!$invId) {
            Log::warning('Robokassa success: InvId not provided', $debugInfo);
            // В режиме отладки показываем информацию
            if (config('app.debug')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'InvId не предоставлен',
                    'debug' => $debugInfo
                ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
            }
            return redirect()->route('home');
        }

        $order = Order::find($invId);
        
        if (!$order) {
            Log::warning('Robokassa success: Order not found', ['invId' => $invId] + $debugInfo);
            // В режиме отладки показываем информацию
            if (config('app.debug')) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Заказ не найден',
                    'invId' => $invId,
                    'debug' => $debugInfo
                ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
            }
            return redirect()->route('home');
        }

        // Перенаправляем на страницу успеха с номером заказа в URL
        // Используем query параметр вместо сессии, чтобы избежать проблем с CSRF
        return redirect()->route('checkout.success', ['orderNumber' => $order->number, 'payment' => 'success']);
    }

    /**
     * Страница неуспешной оплаты (Fail URL)
     */
    public function fail(Request $request)
    {
        // Отладочная информация - сначала логируем все детали
        $debugInfo = [
            'method' => $request->method(),
            'path' => $request->path(),
            'full_url' => $request->fullUrl(),
            'all_params' => $request->all(),
            'query_params' => $request->query(),
            'post_params' => $request->post(),
            'headers' => $request->headers->all(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'has_session' => $request->hasSession(),
            'session_id' => $request->hasSession() ? session()->getId() : null,
        ];
        
        Log::info('Robokassa fail URL called', $debugInfo);
        
        $invId = $request->input('InvId');
        
        $order = null;
        if ($invId) {
            $order = Order::find($invId);
        }

        // В тестовом режиме Робокасса может не передавать InvId
        // Проверяем также другие возможные параметры
        if (!$order && $request->has('OutSum')) {
            Log::warning('Robokassa fail: Order not found by InvId', $debugInfo);
        }

        // В режиме отладки можем показывать JSON вместо страницы
        if (config('app.debug') && $request->wantsJson()) {
            return response()->json([
                'status' => 'fail',
                'invId' => $invId,
                'order_found' => $order !== null,
                'debug' => $debugInfo
            ], 200, ['Content-Type' => 'application/json; charset=utf-8']);
        }

        return view('checkout.fail', compact('order'));
    }
}

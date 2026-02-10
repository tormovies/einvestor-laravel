<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\TelegramNotificationService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Список заказов
     */
    public function index(Request $request)
    {
        $query = Order::with('items.product', 'user')
            ->orderBy('created_at', 'desc');

        // Фильтрация по статусу
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Фильтрация по статусу оплаты
        if ($request->has('payment_status') && $request->payment_status) {
            $query->where('payment_status', $request->payment_status);
        }

        // Поиск по номеру или email
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(20);

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Детали заказа
     */
    public function show($id)
    {
        $order = Order::with(['items.product', 'downloads.orderItem.product', 'user'])
            ->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Обновление статуса заказа
     */
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled,refunded',
        ]);

        $order->update([
            'status' => $request->status,
        ]);

        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'Статус заказа обновлен');
    }

    /**
     * Подтвердить оплату вручную (если webhook не сработал).
     * Меняет payment_status на paid → срабатывает отправка пароля на email из Order::updating.
     */
    public function confirmPayment($id)
    {
        $order = Order::findOrFail($id);

        if ($order->payment_status === 'paid') {
            return redirect()->route('admin.orders.show', $order->id)
                ->with('info', 'Заказ уже помечен как оплаченный.');
        }

        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
            'payment_id' => $order->payment_id ?: 'manual',
        ]);

        $telegram = app(TelegramNotificationService::class);
        if ($telegram->isConfigured()) {
            $telegram->notifyOrderPaid($order);
        }

        return redirect()->route('admin.orders.show', $order->id)
            ->with('success', 'Оплата подтверждена. Пароль для входа отправлен на email заказчика.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDownload;
use App\Models\Product;
use App\Services\RobokassaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Страница оформления заказа
     */
    public function index()
    {
        $cart = session('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Корзина пуста');
        }

        $items = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product && $product->status === 'publish') {
                $items[$productId] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $product->price * $item['quantity']
                ];
                $total += $items[$productId]['subtotal'];
            }
        }

        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'В корзине нет доступных товаров');
        }

        return view('checkout.index', compact('items', 'total'));
    }

    /**
     * Создание заказа и перенаправление на оплату
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ]);

        $cart = session('cart', []);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Корзина пуста');
        }

        // Проверяем товары и считаем сумму
        $items = [];
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if (!$product || $product->status !== 'publish' || !$product->isInStock()) {
                continue;
            }
            $items[$productId] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'price' => $product->price,
                'subtotal' => $product->price * $item['quantity']
            ];
            $total += $items[$productId]['subtotal'];
        }

        if (empty($items)) {
            return redirect()->route('cart.index')->with('error', 'В корзине нет доступных товаров');
        }

        // Создаем заказ
        DB::beginTransaction();
        try {
            $order = Order::create([
                'email' => $request->email,
                'name' => $request->name,
                'phone' => $request->phone,
                'total' => $total,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'robokassa',
                'notes' => $request->notes,
            ]);

            // Создаем позиции заказа и записи для скачивания
            foreach ($items as $productId => $item) {
                $orderItem = $order->items()->create([
                    'order_id' => $order->id,
                    'product_id' => $productId,
                    'product_name' => $item['product']->name,
                    'product_sku' => $item['product']->sku,
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'subtotal' => $item['subtotal'],
                ]);

                // Создаем записи для скачивания файлов
                $product = $item['product'];
                $product->load('files');
                
                if ($product->files->isNotEmpty()) {
                    // Создаем OrderDownload для каждого файла товара
                    foreach ($product->files as $productFile) {
                        OrderDownload::create([
                            'order_id' => $order->id,
                            'order_item_id' => $orderItem->id,
                            'product_file_id' => $productFile->id,
                            'user_id' => $order->user_id,
                            'email' => $order->email,
                            'download_token' => Str::random(64),
                            'download_limit' => 5,
                            'expires_at' => now()->addYear(),
                        ]);
                    }
                }
            }

            DB::commit();

            // Очищаем корзину
            session(['cart' => []]);

            // Перенаправление на Робокассу
            $robokassaService = app(RobokassaService::class);
            
            if ($robokassaService->isConfigured()) {
                $description = 'Заказ #' . $order->number;
                $paymentUrl = $robokassaService->getPaymentUrl(
                    $order->total,
                    $order->id,
                    $description
                );
                
                return redirect()->away($paymentUrl);
            } else {
                // Если Робокасса не настроена - показываем страницу успеха
                return redirect()->route('checkout.success', $order->number)
                    ->with('success', 'Заказ создан! Номер заказа: ' . $order->number)
                    ->with('warning', 'Робокасса не настроена. Оплата будет добавлена позже.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Ошибка при создании заказа: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Страница успешного создания заказа
     */
    public function success($orderNumber)
    {
        $order = Order::where('number', $orderNumber)->firstOrFail();
        return view('checkout.success', compact('order'));
    }
}

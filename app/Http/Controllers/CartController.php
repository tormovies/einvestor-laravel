<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    /**
     * Отображение корзины
     */
    public function index()
    {
        $cart = session('cart', []);
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

        return view('cart.index', compact('items', 'total'));
    }

    /**
     * Добавление товара в корзину
     */
    public function add(Request $request, $productId)
    {
        $product = Product::findOrFail($productId);

        // Проверка, что товар доступен
        if ($product->status !== 'publish') {
            return back()->with('error', 'Товар недоступен для покупки');
        }

        if (!$product->isInStock()) {
            return back()->with('error', 'Товар отсутствует на складе');
        }

        $cart = session('cart', []);
        $quantity = $request->input('quantity', 1);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'quantity' => $quantity
            ];
        }

        session(['cart' => $cart]);

        return back()->with('success', 'Товар добавлен в корзину');
    }

    /**
     * Обновление количества товара в корзине
     */
    public function update(Request $request, $productId)
    {
        $quantity = $request->input('quantity', 1);

        if ($quantity <= 0) {
            return $this->remove($productId);
        }

        $cart = session('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = $quantity;
            session(['cart' => $cart]);
        }

        return redirect()->route('cart.index')->with('success', 'Корзина обновлена');
    }

    /**
     * Удаление товара из корзины
     */
    public function remove($productId)
    {
        $cart = session('cart', []);

        if (isset($cart[$productId])) {
            unset($cart[$productId]);
            session(['cart' => $cart]);
        }

        return redirect()->route('cart.index')->with('success', 'Товар удален из корзины');
    }

    /**
     * Очистка корзины
     */
    public function clear()
    {
        session(['cart' => []]);
        return redirect()->route('cart.index')->with('success', 'Корзина очищена');
    }

    /**
     * Получение количества товаров в корзине (для шапки)
     */
    public static function getCartCount()
    {
        $cart = session('cart', []);
        $count = 0;
        foreach ($cart as $item) {
            $count += $item['quantity'] ?? 1;
        }
        return $count;
    }

    /**
     * Получение суммы корзины (для шапки)
     */
    public static function getCartTotal()
    {
        $cart = session('cart', []);
        $total = 0;

        foreach ($cart as $productId => $item) {
            $product = Product::find($productId);
            if ($product) {
                $total += $product->price * ($item['quantity'] ?? 1);
            }
        }

        return $total;
    }
}

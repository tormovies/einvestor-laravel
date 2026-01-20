<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderDownload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    /**
     * Главная страница личного кабинета
     */
    public function index()
    {
        $user = Auth::user();
        $ordersCount = $user->orders()->count();
        $downloadsCount = $user->downloads()->whereHas('order', function ($query) {
            $query->where('payment_status', 'paid');
        })->count();
        
        return view('account.index', compact('ordersCount', 'downloadsCount'));
    }

    /**
     * История заказов
     */
    public function orders()
    {
        $user = Auth::user();
        $orders = $user->orders()
            ->with('items.product')
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('account.orders', compact('orders'));
    }

    /**
     * Детали заказа
     */
    public function order($orderNumber)
    {
        $user = Auth::user();
        $order = $user->orders()
            ->where('number', $orderNumber)
            ->with(['items.product', 'downloads.orderItem.product'])
            ->firstOrFail();
        
        return view('account.order', compact('order'));
    }

    /**
     * Доступные файлы для скачивания
     */
    public function downloads()
    {
        $user = Auth::user();
        $downloads = $user->downloads()
            ->whereHas('order', function ($query) {
                $query->where('payment_status', 'paid');
            })
            ->with(['orderItem.product', 'order', 'productFile'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        return view('account.downloads', compact('downloads'));
    }

    /**
     * Профиль пользователя
     */
    public function profile()
    {
        $user = Auth::user();
        return view('account.profile', compact('user'));
    }

    /**
     * Обновление профиля
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return redirect()->route('account.profile')->with('success', 'Профиль обновлен');
    }
}

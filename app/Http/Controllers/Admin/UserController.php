<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Список пользователей
     */
    public function index(Request $request)
    {
        $query = User::withCount('orders')
            ->withSum('orders', 'total')
            ->orderBy('created_at', 'desc');

        // Поиск по email или имени
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%");
            });
        }

        // Фильтр: только с заказами
        if ($request->has('has_orders') && $request->has_orders) {
            $query->havingRaw('orders_count > 0');
        }

        // Фильтр: только администраторы
        if ($request->has('is_admin') && $request->is_admin !== '') {
            $query->where('is_admin', $request->is_admin);
        }

        $users = $query->paginate(20);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Детальный просмотр пользователя
     */
    public function show($id)
    {
        $user = User::with(['orders.items.product', 'orders' => function($query) {
            $query->orderBy('created_at', 'desc');
        }])
            ->withCount('orders')
            ->withSum('orders', 'total')
            ->findOrFail($id);

        return view('admin.users.show', compact('user'));
    }
}

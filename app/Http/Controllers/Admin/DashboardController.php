<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('admin');
    }

    /**
     * Главная страница админ-панели
     */
    public function index()
    {
        $stats = [
            'orders_total' => Order::count(),
            'orders_pending' => Order::where('status', 'pending')->count(),
            'orders_paid' => Order::where('payment_status', 'paid')->count(),
            'products_total' => Product::count(),
            'products_published' => Product::where('status', 'publish')->count(),
            'posts_total' => Post::count(),
            'posts_published' => Post::where('status', 'publish')->count(),
        ];

        // Последние заказы
        $recentOrders = Order::with('items.product')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Статистика продаж за месяц
        $monthlyRevenue = Order::where('payment_status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->sum('total');

        return view('admin.dashboard', compact('stats', 'recentOrders', 'monthlyRevenue'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Page;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('status', 'publish')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        // Получаем системную страницу для списка товаров
        $systemPage = Page::where('slug', '_products_list')->first();
        
        return view('products.index', compact('products', 'systemPage'));
    }

    public function show(string $slug)
    {
        $product = Product::where('slug', $slug)
            ->where('status', 'publish')
            ->firstOrFail();
        
        $relatedProducts = Product::where('status', 'publish')
            ->where('id', '!=', $product->id)
            ->whereHas('categories', function ($query) use ($product) {
                $query->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->limit(4)
            ->get();
        
        return view('products.show', compact('product', 'relatedProducts'));
    }
}

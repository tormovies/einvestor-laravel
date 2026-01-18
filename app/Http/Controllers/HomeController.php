<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $latestPosts = Post::where('status', 'publish')
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();
            
        $featuredProducts = Product::where('status', 'publish')
            ->where('stock_status', 'in_stock')
            ->orderBy('created_at', 'desc')
            ->limit(6)
            ->get();
        
        return view('home', compact('latestPosts', 'featuredProducts'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(string $slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        
        if ($category->type === 'post') {
            $posts = $category->posts()
                ->where('status', 'publish')
                ->orderBy('published_at', 'desc')
                ->paginate(10);
            
            return view('categories.posts', compact('category', 'posts'));
        } else {
            $products = $category->products()
                ->where('status', 'publish')
                ->orderBy('created_at', 'desc')
                ->paginate(12);
            
            return view('categories.products', compact('category', 'products'));
        }
    }
}

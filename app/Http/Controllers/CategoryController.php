<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function show(string $slug)
    {
        try {
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
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            abort(404, 'Категория не найдена');
        } catch (\Exception $e) {
            \Log::error('CategoryController error', [
                'slug' => $slug,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            abort(500, 'Ошибка при загрузке категории');
        }
    }
}

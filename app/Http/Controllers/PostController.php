<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Page;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::where('status', 'publish')
            ->orderBy('published_at', 'desc')
            ->paginate(10);
        
        // Получаем системную страницу для списка статей
        $systemPage = Page::where('slug', '_articles_list')->first();
        
        return view('posts.index', compact('posts', 'systemPage'));
    }

    public function show(string $slug)
    {
        $post = Post::where('slug', $slug)
            ->where('status', 'publish')
            ->with('files')
            ->firstOrFail();
        
        $relatedPosts = Post::where('status', 'publish')
            ->where('id', '!=', $post->id)
            ->whereHas('categories', function ($query) use ($post) {
                $query->whereIn('categories.id', $post->categories->pluck('id'));
            })
            ->limit(3)
            ->get();
        
        return view('posts.show', compact('post', 'relatedPosts'));
    }
}

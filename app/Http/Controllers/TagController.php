<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\Request;

class TagController extends Controller
{
    public function show(string $slug)
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        
        $posts = $tag->posts()
            ->where('status', 'publish')
            ->orderBy('published_at', 'desc')
            ->paginate(10);
        
        $products = $tag->products()
            ->where('status', 'publish')
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return view('tags.show', compact('tag', 'posts', 'products'));
    }
}

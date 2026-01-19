<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function show(string $slug)
    {
        // Исключаем системные страницы (они обрабатываются через другие контроллеры)
        $systemPages = ['_home', '_products_list', '_articles_list'];
        if (in_array($slug, $systemPages)) {
            abort(404);
        }
        
        $page = Page::where('slug', $slug)
            ->where('status', 'publish')
            ->firstOrFail();
        
        return view('pages.show', compact('page'));
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Page;

class IndexController extends Controller
{
    public function show($slug)
    {
        // Try to find a page first
        if ($page = Page::where('slug', $slug)->where('is_active', true)->first()) {
            return app(PageController::class)->show($page);
        }

        // Then try to find a category
        if ($category = Category::where('slug', $slug)->first()) {
            return app(CategoryController::class)->show($category);
        }

        abort(404);
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Page;

class PageController extends Controller
{
    public function show(Page $page)
    {
        // Check if page is active
        if (!$page->is_active) {
            abort(404);
        }

        return view('pages.show', compact('page'));
    }
}
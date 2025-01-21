<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;

class CategoryController extends Controller
{
    public function show(Category $category)
    {
        // Get most viewed movies of the category
        $mostViewedMovies = $category->movies()
            ->where('status', 'published')
            ->orderBy('views_count', 'desc')
            ->take(12)
            ->get();

        // Get paginated latest movies of the category
        $latestMovies = $category->movies()
            ->where('status', 'published')
            ->where('status', 'published')
            ->latest()
            ->paginate(50);

        return view('categories.show', compact(
            'category',
            'mostViewedMovies',
            'latestMovies'
        ));
    }
}
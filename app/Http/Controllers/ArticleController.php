<?php

namespace App\Http\Controllers;

use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        // Get latest paginated articles
        $latestArticles = Article::latest()
            ->where('is_published', 1)
            ->paginate(10);

        return view('articles.index', compact('latestArticles'));
    }

    public function show(Article $article)
    {
        // If no slug, redirect to the article list
        if (!$article->slug) {
            return redirect()->route('articles.index');
        }

        // Check if article is published
        if (!$article->is_published) {
            abort(404);
        }

        // Get related articles
        $relatedArticles = Article::where('id', '!=', $article->id)
            ->where('is_published', 1)
            ->latest()
            ->limit(5)
            ->get();

        return view('articles.show', compact('article', 'relatedArticles'));
    }
}

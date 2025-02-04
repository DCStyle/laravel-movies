<?php

namespace App\Providers;

use App\Models\Article;
use App\Models\Movie;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            // Get trending movies for sidebar
            $sidebarTrendingMovies = cache()->remember('trending_movies', 3600, function () {
                return Movie::where('created_at', '>=', Carbon::now()->subMonths(6))
                    ->orderByDesc('rating')
                    ->orderByDesc('views_count')
                    ->limit(6)
                    ->get();
            });

            $sidebarFeaturedMovie = $sidebarTrendingMovies->first();
            $sidebarTrendingMovies = $sidebarTrendingMovies->slice(1);

            // Get latest articles for sidebar
            $latestArticles = cache()->remember('latest_articles', 3600, function () {
                return Article::where('is_published', true)
                    ->latest()
                    ->limit(5)
                    ->get();
            });

            $view->with([
                'sidebarTrendingMovies' => $sidebarTrendingMovies,
                'sidebarFeaturedMovie' => $sidebarFeaturedMovie,
                'latestArticles' => $latestArticles
            ]);
        });
    }
}
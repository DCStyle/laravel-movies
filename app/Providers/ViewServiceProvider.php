<?php

namespace App\Providers;

use App\Models\Movie;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('*', function ($view) {
            $sidebarTrendingMovies = cache()->remember('trending_movies', 3600, function () {
                return Movie::where('created_at', '>=', Carbon::now()->subMonths(6))
                    ->orderByDesc('rating')
                    ->orderByDesc('views_count')
                    ->limit(6)
                    ->get();
            });

            $sidebarFeaturedMovie = $sidebarTrendingMovies->first();

            // Remove the first movie from the trending movies collection
            $sidebarTrendingMovies = $sidebarTrendingMovies->slice(1);

            $view->with([
                'sidebarTrendingMovies' => $sidebarTrendingMovies,
                'sidebarFeaturedMovie' => $sidebarFeaturedMovie
            ]);
        });
    }
}
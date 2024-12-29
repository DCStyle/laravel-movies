<?php

namespace App\Http\Controllers\Mod;

use App\Http\Controllers\Controller;
use App\Models\Movie;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:mod']);
    }

    public function index()
    {
        $stats = [
            'total_movies' => Movie::where('uploaded_by', auth()->id())->count(),
            'published_movies' => Movie::where('uploaded_by', auth()->id())
                ->where('status', 'published')
                ->count(),
            'total_views' => Movie::where('uploaded_by', auth()->id())
                ->sum('views_count'),
        ];

        $latest_movies = Movie::where('uploaded_by', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        return view('mod.dashboard', compact('stats', 'latest_movies'));
    }
}
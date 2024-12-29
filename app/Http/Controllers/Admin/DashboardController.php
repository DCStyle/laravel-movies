<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\User;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function index()
    {
        $stats = [
            'total_movies' => Movie::count(),
            'published_movies' => Movie::where('status', 'published')->count(),
            'total_views' => Movie::sum('views_count'),
            'total_mods' => User::role('mod')->count(),
        ];

        $latest_movies = Movie::latest()->take(5)->get();
        $top_movies = Movie::orderBy('views_count', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'latest_movies', 'top_movies'));
    }
}
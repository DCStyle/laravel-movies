<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class ReleaseYearController extends Controller
{
    public function show($year)
    {
        // Check if the year is valid
        if (!in_array($year, range(1900, date('Y')))) {
            abort(404);
        }

        // Get movies by release year
        $latestMovies = Movie::whereYear('release_year', $year)
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

        $SEOData = new SEOData(
            title: 'Năm phát hành ' . $year,
            description: 'Danh sách phim năm ' . $year,
            tags: ['phim', 'phim ' . $year],
        );

        return view('release-years.show', compact(
            'year',
            'latestMovies',
            'SEOData',
        ));
    }
}
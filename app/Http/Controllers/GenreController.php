<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Genre;

class GenreController extends Controller
{
    public function show(Genre $genre)
    {
        // Get paginated movies for the genre
        $latestMovies = $genre->movies()
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

        return view('genres.show', compact(
            'genre',
            'latestMovies'
        ));
    }
}
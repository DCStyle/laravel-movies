<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Season;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    public function index(Movie $movie)
    {
        abort_if($movie->type !== 'series', 404);

        return view('movies.seasons.index', [
            'movie' => $movie->load('seasons.episodes'),
        ]);
    }

    public function store(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'number' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
        ]);

        $season = $movie->seasons()->create($validated);

        return redirect()
            ->route('seasons.index', [$movie, $season])
            ->with('success', 'Season created successfully');
    }

    public function update(Request $request, Movie $movie, Season $season)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'release_date' => 'nullable|date',
        ]);

        $season->update($validated);

        return redirect()
            ->route('seasons.index', [$movie, $season])
            ->with('success', 'Season updated successfully');
    }

    public function destroy(Movie $movie, Season $season)
    {
        $season->delete();

        return redirect()
            ->route('seasons.index', $movie)
            ->with('success', 'Season deleted successfully');
    }
}
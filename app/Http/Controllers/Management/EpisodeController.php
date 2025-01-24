<?php

namespace App\Http\Controllers\Management;

use App\Http\Controllers\Controller;
use App\Models\Episode;
use App\Models\Season;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EpisodeController extends Controller
{
    public function index(Season $season)
    {
        return view('movies.episodes.index', [
            'season' => $season->load('episodes.sources'),
            'movie' => $season->movie,
        ]);
    }

    public function create(Season $season)
    {
        return view('movies.episodes.add_edit', [
            'isEdit' => false,
            'season' => $season->load('movie'),
            'movie' => $season->movie
        ]);
    }


    public function store(Request $request, Season $season)
    {
        $validated = $request->validate([
            'number' => 'required|integer|min:1',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'duration' => 'nullable|integer|min:1',
            'air_date' => 'nullable|date',
            'sources' => 'required|array|min:1',
            'sources.*.source_type' => 'required|in:direct,fshare,gdrive,youtube,twitter,facebook,tiktok',
            'sources.*.quality' => 'required|in:360p,480p,720p,1080p,4k',
            'sources.*.source_url' => 'required|url',
        ]);

        // Create episode
        $episode = $season->episodes()->create([
            'number' => $validated['number'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'duration' => $validated['duration'],
            'air_date' => $validated['air_date'],
        ]);

        // Handle thumbnail
        if ($request->hasFile('thumbnail')) {
            $episode->update([
                'thumbnail' => $request->file('thumbnail')->store('episodes', 'public')
            ]);
        }

        // Create sources
        foreach ($validated['sources'] as $index => $source) {
            $episode->sources()->create([
                ...$source,
                'is_primary' => $index === 0
            ]);
        }

        $movie = $season->movie;

        return redirect()
            ->route('episodes.index', $season)
            ->with('success', 'Episode created successfully');
    }

    public function edit(Season $season, Episode $episode)
    {
        return view('movies.episodes.add_edit', [
            'isEdit' => true,
            'season' => $season,
            'episode' => $episode->load('sources'),
            'movie' => $season->movie
        ]);
    }

    public function update(Request $request, Season $season, Episode $episode)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail' => 'nullable|image|max:2048',
            'duration' => 'nullable|integer|min:1',
            'air_date' => 'nullable|date',
            'sources' => 'required|array|min:1',
            'sources.*.source_type' => 'required|in:direct,fshare,gdrive,youtube,twitter,facebook,tiktok',
            'sources.*.quality' => 'required|in:360p,480p,720p,1080p,4k',
            'sources.*.source_url' => 'required|url',
        ]);

        // Update episode
        $episode->update([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'duration' => $validated['duration'],
            'air_date' => $validated['air_date'],
        ]);

        // Handle thumbnail
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($episode->thumbnail) {
                Storage::disk('public')->delete($episode->thumbnail);
            }

            $episode->update([
                'thumbnail' => $request->file('thumbnail')->store('episodes', 'public')
            ]);
        }

        // Update sources
        $episode->sources()->delete();
        foreach ($validated['sources'] as $index => $source) {
            $episode->sources()->create([
                ...$source,
                'is_primary' => $index === 0
            ]);
        }

        return redirect()
            ->route('episodes.index', $season)
            ->with('success', 'Episode updated successfully');
    }

    public function destroy(Season $season, Episode $episode)
    {
        $movie = $season->movie;

        // Delete thumbnail
        if ($episode->thumbnail) {
            Storage::disk('public')->delete($episode->thumbnail);
        }

        $episode->delete();

        return redirect()
            ->route('episodes.index', $season)
            ->with('success', 'Episode deleted successfully');
    }
}
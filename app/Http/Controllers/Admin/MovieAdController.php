<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MovieAd;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class MovieAdController extends Controller
{
    public function index()
    {
        $query = MovieAd::query();

        if (request('search')) {
            $query->where('name', 'like', '%' . request('search') . '%');
        }

        if (request('type')) {
            $query->where('type', request('type'));
        }

        $ads = $query->orderBy('order')->paginate(12);

        return view('admin.movie-ads.index', compact('ads'));
    }

    public function create()
    {
        $movieAd = new MovieAd();

        return view('admin.movie-ads.add_edit', compact('movieAd'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:image,video',
            'content' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,webm|max:10240',
            'display_time' => 'required|numeric|min:0|max:100',
            'duration' => 'required_if:type,image|nullable|integer|min:1',
            'click_url' => 'nullable|url',
            'order' => 'nullable|integer|min:0',
        ]);

        $path = $request->file('content')->store('movie-ads', 'public');

        MovieAd::create([
            'name' => $validated['name'],
            'type' => $validated['type'],
            'content_path' => $path,
            'display_time' => $validated['display_time'],
            'duration' => $validated['duration'],
            'click_url' => $validated['click_url'],
            'order' => $validated['order'] ?? 0,
        ]);

        return redirect()->route('admin.movie-ads.index')
            ->with('success', 'Movie ad created successfully');
    }

    public function edit(MovieAd $movieAd)
    {
        return view('admin.movie-ads.add_edit', compact('movieAd'));
    }

    public function update(Request $request, MovieAd $movieAd)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:image,video',
            'content' => 'nullable|file|mimes:jpeg,png,jpg,gif,mp4,webm|max:10240',
            'display_time' => 'required|numeric|min:0|max:100',
            'duration' => 'required_if:type,image|nullable|integer|min:1',
            'click_url' => 'nullable|url',
            'order' => 'nullable|integer|min:0',
            'is_enabled' => 'boolean',
        ]);

        if ($request->hasFile('content')) {
            // Delete old file
            Storage::delete($movieAd->content_path);
            // Store new file
            $validated['content_path'] = $request->file('content')->store('movie-ads', 'public');
        }

        $movieAd->update($validated);

        return redirect()->route('admin.movie-ads.index')
            ->with('success', 'Movie ad updated successfully');
    }

    public function destroy(MovieAd $movieAd)
    {
        Storage::delete($movieAd->content_path);
        $movieAd->delete();

        return redirect()->route('admin.movie-ads.index')
            ->with('success', 'Movie ad deleted successfully');
    }

    public function toggleStatus(MovieAd $movieAd)
    {
        $movieAd->update(['is_enabled' => !$movieAd->is_enabled]);
        return response()->json(['status' => 'success']);
    }

    public function updateOrder(Request $request)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'required|integer|exists:movie_ads,id',
        ]);

        foreach ($request->orders as $index => $id) {
            MovieAd::where('id', $id)->update(['order' => $index]);
        }

        return response()->json(['status' => 'success']);
    }
}
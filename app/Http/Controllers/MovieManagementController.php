<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Movie;
use App\Traits\LogsActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MovieManagementController extends Controller
{
    use LogsActivity;

    public function index(Request $request)
    {
        $query = Auth::user()->hasRole('Admin') ? Movie::all() : Movie::where('uploaded_by', Auth::id());

        if ($search = $request->get('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('slug', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $movies = $query
            ->with(['genres', 'category', 'sources', 'uploader'])
            ->latest()
            ->paginate(50);

        return view('movies.management.index', compact('movies'));
    }

    public function create()
    {
        return view('movies.management.add_edit', [
            'isEdit' => false,
            'genres' => Genre::all(),
            'categories' => Category::all(),
            'countries' => Movie::fetchCountries(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'banner' => 'nullable|image|max:20480',
            'thumbnail' => 'nullable|image|max:20480',
            'release_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'duration' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,published',
            'genres' => 'array',
            'genres.*' => 'exists:genres,id',
            'category_id' => 'required|exists:categories,id',
            'sources' => 'required|array',
            'sources.*.source_type' => 'required|in:direct,fshare,gdrive,youtube,twitter,facebook,tiktok',
            'sources.*.quality' => 'required|in:360p,480p,720p,1080p,4k',
            'sources.*.source_url' => 'required|url',
            'country' => 'required|string|max:255',
            'rating' => 'required|numeric|min:0|max:10',
            'age_rating' => 'required|in:G,PG,PG-13,R',
        ]);

        $movie = Movie::create([
            ...$validated,
            'slug' => Str::slug($validated['title']),
            'uploaded_by' => auth()->id(),
        ]);

        // Handle movie thumbnail
        if ($request->hasFile('thumbnail')) {
            $movie->update([
                'thumbnail' => $request->file('thumbnail')->store('movies', 'public')
            ]);
        }

        // Handle movie banner
        if ($request->hasFile('banner')) {
            $movie->update([
                'banner' => $request->file('banner')->store('movies/banners', 'public')
            ]);
        }

        // Handle movie sources
        if ($request->has('sources')) {
            foreach ($request->sources as $source) {
                $movie->sources()->create($source);
            }
        }

        // Attach genres
        if (!empty($validated['genres'])) {
            $movie->genres()->sync($validated['genres']);
        }

        // Log activity
        $this->logActivity(
            'create_movie',
            'Created movie: ' . $movie->title,
            [
                'movie_id' => $movie->id,
                'title' => $movie->title,
                'status' => $movie->status
            ]
        );

        return redirect()
            ->route('movies.management.index')
            ->with('success', 'Tạo phim thành công.');
    }

    public function edit(Movie $movie)
    {
        return view('movies.management.add_edit', [
            'isEdit' => true,
            'movie' => $movie,
            'genres' => Genre::all(),
            'categories' => Category::all(),
            'countries' => Movie::fetchCountries(),
        ]);
    }

    public function update(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'title' => 'required|max:255',
            'description' => 'nullable',
            'banner' => 'nullable|image|max:20480',
            'thumbnail' => 'nullable|image|max:20480',
            'release_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 1),
            'duration' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,published',
            'genres' => 'array',
            'genres.*' => 'exists:genres,id',
            'category_id' => 'required|exists:categories,id',
            'sources' => 'required|array',
            'sources.*.source_type' => 'required|in:direct,fshare,gdrive,youtube,twitter,facebook,tiktok',
            'sources.*.quality' => 'required|in:360p,480p,720p,1080p,4k',
            'sources.*.source_url' => 'required|url',
            'country' => 'required|string|max:255',
            'rating' => 'required|numeric|min:0|max:10',
            'age_rating' => 'required|in:G,PG,PG-13,R'
        ]);

        $movie->update([
            ...$validated,
            'slug' => Str::slug($validated['title']),
        ]);

        // Handle movie thumbnail
        if ($request->hasFile('thumbnail')) {
            $movie->update([
                'thumbnail' => $request->file('thumbnail')->store('movies', 'public')
            ]);
        }

        // Handle movie banner
        if ($request->hasFile('banner')) {
            $movie->update([
                'banner' => $request->file('banner')->store('movies/banners', 'public')
            ]);
        }

        // Handle movie sources
        if ($request->has('sources')) {
            // Remove old sources
            $movie->sources()->delete();

            foreach ($request->sources as $source) {
                $movie->sources()->create($source);
            }
        }

        // Attach genres
        if (!empty($validated['genres'])) {
            $movie->genres()->sync($validated['genres']);
        }

        // Log activity
        $this->logActivity(
            'update_movie',
            'Updated movie: ' . $movie->title,
            [
                'movie_id' => $movie->id,
                'title' => $movie->title,
                'status' => $movie->status
            ]
        );

        return redirect()
            ->route('movies.management.index')
            ->with('success', 'Cập nhật phim thành công.');
    }

    public function destroy(Movie $movie)
    {
        // Log activity
        $this->logActivity(
            'delete_movie',
            'Deleted movie: ' . $movie->title,
            [
                'movie_id' => $movie->id,
                'title' => $movie->title,
                'status' => $movie->status
            ]
        );

        $movie->delete();

        return redirect()
            ->route('movies.management.index')
            ->with('success', 'Xóa phim thành công.');
    }
}
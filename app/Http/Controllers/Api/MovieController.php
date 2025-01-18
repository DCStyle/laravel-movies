<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use App\Models\Genre;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MovieController extends Controller
{
    public function findMovieByCrawlSourceUrl(Request $request)
    {
        $validatedData = $request->validate([
            'md5_hashed_crawl_source_url' => 'required|string'
        ]);


    }

    public function importMovie(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'title_en' => 'nullable|string|max:255',
            'type' => 'required|in:single,series',
            'description' => 'nullable|string',
            'banner_url' => 'nullable|string',
            'thumbnail_url' => 'nullable|string',
            'release_year' => 'nullable|integer|min:1900',
            'duration' => 'nullable|integer',
            'genres' => 'nullable|string',
            'category' => 'required|string',
            'country' => 'nullable|string|max:2',
            'rating' => 'nullable|numeric|min:0|max:10',
            'age_rating' => 'required|in:G,PG,PG-13,R',
            'crawl_source_url' => 'nullable|string'
        ]);

        $category = Category::firstOrCreate(['name' => $validatedData['category']]);
        $existingMovie = Movie::where('crawl_source_url', $validatedData['crawl_source_url'])->first();

        $baseSlug = Str::slug($validatedData['title']);
        $slug = $baseSlug;
        $counter = 1;

        while (Movie::where('slug', $slug)
            ->where('id', '!=', optional($existingMovie)->id)
            ->exists()) {
            $slug = $baseSlug . '-' . $counter++;
        }

        $movieData = [
            'title' => $validatedData['title'],
            'title_en' => $validatedData['title_en'],
            'slug' => $slug,
            'description' => $validatedData['description'],
            'banner' => $validatedData['banner_url'],
            'thumbnail' => $validatedData['thumbnail_url'],
            'release_year' => $validatedData['release_year'],
            'duration' => $validatedData['duration'],
            'country' => $validatedData['country'],
            'rating' => $validatedData['rating'],
            'age_rating' => $validatedData['age_rating'],
            'category_id' => $category->id,
            'type' => $validatedData['type'],
            'crawl_source_url' => $validatedData['crawl_source_url'],
            'status' => 'published',
            'uploaded_by' => 1
        ];

        if ($existingMovie) {
            $movie = tap($existingMovie)->update($movieData);
        } else {
            $movie = Movie::create($movieData);
        }

        if (!empty($validatedData['genres'])) {
            $genreNames = explode(',', $validatedData['genres']);
            $genreIds = [];
            foreach ($genreNames as $name) {
                $genre = Genre::firstOrCreate(['name' => trim($name)]);
                $genreIds[] = $genre->id;
            }
            $movie->genres()->sync($genreIds);
        }

        return response()->json([
            'success' => true,
            'message' => $existingMovie ? 'Movie updated successfully' : 'Movie created successfully',
            'movie' => $movie
        ]);
    }
}
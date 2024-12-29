<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\MovieSource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class MovieController extends Controller
{
    public function index()
    {
        // Get latest movies
        $latestMovies = Movie::latest()
            ->take(12)
            ->get();

        // Get highlighted movies (ordered by rating)
        $highlightedMovies = Movie::where('status', 'published')
            ->take(12)
            ->orderBy('rating', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get theaters movies (movies with genre "Phim chiếu rạp")
        $theatersMovies = Movie::whereHas('genres', function($q) {
                $q->where('slug', 'phim-chieu-rap');
            })
            ->where('status', 'published')
            ->latest()
            ->take(12)
            ->get();

        // Get TV series (movies with category "Phim bộ")
        $tvSeries = Movie::where('category_id', Category::where('slug', 'phim-bo')->first()->id)
            ->where('status', 'published')
            ->latest()
            ->take(12)
            ->get();

        // Get single movies (movies with category "Phim lẻ")
        $movies = Movie::where('category_id', Category::where('slug', 'phim-le')->first()->id)
            ->where('status', 'published')
            ->latest()
            ->take(12)
            ->get();

        // Get latest articles
        $latestArticles = Article::latest()
            ->take(5)
            ->get();

        $SEOData = new SEOData(
            title: setting('site_name'),
            description: setting('site_description'),
            image: Storage::url(setting('site_og_image')),
            tags: explode(',', setting('site_meta_keywords'))
        );

        return view('movies.index', compact(
            'latestMovies',
            'highlightedMovies',
            'theatersMovies',
            'tvSeries',
            'movies',
            'latestArticles',
            'SEOData'
        ));
    }

    /**
     * Display the specified movie.
     *
     * @param  string  $slug
     * @return \Illuminate\View\View
     */
    public function show($slug)
    {
        // Get movie with essential relationships
        $movie = Movie::with([
            'sources',
            'genres',
            'category',
            'uploader:id,name',
        ])->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Increment views count using cache to prevent hammering the database
        Cache::remember("movie_view_{$movie->id}_" . now()->format('Y-m-d'), 60 * 60, function () use ($movie) {
            DB::table('movies')
                ->where('id', $movie->id)
                ->increment('views_count');
            return true;
        });

        // Get related movies based on genres and category
        $relatedMovies = Cache::remember("related_movies_{$movie->id}", 60 * 30, function () use ($movie) {
            return Movie::where('id', '!=', $movie->id)
                ->where('status', 'published')
                ->where(function ($query) use ($movie) {
                    // Match by category
                    $query->where('category_id', $movie->category_id)
                        // Or match by genres
                        ->orWhereHas('genres', function ($q) use ($movie) {
                            $q->whereIn('genres.id', $movie->genres->pluck('id'));
                        });
                })
                ->select([
                    'id',
                    'title',
                    'slug',
                    'thumbnail',
                    'duration',
                    'release_year',
                    'views_count'
                ])
                ->withCount('sources')
                ->having('sources_count', '>', 0)
                ->orderBy('views_count', 'desc')
                ->take(5)
                ->get();
        });

        // Get movie viewing statistics for the last 30 days
        $viewStats = null;
        if (auth()->check() && auth()->user()->hasRole('admin')) {
            $viewStats = Cache::remember("movie_stats_{$movie->id}", 60 * 60, function () use ($movie) {
                return DB::table('movie_views')
                    ->where('movie_id', $movie->id)
                    ->where('viewed_at', '>=', now()->subDays(30))
                    ->select(
                        DB::raw('DATE(viewed_at) as date'),
                        DB::raw('COUNT(*) as views')
                    )
                    ->groupBy('date')
                    ->orderBy('date', 'desc')
                    ->get();
            });
        }

        // Mark movie as recently viewed for the user
        if (auth()->check()) {
            $recentlyViewed = Cache::get('recently_viewed_' . auth()->id(), []);
            if (!in_array($movie->id, $recentlyViewed)) {
                array_unshift($recentlyViewed, $movie->id);
                $recentlyViewed = array_slice($recentlyViewed, 0, 10); // Keep only last 10
                Cache::put('recently_viewed_' . auth()->id(), $recentlyViewed, now()->addDays(30));
            }
        }

        // Log detailed view information if enabled in config
        if (config('movies.log_detailed_views', false)) {
            $this->logMovieView($movie);
        }

        return view('movies.show', compact('movie', 'relatedMovies', 'viewStats'))
            ->with([
                'ogTitle' => $movie->meta_title ?: $movie->title,
                'ogDescription' => $movie->meta_description ?: substr($movie->description, 0, 160),
                'ogImage' => $movie->thumbnail,
            ]);
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $year = $request->get('year');
        $genre = $request->get('genre');

        // Extract country
        if (preg_match('/country:([^,]+)/', $query, $matches)) {
            $countryName = trim($matches[1]);
            $query = trim(str_replace("country:{$countryName}", '', $query));
        } else {
            $countryName = null;
        }

        // Start with Scout search
        $search = Movie::search($query)
            ->query(function ($builder) use ($year, $genre, $countryName) {
                $builder->with(['genres', 'category']);

                if (!empty($countryName) && $code = $this->findCountryCode($countryName)) {
                    $builder->where('country', $code);
                }

                if ($year) {
                    $builder->where('release_year', $year);
                }

                if ($genre) {
                    $builder->whereHas('genres', function($q) use ($genre) {
                        $q->where('name', 'like', "%{$genre}%");
                    });
                }
            });

        return response()->json([
            'data' => $search->get()->transform(fn($movie) => [
                'id' => $movie->id,
                'title' => $movie->title,
                'url' => route('movies.show', $movie),
                'thumbnail' => Storage::url($movie->thumbnail),
                'year' => $movie->release_year,
                'description' => Str::limit($movie->description, 100),
                'genres' => $movie->genres->pluck('name'),
                'country' => $movie->country,
                'category' => $movie->category->name,
                'rating' => $movie->rating,
            ])
        ]);
    }

    public function getSource($sourceId)
    {
        $source = MovieSource::findOrFail($sourceId);

        return response()->json([
            'player_html' => view('components.video-player', ['source' => $source])->render()
        ]);
    }

    private function findCountryCode($countryName)
    {
        $countries = json_decode(file_get_contents(base_path('resources/json/countries.json')), true);
        $searchName = strtolower($countryName);

        foreach ($countries as $country) {
            if (str_contains(strtolower($country['name']), $searchName)) {
                return $country['code'];
            }
        }
        return null;
    }

    /**
     * Log detailed information about the movie view.
     *
     * @param  Movie  $movie
     * @return void
     */
    private function logMovieView(Movie $movie)
    {
        $data = [
            'movie_id' => $movie->id,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referer' => request()->header('referer'),
            'viewed_at' => now(),
        ];

        // Queue the logging to prevent impacting response time
        DB::table('movie_views')->insert($data);
    }
}
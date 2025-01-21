<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\MovieSource;
use App\Models\EpisodeSource;
use App\Models\Episode;
use App\Models\Season;
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
        // Get latest movies/series
        $latestContent = Movie::where('status', 'published')
            ->latest()
            ->take(12)
            ->get();

        // Get highlighted content (ordered by rating)
        $highlightedContent = Movie::where('status', 'published')
            ->take(12)
            ->orderBy('rating', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Get theater movies
        $theatersMovies = Movie::whereHas('genres', function($q) {
            $q->where('slug', 'phim-chieu-rap');
        })
            ->where('status', 'published')
            ->where('type', 'single')
            ->latest()
            ->take(12)
            ->get();

        // Get TV series
        $tvSeries = Movie::where('type', 'series')
            ->where('status', 'published')
            ->latest()
            ->take(12)
            ->get();

        // Get single movies
        $movies = Movie::where('type', 'single')
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
            'latestContent',
            'highlightedContent',
            'theatersMovies',
            'tvSeries',
            'movies',
            'latestArticles',
            'SEOData'
        ));
    }

    public function show($slug)
    {
        $movie = Movie::with([
            'sources',
            'genres',
            'category',
            'uploader:id,name',
            'seasons.episodes.sources',
        ])->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // For series type, get the first episode of first season by default
        $currentSeason = null;
        $currentEpisode = null;
        $nextEpisode = null;

        if ($movie->type === 'series' && $movie->seasons->isNotEmpty()) {
            $currentSeason = $movie->seasons->first();
            $currentEpisode = $currentSeason->episodes->first();
            if ($currentEpisode) {
                $nextEpisode = $movie->getNextEpisode($currentEpisode);
            }
        }

        // Increment views count using cache
        $this->incrementViews($movie);

        // Get related content
        $relatedContent = $this->getRelatedContent($movie);

        // Track recently viewed
        $this->trackRecentlyViewed($movie);

        // Log detailed view if enabled
        if (config('movies.log_detailed_views', false)) {
            $this->logMovieView($movie);
        }

        return view('movies.show', compact(
            'movie',
            'currentSeason',
            'currentEpisode',
            'nextEpisode',
            'relatedContent'
        ));
    }

    public function episode($movieSlug, $seasonNumber, $episodeNumber)
    {
        $movie = Movie::with([
            'genres',
            'category',
            'uploader:id,name',
            'seasons.episodes.sources'
        ])->where('slug', $movieSlug)
            ->where('status', 'published')
            ->where('type', 'series')
            ->firstOrFail();

        $currentSeason = $movie->seasons()
            ->where('number', $seasonNumber)
            ->firstOrFail();

        $currentEpisode = $currentSeason->episodes()
            ->where('number', $episodeNumber)
            ->with('sources')
            ->firstOrFail();

        $nextEpisode = $movie->getNextEpisode($currentEpisode);

        // Increment views count
        $this->incrementViews($movie);

        // Get related content
        $relatedContent = $this->getRelatedContent($movie);

        // Track recently viewed
        $this->trackRecentlyViewed($movie);

        return view('movies.show', compact(
            'movie',
            'currentSeason',
            'currentEpisode',
            'nextEpisode',
            'relatedContent'
        ));
    }

    public function search(Request $request)
    {
        $query = $request->get('q');
        $year = $request->get('year');
        $genre = $request->get('genre');
        $type = $request->get('type');

        // Extract country from query
        if (preg_match('/country:([^,]+)/', $query, $matches)) {
            $countryName = trim($matches[1]);
            $query = trim(str_replace("country:{$countryName}", '', $query));
        } else {
            $countryName = null;
        }

        // Start with Scout search
        $search = Movie::search($query)
            ->query(function ($builder) use ($year, $genre, $countryName, $type) {
                $builder->with(['genres', 'category'])
                    ->where('status', 'published');

                if ($type) {
                    $builder->where('type', $type);
                }

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

        $results = $search->get()->transform(function($movie) {
            return [
                'id' => $movie->id,
                'title' => $movie->title,
                'title_en' => $movie->title_en ?? null,
                'url' => route('movies.show', $movie),
                'thumbnail' => $movie->getThumbnail(),
                'year' => $movie->release_year,
                'description' => Str::limit($movie->description, 100),
                'genres' => $movie->genres->pluck('name'),
                'country' => $movie->country,
                'category' => $movie->category->name,
                'rating' => $movie->rating,
                'type' => $movie->type,
                'seasons_count' => $movie->type === 'series' ? $movie->seasons->count() : null
            ];
        });

        return response()->json(['data' => $results]);
    }

    public function getSource($sourceId, $type = 'movie')
    {
        $source = $type === 'movie'
            ? MovieSource::findOrFail($sourceId)
            : EpisodeSource::findOrFail($sourceId);

        return response()->json([
            'player_html' => view('components.video-player', ['source' => $source])->render()
        ]);
    }

    protected function incrementViews($movie)
    {
        Cache::remember(
            "movie_view_{$movie->id}_" . now()->format('Y-m-d'),
            60 * 60,
            function () use ($movie) {
                DB::table('movies')
                    ->where('id', $movie->id)
                    ->increment('views_count');
                return true;
            }
        );
    }

    protected function getRelatedContent($movie)
    {
        return Cache::remember(
            "related_content_{$movie->id}",
            60 * 30,
            function () use ($movie) {
                return Movie::where('id', '!=', $movie->id)
                    ->where('status', 'published')
                    ->where('type', $movie->type)
                    ->where(function ($query) use ($movie) {
                        $query->where('category_id', $movie->category_id)
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
                        'views_count',
                        'type'
                    ])
                    ->when($movie->type === 'series', function($query) {
                        $query->withCount('seasons');
                    })
                    ->take(5)
                    ->get();
            }
        );
    }

    protected function trackRecentlyViewed($movie)
    {
        if (auth()->check()) {
            $recentlyViewed = Cache::get('recently_viewed_' . auth()->id(), []);
            if (!in_array($movie->id, $recentlyViewed)) {
                array_unshift($recentlyViewed, $movie->id);
                $recentlyViewed = array_slice($recentlyViewed, 0, 10);
                Cache::put(
                    'recently_viewed_' . auth()->id(),
                    $recentlyViewed,
                    now()->addDays(30)
                );
            }
        }
    }

    protected function logMovieView($movie)
    {
        DB::table('movie_views')->insert([
            'movie_id' => $movie->id,
            'user_id' => auth()->id(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'referer' => request()->header('referer'),
            'viewed_at' => now(),
        ]);
    }

    private function findCountryCode($countryName)
    {
        $countries = json_decode(
            file_get_contents(base_path('resources/json/countries.json')),
            true
        );
        $searchName = strtolower($countryName);

        foreach ($countries as $country) {
            if (str_contains(strtolower($country['name']), $searchName)) {
                return $country['code'];
            }
        }

        return null;
    }
}
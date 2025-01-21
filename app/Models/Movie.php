<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Scout\Searchable;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Movie extends Model
{
    use Searchable;
    use HasSEO;

    protected $fillable = [
        'title',
        'title_en',
        'slug',
        'description',
        'thumbnail',
        'banner',
        'release_year',
        'duration',
        'status',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'uploaded_by',
        'category_id',
        'country',
        'rating',
        'age_rating',
        'type', // Add this new field: 'single' or 'series'
        'total_episodes', // Optional: for series
        'total_seasons', // Optional: for series
        'crawl_source_url' // Optional: for crawled movies
    ];

    const TYPES = [
        'single' => 'Single Movie',
        'series' => 'TV Series'
    ];

    protected $searchable = [
        'title',
        'description',
        'country'
    ];

    protected $casts = [
        'rating' => 'float',
    ];

    const AGE_RATINGS = ['G', 'PG', 'PG-13', 'R'];
    const STATUSES = [
        'draft' => 'Nháp',
        'published' => 'Hiển thị',
    ];

    public function toSearchableArray()
    {
        return [
            'title' => $this->title,
            'title_en' => $this->title_en,
            'description' => $this->description,
            'country' => $this->country,
            'release_year' => $this->release_year,
            'meta_title' => $this->meta_title,
            'meta_description' => $this->meta_description,
            'meta_keywords' => $this->meta_keywords
        ];
    }

    public static function fetchCountries()
    {
        // Read countries from local JSON file
        $countries = json_decode(file_get_contents(base_path('resources/json/countries.json')), true);

        // Return only the country as code => name
        return collect($countries)->mapWithKeys(function ($country) {
            return [$country['code'] => $country['name']];
        });
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($movie) {
            if (!$movie->slug) {
                // Generate slug from title, handle duplicate slugs
                $slug = Str::slug($movie->title);
                $count = Movie::where('slug', 'like', $slug . '%')->count();
                if ($count) {
                    $slug .= '-' . ($count + 1);
                }

                $movie->slug = $slug;
            }
        });
    }

    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->meta_title && $this->meta_title !== '' ? $this->meta_title : $this->title,
            description: $this->meta_description && $this->meta_description !== '' ? $this->meta_description : $this->description,
            image: Storage::url($this->thumbnail),
            tags: explode(',', $this->meta_keywords)
        );
    }

    public function seasons()
    {
        return $this->hasMany(Season::class)->orderBy('number');
    }

    public function sources()
    {
        return $this->hasMany(MovieSource::class)->when($this->type === 'single', function($query) {
            return $query;
        });
    }

    public function isSeries()
    {
        return $this->type === 'series';
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function genres()
    {
        return $this->belongsToMany(Genre::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getNextEpisode(Episode $currentEpisode)
    {
        // First try to get next episode in current season
        $nextEpisode = Episode::where('season_id', $currentEpisode->season_id)
            ->where('number', '>', $currentEpisode->number)
            ->orderBy('number')
            ->first();

        if (!$nextEpisode) {
            // If no next episode in current season, try first episode of next season
            $nextSeason = Season::where('movie_id', $this->id)
                ->where('number', '>', $currentEpisode->season->number)
                ->orderBy('number')
                ->first();

            if ($nextSeason) {
                $nextEpisode = $nextSeason->episodes()->orderBy('number')->first();
            }
        }

        return $nextEpisode;
    }

    public function getLatestEpisodes($limit = 5)
    {
        if (!$this->isSeries()) {
            return collect();
        }

        return Episode::whereHas('season', function($query) {
            $query->where('movie_id', $this->id);
        })
            ->with(['season', 'sources'])
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getBanner()
    {
        if ($this->banner)
        {
            if (Str::contains($this->banner, 'http'))
            {
                return $this->banner;
            } else {
                return Storage::url($this->banner);
            }
        }
        else
        {
            return null;
        }
    }

    public function getThumbnail()
    {
        if ($this->thumbnail)
        {
            if (Str::contains($this->thumbnail, 'http'))
            {
                return $this->thumbnail;
            } else {
                return Storage::url($this->thumbnail);
            }
        }
        else
        {
            return null;
        }
    }

    public function getPlayerIdAttribute()
    {
        return md5($this->crawl_source_url);
    }
}
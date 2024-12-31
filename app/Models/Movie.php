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
                $movie->slug = Str::slug($movie->title);
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

    public function sources()
    {
        return $this->hasMany(MovieSource::class);
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
}
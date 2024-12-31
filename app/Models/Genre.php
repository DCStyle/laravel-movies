<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Genre extends Model
{
    use HasSEO;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'meta_title',
        'meta_description'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($genre) {
            if (!$genre->slug) {
                $genre->slug = Str::slug($genre->name);
            }
        });
    }

    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->meta_title && $this->meta_title !== '' ? $this->meta_title : $this->name,
            description: $this->meta_description && $this->meta_description !== '' ? $this->meta_description : $this->description
        );
    }

    public function movies()
    {
        return $this->belongsToMany(Movie::class);
    }
}
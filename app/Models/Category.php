<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\AlternateTag;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Category extends Model
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

        static::creating(function ($category) {
            if (!$category->slug) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->meta_title !== '' ? $this->meta_title : $this->name,
            description: $this->meta_description !== '' ? $this->meta_description : $this->description
        );
    }

    public function movies()
    {
        return $this->hasMany(Movie::class);
    }
}
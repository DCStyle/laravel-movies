<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Psy\Util\Str;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Page extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'content',
        'is_active',
        'meta_title',
        'meta_description',
    ];

    protected static function booted()
    {
        static::deleting(function($article) {
            $article->images->each(function($image) {
                Storage::disk('public')->delete($image->path);
                $image->delete();
            });
        });
    }

    public function getDynamicSEOData(): SEOData
    {
        return new SEOData(
            title: $this->meta_title && $this->meta_title !== '' ? $this->meta_title : $this->title,
            description: $this->meta_description && $this->meta_description !== '' ? $this->meta_description : \Illuminate\Support\Str::limit($this->content, 160),
            image: $this->images ? Storage::url($this->images->first()?->path) : null
        );
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
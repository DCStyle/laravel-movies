<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RalphJSmit\Laravel\SEO\Support\HasSEO;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class Article extends Model
{
    use HasSEO;

    protected $fillable = [
        'title', 'slug', 'content', 'image', 'is_published', 'meta_title', 'meta_description'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($article) {
            $article->slug = Str::slug($article->title);
        });
    }

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
            title: $this->meta_title && $this->meta_title !== '' ? $this->meta_title : $this->name,
            description: $this->meta_description && $this->meta_description !== '' ? $this->meta_description : $this->description,
            image: Storage::url($this->image)
        );
    }

    public function thumbnail()
    {
        // First check if article has a direct image relationship
        if ($this->images()->exists()) {
            $firstImage = $this->images()->first();
            if ($firstImage) {
                return Storage::url($firstImage->path);
            }
        }

        // If no direct image, try to extract from content
        if ($this->content) {
            preg_match('/<img.+src=[\'"](?P<src>.+?)[\'"].*>/i', $this->content, $matches);
            if (!empty($matches['src'])) {
                return $matches['src'];
            }
        }

        // If no image found, return null
        return null;
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}

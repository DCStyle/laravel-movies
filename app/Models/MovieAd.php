<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class MovieAd extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type', // 'image' or 'video'
        'content_path',
        'display_time', // percentage of video duration (0-100)
        'duration', // for image ads: how long to show in seconds
        'is_enabled',
        'order',
        'click_url', // optional URL when ad is clicked
    ];

    protected $casts = [
        'display_time' => 'float',
        'duration' => 'integer',
        'is_enabled' => 'boolean',
        'order' => 'integer',
        'last_shown_at' => 'datetime',
    ];

    const TYPES = [
        'image' => 'Image',
        'video' => 'Video',
    ];

    // Custom accessor for content URL
    public function getContentUrlAttribute()
    {
        if ($this->content_path) {
            return Storage::url($this->content_path);
        }

        return null;
    }
}
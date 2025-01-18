<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MovieSource extends Model
{
    protected $fillable = [
        'movie_id',
        'source_type',
        'source_url',
        'quality',
        'is_primary'
    ];

    const TYPES = [
        'direct' => 'Direct Upload',
        'fshare' => 'FShare',
        'gdrive' => 'Google Drive',
        'youtube' => 'YouTube',
        'twitter' => 'Twitter',
        'facebook' => 'Facebook',
        'tiktok' => 'TikTok',
        'embed' => 'Embed Player'
    ];

    protected $casts = [
        'is_primary' => 'boolean'
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
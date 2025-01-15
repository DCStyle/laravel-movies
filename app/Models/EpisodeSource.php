<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EpisodeSource extends Model
{
    protected $fillable = [
        'episode_id',
        'source_type',
        'source_url',
        'quality',
        'is_primary'
    ];

    public function episode()
    {
        return $this->belongsTo(Episode::class);
    }
}
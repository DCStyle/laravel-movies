<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    protected $fillable = [
        'season_id',
        'number',
        'title',
        'description',
        'thumbnail',
        'duration',
        'air_date',
    ];

    protected $casts = [
        'air_date' => 'date',
        'duration' => 'integer',
    ];

    public function season()
    {
        return $this->belongsTo(Season::class);
    }

    public function sources()
    {
        return $this->hasMany(EpisodeSource::class);
    }
}
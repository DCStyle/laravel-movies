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

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
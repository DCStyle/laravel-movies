<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Season extends Model
{
    protected $fillable = [
        'movie_id',
        'number',
        'title',
        'description',
        'poster',
        'release_date',
    ];

    protected $casts = [
        'release_date' => 'date',
    ];

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function episodes()
    {
        return $this->hasMany(Episode::class)->orderBy('number');
    }
}
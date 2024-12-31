<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    protected $fillable = ['name', 'position', 'content', 'is_active', 'order'];

    public const POSITIONS = [
        'header' => 'Header',
        'footer' => 'Footer',
        'sidebar' => 'Sidebar',
    ];
}
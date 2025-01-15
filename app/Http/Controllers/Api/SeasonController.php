<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Season;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    public function show(Season $season)
    {
        return response()->json($season->load('episodes.sources'));
    }
}
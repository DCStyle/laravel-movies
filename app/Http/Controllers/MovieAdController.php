<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MovieAd;
use Illuminate\Http\Request;

class MovieAdController extends Controller
{
    public function getNext(Request $request)
    {
        if ($request->method() === 'OPTIONS') {
            return response()->json([], 200);
        }

        $currentTime = $request->query('time', 0);
        $shownAds = array_filter(explode(',', $request->query('shown', '')));

        $ad = MovieAd::where('is_enabled', true)
            ->where('display_time', '<=', $currentTime)
            ->when(!empty($shownAds), function($query) use ($shownAds) {
                $query->whereNotIn('id', $shownAds);
            })
            ->orderBy('display_time')
            ->first();

        if ($ad) {
            $ad->update(['last_shown_at' => now()]);
            return response()->json([
                'overlay' => [
                    'id' => $ad->id,
                    'type' => $ad->type,
                    'content_url' => url($ad->content_url),
                    'click_url' => $ad->click_url,
                    'duration' => $ad->duration,
                    'display_time' => $ad->display_time,
                ]
            ])->header('Access-Control-Allow-Origin', '*');
        }

        return response()->json(['overlay' => null])
            ->header('Access-Control-Allow-Origin', '*');
    }
}
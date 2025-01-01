<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\MovieAd;
use Illuminate\Http\Request;

class MovieAdController extends Controller
{
    public function getNext(Request $request)
    {
        $currentTime = $request->query('time', 0);
        $shownAds = array_filter(explode(',', $request->query('shown', '')));

        // Get the next eligible ad that should be shown
        $ad = MovieAd::where('is_enabled', true)
            ->where('display_time', '<=', $currentTime)
            ->when(!empty($shownAds), function($query) use ($shownAds) {
                // Exclude already shown ads
                $query->whereNotIn('id', $shownAds);
            })
            ->orderBy('display_time')
            ->first();

        if ($ad) {
            // Update last shown timestamp
            $ad->update(['last_shown_at' => now()]);

            return response()->json([
                'overlay' => [
                    'id' => $ad->id,
                    'type' => $ad->type,
                    'content_url' => $ad->content_url,
                    'click_url' => $ad->click_url,
                    'duration' => $ad->duration,
                    'display_time' => $ad->display_time,
                ]
            ]);
        }

        return response()->json(['ad' => null]);
    }
}
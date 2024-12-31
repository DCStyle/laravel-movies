<?php

namespace App\Services;

use App\Models\Ad;

class AdService {
    public function getAds($position)
    {
        return Ad::where('position', $position)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();
    }
}
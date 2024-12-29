<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Movie;
use RalphJSmit\Laravel\SEO\Support\SEOData;

class CountryController extends Controller
{
    private function getAllowedCountries()
    {
        // Return an array of allowed countries
        // Format: [country_code => [name => country_name, code => ISO2_code]]
        return [
            'phim-au-my' => [
                'name' => 'Phim Âu Mỹ',
                'code' => 'US'
            ],
            'phim-nhat-ban' => [
                'name' => 'Phim Nhật Bản',
                'code' => 'JP'
            ],
            'phim-thai-lan' => [
                'name' => 'Phim Thái Lan',
                'code' => 'TH'
            ],
            'phim-indonesia' => [
                'name' => 'Phim Indonesia',
                'code' => 'ID'
            ],
            'phim-han-quoc' => [
                'name' => 'Phim Hàn Quốc',
                'code' => 'KR'
            ],
            'phim-dai-loan' => [
                'name' => 'Phim Đài Loan',
                'code' => 'TW'
            ],
            'phim-an-do' => [
                'name' => 'Phim Ấn Độ',
                'code' => 'IN'
            ],
            'phim-singapore' => [
                'name' => 'Phim Singapore',
                'code' => 'SG'
            ],
            'phim-trung-quoc' => [
                'name' => 'Phim Trung Quốc',
                'code' => 'CN'
            ],
            'phim-hong-kong' => [
                'name' => 'Phim Hồng Kông',
                'code' => 'HK'
            ],
            'phim-philippines' => [
                'name' => 'Phim Philippines',
                'code' => 'PH'
            ],
        ];
    }

    public function show($country)
    {
        $allowedCountries = $this->getAllowedCountries();
        if (!isset($allowedCountries[$country])) {
            abort(404);
        }

        $countryName = $allowedCountries[$country]['name'];
        $countryCode = $allowedCountries[$country]['code'];

        // Get paginated movies by country code
        $latestMovies = Movie::where('country', $countryCode)
            ->where('status', 'published')
            ->latest()
            ->paginate(12);

        $SEOData = new SEOData(
            title: 'Phim ' . $countryName,
            description: 'Danh sách phim ' . $countryName,
            tags: ['phim', 'phim ' . $countryName],
        );

        return view('countries.show', compact(
            'latestMovies',
            'countryName',
            'countryCode',
            'SEOData',
        ));
    }
}
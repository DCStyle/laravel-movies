<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Category;
use App\Models\Movie;

class UpdateMoviesToSeries extends Command
{
    protected $signature = 'movies:update-to-series';
    protected $description = 'Update movies in "Phim bộ" category to series type';

    public function handle()
    {
        $category = Category::where('name', 'Phim bộ')->first();

        if (!$category) {
            $this->error('Category "Phim bộ" not found');
            return;
        }

        $count = Movie::where('category_id', $category->id)
            ->update([
                'type' => 'series',
                'total_seasons' => 1
            ]);

        $this->info("Updated {$count} movies to series type");
    }
}
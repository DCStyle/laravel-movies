<?php

namespace App\Console\Commands;

use App\Models\Movie;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Symfony\Component\DomCrawler\Crawler;

class UpdateMovieEnglishTitles extends Command
{
    protected $signature = 'movies:update-english-titles';
    protected $description = 'Update English titles for movies by crawling their source URLs';

    public function handle()
    {
        $movies = Movie::whereNotNull('crawl_source_url')
            ->whereNull('title_en')
            ->get();

        $this->info("Found {$movies->count()} movies to process.");
        $bar = $this->output->createProgressBar($movies->count());

        foreach ($movies as $movie) {
            try {
                // Add a small delay to prevent overwhelming the server
                usleep(500000); // 0.5 second delay

                $response = Http::get($movie->crawl_source_url);

                if ($response->successful()) {
                    $crawler = new Crawler($response->body());

                    // Try to find the .valor element
                    $englishTitle = $crawler->filter('.valor')->text('');

                    if ($englishTitle) {
                        $movie->update([
                            'title_en' => trim($englishTitle)
                        ]);

                        $this->info("\nUpdated {$movie->title} with English title: {$englishTitle}");
                    } else {
                        $this->warn("\nNo English title found for movie: {$movie->title}");
                    }
                } else {
                    $this->error("\nFailed to fetch URL for movie: {$movie->title}");
                }
            } catch (\Exception $e) {
                $this->error("\nError processing movie {$movie->title}: {$e->getMessage()}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->info("\nCommand completed!");
    }
}
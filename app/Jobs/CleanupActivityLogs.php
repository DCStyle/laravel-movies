<?php

namespace App\Jobs;

use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class CleanupActivityLogs implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $retentionDays = config('activity-log.retention_days');
        $chunkSize = config('activity-log.cleanup_chunk_size');
        $excludedTypes = config('activity-log.excluded_types', []);

        $date = Carbon::now()->subDays($retentionDays);

        try {
            $totalDeleted = 0;

            // Delete in chunks to avoid memory issues
            do {
                $deleted = Activity::where('created_at', '<', $date)
                    ->whereNotIn('type', $excludedTypes)
                    ->take($chunkSize)
                    ->delete();

                $totalDeleted += $deleted;

                // Add a small delay to prevent overwhelming the database
                if ($deleted > 0) {
                    usleep(100000); // 0.1 second
                }
            } while ($deleted > 0);

            Log::info("Activity log cleanup completed. Deleted {$totalDeleted} records.");

            // Optionally, optimize the table after large deletions
            if ($totalDeleted > 1000) {
                \DB::statement('OPTIMIZE TABLE activities');
            }
        } catch (\Exception $e) {
            Log::error("Activity log cleanup failed: " . $e->getMessage());
            throw $e;
        }
    }

    public function failed(\Throwable $exception)
    {
        Log::error("Activity log cleanup job failed: " . $exception->getMessage());
    }
}
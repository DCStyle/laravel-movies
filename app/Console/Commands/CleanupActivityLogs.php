<?php

namespace App\Console\Commands;

use App\Models\Activity;
use Illuminate\Console\Command;
use Carbon\Carbon;

class CleanupActivityLogs extends Command
{
    protected $signature = 'logs:cleanup {--days=30 : Number of days to keep logs}';
    protected $description = 'Clean up old activity logs';

    public function handle()
    {
        $days = $this->option('days');
        $date = Carbon::now()->subDays($days);

        $count = Activity::where('created_at', '<', $date)->delete();

        $this->info("Deleted {$count} activity logs older than {$days} days.");
    }
}
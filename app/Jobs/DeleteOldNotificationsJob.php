<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Notification;
use Carbon\Carbon;

class DeleteOldNotificationsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Delete notifications that are older than 7 days
        // This will also delete related notification_targets due to cascade on delete
        $sevenDaysAgo = Carbon::now()->subDays(7);
        Notification::where('created_at', '<', $sevenDaysAgo)->delete();
    }
}

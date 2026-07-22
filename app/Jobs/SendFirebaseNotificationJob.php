<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Notification;
use App\Models\Device;
use App\Services\FirebaseService;

class SendFirebaseNotificationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $notification;
    public $userIds;

    /**
     * Create a new job instance.
     */
    public function __construct(Notification $notification, array $userIds)
    {
        $this->notification = $notification;
        $this->userIds = $userIds;
    }

    /**
     * Execute the job.
     */
    public function handle(FirebaseService $firebaseService): void
    {
        // Chunking the devices to avoid Firebase limits (max 500 per multicast)
        Device::where('project_id', $this->notification->project_id)
            ->whereIn('user_id', $this->userIds)
            ->whereNotNull('token')
            ->chunk(500, function ($devices) use ($firebaseService) {
                $tokens = $devices->pluck('token')->toArray();
                
                $result = $firebaseService->sendMulticast(
                    $tokens,
                    $this->notification->title,
                    $this->notification->body,
                    $this->notification->data
                );

                if (!empty($result['invalid_tokens'])) {
                    // Cleanup invalid tokens if requested (optional)
                    Device::whereIn('token', $result['invalid_tokens'])->delete();
                }
            });
    }
}

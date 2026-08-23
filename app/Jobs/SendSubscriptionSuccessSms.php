<?php

namespace App\Jobs;

use App\Services\SemaphoreService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class SendSubscriptionSuccessSms implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $phone,
        public string $message
    ) {}

    /**
     * Execute the job.
     */
    public function handle(SemaphoreService $smsService): void
    {
        $smsService->sendSms($this->phone, $this->message);
    }
}

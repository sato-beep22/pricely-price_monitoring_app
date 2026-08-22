<?php

namespace App\Listeners;

use App\Events\CeilingPriceUpdated;
use App\Models\User;
use App\Services\SemaphoreService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendCeilingPriceAlertSms implements ShouldQueue
{
    use InteractsWithQueue;

    /**
     * Create the event listener.
     */
    public function __construct(protected SemaphoreService $smsService) {}

    /**
     * Handle the event.
     */
    public function handle(CeilingPriceUpdated $event): void
    {
        $updates = $event->updates;

        /** @var Collection<int, User> $buyers */
        $buyers = User::where('role', 'buyer')
            ->whereNotNull('phone')
            ->whereNotNull('phone_verified_at')
            ->where('sms_notifications_enabled', true)
            ->get();

        if ($buyers->isEmpty()) {
            return;
        }

        $message = $this->buildMessage($updates);

        foreach ($buyers as $buyer) {
            Log::info("Sending ceiling price SMS to buyer ID {$buyer->id} ({$buyer->phone}).");
            $this->smsService->sendSms($buyer->phone, $message);
        }
    }

    /**
     * Build the SMS message body from the ceiling price updates.
     *
     * @param  array<int, array{crop_name: string, specification: string|null, old_max_price: string|null, new_max_price: string}>  $updates
     */
    protected function buildMessage(array $updates): string
    {
        $lines = [];
        $lines[] = 'Pricely Alerto: Bag-ong price ceiling ang itinakda ng gobyerno:';

        foreach ($updates as $update) {
            $specStr = $update['specification'] ? " ({$update['specification']})" : '';

            if ($update['old_max_price'] !== null && $update['old_max_price'] !== $update['new_max_price']) {
                $priceStr = 'mula P'.number_format((float) $update['old_max_price'], 2)
                    .' to P'.number_format((float) $update['new_max_price'], 2).'/kg';
            } else {
                $priceStr = 'Max P'.number_format((float) $update['new_max_price'], 2).'/kg';
            }

            $lines[] = "- {$update['crop_name']}{$specStr}: {$priceStr}";
        }

        $lines[] = 'Bisitahin ang app para sa detalye.';

        return implode("\n", $lines);
    }
}

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

        // Extract valid phone numbers
        $phoneNumbers = $buyers->pluck('phone')->filter()->unique()->values()->all();

        // Semaphore allows bulk sending by separating numbers with a comma.
        // Chunk by 500 to be safe and avoid excessively large payloads.
        $chunks = array_chunk($phoneNumbers, 500);

        foreach ($chunks as $chunk) {
            $bulkNumbers = implode(',', $chunk);
            Log::info("Sending bulk ceiling price SMS to " . count($chunk) . " buyers.");
            $this->smsService->sendSms($bulkNumbers, $message);
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
        $lines[] = 'Pricely Alert: Bagong price ceiling ang itinakda ng Department of Agriculture:';

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

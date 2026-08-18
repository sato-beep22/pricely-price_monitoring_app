<?php

namespace App\Listeners;

use App\Events\PriceUpdated;
use App\Services\IprogService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPriceUpdateSms implements ShouldQueue
{
    use InteractsWithQueue;

    protected IprogService $smsService;

    /**
     * Create the event listener.
     */
    public function __construct(IprogService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Handle the event.
     */
    public function handle(PriceUpdated $event): void
    {
        $shop = $event->shop;
        $updates = $event->updates;

        // Get all active subscriptions for the buyer who owns the shop
        $subscriptions = $shop->user->subscribers()->active()->with('farmer')->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        foreach ($subscriptions as $subscription) {
            $farmer = $subscription->farmer;

            if (! $farmer->phoneVerified()) {
                Log::info("Skipped SMS to farmer ID {$farmer->id} because phone is not verified.");
                continue;
            }

            if (empty($farmer->phone)) {
                Log::info("Skipped SMS to farmer ID {$farmer->id} because no phone number is set.");
                continue;
            }

            // Filter updates based on the farmer's subscribed crop_ids
            $subscribedCropIds = is_array($subscription->crop_ids) ? $subscription->crop_ids : json_decode($subscription->crop_ids, true);
            $subscribedCropIds = $subscribedCropIds ?? [];

            $relevantUpdates = array_filter($updates, function($u) use ($subscribedCropIds) {
                return in_array($u['crop_id'], $subscribedCropIds);
            });

            if (empty($relevantUpdates)) {
                continue; // Farmer isn't subscribed to any of the updated crops
            }

            // Build the multi-line message
            $messageLines = [];
            $messageLines[] = "Pricely Update: Mga ka-Farmer! Ang {$shop->name} ay nag-update ng presyo:";
            
            foreach ($relevantUpdates as $u) {
                $specStr = $u['specification'] ? "({$u['specification']})" : "";
                
                $priceStr = "";
                if ($u['old_price'] !== null && $u['old_price'] != $u['new_price']) {
                    $priceStr = "mula P" . number_format($u['old_price'], 2) . " to P" . number_format($u['new_price'], 2) . "/kg";
                } else {
                    $priceStr = "P" . number_format($u['new_price'], 2) . "/kg";
                }
                
                $messageLines[] = "- {$u['crop_name']} {$specStr}: {$priceStr}";
            }
            
            $messageLines[] = "Bisitahin ang app para sa detalye.";
            
            $message = implode("\n", $messageLines);

            $this->smsService->sendSms($farmer->phone, $message);
        }
    }
}

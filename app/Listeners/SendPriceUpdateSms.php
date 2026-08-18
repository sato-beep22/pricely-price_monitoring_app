<?php

namespace App\Listeners;

use App\Events\PriceUpdated;
use App\Services\InfobipService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class SendPriceUpdateSms implements ShouldQueue
{
    use InteractsWithQueue;

    protected InfobipService $infobipService;

    /**
     * Create the event listener.
     */
    public function __construct(InfobipService $infobipService)
    {
        $this->infobipService = $infobipService;
    }

    /**
     * Handle the event.
     */
    public function handle(PriceUpdated $event): void
    {
        $shop = $event->shop;
        $crop = $event->crop;
        $price = $event->price;

        // Get all active subscriptions for the buyer who owns the shop
        $subscriptions = $shop->user->subscribers()->active()->with('farmer')->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $message = sprintf(
            'Pricely Update: %s updated their price for %s to P%s/kg. Visit the map for details.',
            $shop->name,
            $crop->name,
            number_format($price->price_per_kg, 2)
        );

        foreach ($subscriptions as $subscription) {
            $farmer = $subscription->farmer;

            if (! $farmer->phoneVerified()) {
                Log::info("Skipped SMS to farmer ID {$farmer->id} because phone is not verified.");

                continue;
            }

            if (! empty($farmer->phone)) {
                $this->infobipService->sendSms($farmer->phone, $message);
            } else {
                Log::info("Skipped SMS to farmer ID {$farmer->id} because no phone number is set.");
            }
        }
    }
}

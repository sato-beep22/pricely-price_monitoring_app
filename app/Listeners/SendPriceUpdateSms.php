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
        $crop = $event->crop;
        $price = $event->price;

        // Get all active subscriptions for the buyer who owns the shop
        $subscriptions = $shop->user->subscribers()->active()->with('farmer')->get();

        if ($subscriptions->isEmpty()) {
            return;
        }

        $message = sprintf(
            'Pricely Update: Magandang araw mga ka Farmers! Ang shop na %s ay nag update ng presyo ng %s sa halagang P%s/kg. Bisitahin ang mapa para sa detalye.',
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
                $this->smsService->sendSms($farmer->phone, $message);
            } else {
                Log::info("Skipped SMS to farmer ID {$farmer->id} because no phone number is set.");
            }
        }
    }
}

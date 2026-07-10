<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Seed farmer-to-buyer subscriptions.
     */
    public function run(): void
    {
        $farmers = User::where('role', 'farmer')->get();
        $buyers = User::where('role', 'buyer')->get();

        if ($farmers->isEmpty() || $buyers->isEmpty()) {
            return;
        }

        // Each farmer subscribes to 1-2 random buyers
        foreach ($farmers as $farmer) {
            $subscribeTo = $buyers->random(min(2, $buyers->count()));

            foreach ($subscribeTo as $buyer) {
                Subscription::firstOrCreate([
                    'farmer_id' => $farmer->id,
                    'buyer_id' => $buyer->id,
                ], [
                    'is_active' => true,
                ]);
            }
        }
    }
}

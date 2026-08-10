<?php

namespace Database\Seeders;

use App\Models\Crop;
use App\Models\Price;
use App\Models\Shop;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class PriceSeeder extends Seeder
{
    /**
     * Seed 30 days of continuous daily historical price data for forecasting and trend analysis.
     */
    public function run(): void
    {
        // Clear existing price entries for a clean, deterministic dataset
        Price::query()->delete();

        $shops = Shop::all();
        $crops = Crop::all();

        if ($shops->isEmpty() || $crops->isEmpty()) {
            return;
        }

        $daysHistory = 30;
        $now = Carbon::now();

        /**
         * Specification trend definitions:
         * - 'start': Price 30 days ago
         * - 'end': Price today
         * - 'noise': Maximum random daily fluctuation for variety across shops
         */
        $trends = [
            'rice' => [
                'dry' => ['start' => 43.00, 'end' => 50.50, 'noise' => 0.60], // Upward trend -> HOLD recommendation
                'basa' => ['start' => 38.00, 'end' => 32.50, 'noise' => 0.50], // Downward trend -> SELL_NOW recommendation
            ],
            'corn' => [
                'yellow (dry)' => ['start' => 23.50, 'end' => 23.80, 'noise' => 0.40], // Stable trend -> STABLE recommendation
                'yellow (basa)' => ['start' => 18.00, 'end' => 22.00, 'noise' => 0.40], // Upward trend -> HOLD recommendation
                'white' => ['start' => 27.00, 'end' => 23.00, 'noise' => 0.50], // Downward trend -> SELL_NOW recommendation
            ],
            'mung-bean' => [
                'kusapo' => ['start' => 62.00, 'end' => 70.50, 'noise' => 0.80], // Upward trend -> HOLD recommendation
                'bullad' => ['start' => 65.00, 'end' => 65.40, 'noise' => 0.70], // Stable trend -> STABLE recommendation
            ],
        ];

        for ($dayOffset = $daysHistory; $dayOffset >= 0; $dayOffset--) {
            $recordDate = $now->copy()->subDays($dayOffset)->startOfDay()->addHours(8); // Recorded at 8:00 AM daily
            $progress = 1.0 - ($dayOffset / (float) $daysHistory); // 0.0 at start, 1.0 today

            foreach ($crops as $crop) {
                $cropTrends = $trends[$crop->slug] ?? [];
                $specs = array_map('trim', explode(',', $crop->specification));

                foreach ($specs as $spec) {
                    $trendConfig = $cropTrends[$spec] ?? ['start' => 40.00, 'end' => 42.00, 'noise' => 0.50];
                    $baseTrendPrice = $trendConfig['start'] + ($progress * ($trendConfig['end'] - $trendConfig['start']));

                    foreach ($shops as $shopIndex => $shop) {
                        // Shop-specific offset to give realistic geographic variation between shops
                        $shopOffset = (($shopIndex % 3) - 1) * 0.35;
                        // Daily random noise
                        $dailyNoise = (fake()->randomFloat(2, -1, 1)) * $trendConfig['noise'];
                        $finalPrice = round(max(5.00, $baseTrendPrice + $shopOffset + $dailyNoise), 2);

                        Price::create([
                            'shop_id' => $shop->id,
                            'crop_id' => $crop->id,
                            'specification' => $spec,
                            'price_per_kg' => $finalPrice,
                            'recorded_at' => $recordDate,
                            'source' => 'system',
                            'created_at' => $recordDate,
                            'updated_at' => $recordDate,
                        ]);
                    }
                }
            }
        }
    }
}

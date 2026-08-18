<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Crop;
use App\Models\Shop;
use App\Models\Price;
use Carbon\Carbon;

class HistoricalPriceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $crops = Crop::all();
        $shops = Shop::all();

        if ($crops->isEmpty() || $shops->isEmpty()) {
            $this->command->info('No crops or shops found. Please create crops and shops first.');
            return;
        }

        $startDate = Carbon::now()->subDays(180);
        $endDate = Carbon::now();

        foreach ($shops as $shop) {
            foreach ($crops as $crop) {
                // Base price for realistic variation
                $basePrice = rand(20, 100);
                
                $currentDate = clone $startDate;
                while ($currentDate <= $endDate) {
                    // Random walk for price (trend)
                    $change = rand(-2, 3);
                    $basePrice += $change;
                    if ($basePrice < 10) $basePrice = 10;

                    Price::create([
                        'shop_id' => $shop->id,
                        'crop_id' => $crop->id,
                        'specification' => 'Standard',
                        'price_per_kg' => $basePrice,
                        'recorded_at' => $currentDate->copy(),
                        'source' => 'testing',
                    ]);

                    // Add an entry for every 1 to 4 days to simulate realistic sporadic updates
                    $currentDate->addDays(rand(1, 4));
                }
            }
        }

        $this->command->info('Historical prices seeded successfully for testing forecasting.');
    }
}

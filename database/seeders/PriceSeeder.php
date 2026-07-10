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
     * Seed 3 months of historical price data for forecasting demos.
     *
     * @var array<string, array{base: float, variance: float}>
     */
    private array $cropBasePrices = [
        'rice' => ['base' => 45.00, 'variance' => 5.00],
        'corn' => ['base' => 22.00, 'variance' => 3.00],
        'mung-bean' => ['base' => 65.00, 'variance' => 8.00],
    ];

    /**
     * Seed the prices table.
     */
    public function run(): void
    {
        $shops = Shop::all();
        $crops = Crop::all();
        $startDate = Carbon::now()->subMonths(3);
        $endDate = Carbon::now();

        foreach ($shops as $shop) {
            foreach ($crops as $crop) {
                $specs = array_map('trim', explode(',', $crop->specification));
                
                foreach ($specs as $spec) {
                    $config = $this->cropBasePrices[$crop->slug] ?? ['base' => 40.00, 'variance' => 5.00];
                    $currentPrice = $config['base'];

                    // Generate a price entry every 3 days for realistic trends
                    $date = $startDate->copy();
                    while ($date->lte($endDate)) {
                        // Random walk with mean reversion for realistic price movement
                        $change = (fake()->randomFloat(2, -1, 1) * $config['variance'] * 0.3);
                        $meanReversion = ($config['base'] - $currentPrice) * 0.1;
                        $currentPrice = max(
                            $config['base'] * 0.7,
                            min($config['base'] * 1.3, $currentPrice + $change + $meanReversion)
                        );

                        Price::create([
                            'shop_id' => $shop->id,
                            'crop_id' => $crop->id,
                            'specification' => $spec,
                            'price_per_kg' => round($currentPrice, 2),
                            'recorded_at' => $date->copy(),
                        ]);

                        $date->addDays(fake()->numberBetween(2, 4));
                    }
                }
            }
        }
    }
}

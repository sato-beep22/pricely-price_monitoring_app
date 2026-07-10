<?php

namespace Database\Seeders;

use App\Models\CeilingPrice;
use App\Models\Crop;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CeilingPriceSeeder extends Seeder
{
    /**
     * Seed DA-based ceiling prices for each crop.
     */
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();

        if (! $admin) {
            return;
        }

        $ceilingPrices = [
            ['slug' => 'rice', 'specification' => 'dry', 'max_price' => 55.00, 'notes' => 'DA recommended ceiling price for well-milled palay.'],
            ['slug' => 'rice', 'specification' => 'basa', 'max_price' => 45.00, 'notes' => 'DA recommended ceiling price for fresh palay.'],
            ['slug' => 'corn', 'specification' => 'yellow (dry)', 'max_price' => 30.00, 'notes' => 'DA recommended ceiling price for yellow mais.'],
            ['slug' => 'corn', 'specification' => 'white', 'max_price' => 35.00, 'notes' => 'DA recommended ceiling price for white mais.'],
            ['slug' => 'mung-bean', 'specification' => 'kusapo', 'max_price' => 85.00, 'notes' => 'DA recommended ceiling price for munggo.'],
        ];

        foreach ($ceilingPrices as $data) {
            $crop = Crop::where('slug', $data['slug'])->first();

            if ($crop) {
                CeilingPrice::create([
                    'crop_id' => $crop->id,
                    'admin_id' => $admin->id,
                    'specification' => $data['specification'],
                    'max_price' => $data['max_price'],
                    'effective_date' => Carbon::now()->startOfMonth(),
                    'notes' => $data['notes'],
                ]);
            }
        }
    }
}

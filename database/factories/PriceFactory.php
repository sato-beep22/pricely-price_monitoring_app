<?php

namespace Database\Factories;

use App\Models\Crop;
use App\Models\Price;
use App\Models\Shop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Price>
 */
class PriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shop_id' => Shop::factory(),
            'crop_id' => Crop::factory(),
            'price_per_kg' => fake()->randomFloat(2, 20, 80),
            'recorded_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}

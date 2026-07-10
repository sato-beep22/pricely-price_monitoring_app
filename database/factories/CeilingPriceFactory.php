<?php

namespace Database\Factories;

use App\Models\CeilingPrice;
use App\Models\Crop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CeilingPrice>
 */
class CeilingPriceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'crop_id' => Crop::factory(),
            'admin_id' => User::factory()->admin(),
            'max_price' => fake()->randomFloat(2, 40, 100),
            'effective_date' => fake()->dateTimeBetween('-1 month', '+1 month'),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}

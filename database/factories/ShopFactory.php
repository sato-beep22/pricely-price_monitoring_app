<?php

namespace Database\Factories;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Shop>
 */
class ShopFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory()->buyer(),
            'name' => fake()->company().' Agri-Trade',
            'address' => fake()->address(),
            'latitude' => fake()->latitude(14.3, 14.8),
            'longitude' => fake()->longitude(120.8, 121.2),
            'description' => fake()->sentence(),
            'is_active' => true,
            'classification' => fake()->randomElement([
                'trader', 'miller', 'wholesaler', 'retailer', 'government', 'cooperative',
            ]),
        ];
    }

    /**
     * Indicate the shop is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}

<?php

namespace Database\Factories;

use App\Models\Crop;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Crop>
 */
class CropFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->randomElement(['Rice', 'Corn', 'Mung Bean']),
            'slug' => fn (array $attributes) => str($attributes['name'])->slug()->toString(),
            'unit' => 'kg',
        ];
    }
}

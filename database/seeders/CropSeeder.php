<?php

namespace Database\Seeders;

use App\Models\Crop;
use Illuminate\Database\Seeder;

class CropSeeder extends Seeder
{
    /**
     * Seed the crops table with the three supported crop types.
     */
    public function run(): void
    {
        $crops = [
            ['name' => 'Palay', 'slug' => 'rice', 'unit' => 'kg', 'specification' => 'dry, basa'],
            ['name' => 'Mais', 'slug' => 'corn', 'unit' => 'kg', 'specification' => 'yellow (dry), yellow (basa), white'],
            ['name' => 'Munggo', 'slug' => 'mung-bean', 'unit' => 'kg', 'specification' => 'kusapo, bullad'],
        ];

        foreach ($crops as $crop) {
            Crop::updateOrCreate(['slug' => $crop['slug']], $crop);
        }
    }
}

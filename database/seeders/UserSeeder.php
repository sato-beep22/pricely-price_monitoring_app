<?php

namespace Database\Seeders;

use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed demo users: 1 admin, 3 buyers with shops, 5 farmers.
     */
    public function run(): void
    {
        // Admin
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@pricely.test',
            'password' => Hash::make('password'),
            'phone' => '09171234567',
        ]);

        // Buyers with shops (coordinates near Nueva Ecija / Central Luzon rice belt)
        $buyerShops = [
            [
                'user' => ['name' => 'Juan Dela Cruz', 'email' => 'buyer1@pricely.test', 'phone' => '09181234567'],
                'shop' => ['name' => 'Dela Cruz Rice Trading', 'address' => 'Cabanatuan City, Nueva Ecija', 'latitude' => 15.4868, 'longitude' => 120.9734, 'description' => 'Wholesale rice and corn buyer since 2010.'],
            ],
            [
                'user' => ['name' => 'Maria Santos', 'email' => 'buyer2@pricely.test', 'phone' => '09191234567'],
                'shop' => ['name' => 'Santos Agri-Products', 'address' => 'San Jose City, Nueva Ecija', 'latitude' => 15.7889, 'longitude' => 120.9909, 'description' => 'Premium quality crops at fair prices.'],
            ],
            [
                'user' => ['name' => 'Pedro Reyes', 'email' => 'buyer3@pricely.test', 'phone' => '09201234567'],
                'shop' => ['name' => 'Reyes Grain Center', 'address' => 'Muñoz, Nueva Ecija', 'latitude' => 15.7148, 'longitude' => 120.9028, 'description' => 'Government-accredited grain buyer.'],
            ],
        ];

        foreach ($buyerShops as $data) {
            $buyer = User::factory()->buyer()->create(array_merge(
                $data['user'],
                ['password' => Hash::make('password')]
            ));

            Shop::create(array_merge(
                $data['shop'],
                ['user_id' => $buyer->id]
            ));
        }

        // Farmers
        User::factory()->farmer()->count(5)->create([
            'password' => Hash::make('password'),
        ]);
    }
}

<?php

use App\Models\Shop;
use App\Services\PriceForecastService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Provide shop data with latest prices for the Leaflet Map
Route::get('/shops', function () {
    $shops = Shop::with(['user', 'prices' => function($query) {
        // Fetch all prices
    }])
    ->where('is_active', true)
    ->get();

    $formattedShops = $shops->map(function ($shop) {
        $latestPrices = $shop->prices->groupBy(function ($price) {
            return $price->crop_id . '_' . $price->specification;
        })->map(function ($prices) {
            return $prices->sortByDesc('recorded_at')->first();
        })->values();

        return [
            'id' => $shop->id,
            'user_id' => $shop->user_id,
            'name' => $shop->name,
            'address' => $shop->address,
            'latitude' => $shop->latitude,
            'longitude' => $shop->longitude,
            'owner' => $shop->user->name,
            'phone' => $shop->user->phone,
            'prices' => $latestPrices->map(function ($p) {
                $specLabel = $p->specification ? ' (' . ucfirst($p->specification) . ')' : '';
                return [
                    'crop_name' => ($p->crop->name ?? 'Unknown') . $specLabel,
                    'price' => $p->price_per_kg,
                    'date' => $p->recorded_at->format('M d, Y'),
                ];
            })
        ];
    });

    return response()->json($formattedShops);
});

// Provide forecast data for ApexCharts
Route::get('/forecast/{crop_id}', function ($crop_id, Request $request, PriceForecastService $forecastService) {
    $spec = $request->query('spec');
    $data = $forecastService->getForecast($crop_id, $spec);
    return response()->json($data);
});

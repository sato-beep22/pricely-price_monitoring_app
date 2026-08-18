<?php

use App\Models\Shop;
use App\Services\PriceForecastService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Provide shop data with latest prices for the Leaflet Map
Route::get('/shops', function () {
    $shops = Shop::with([
        'user' => function ($query) {
            $query->withCount('subscribers');
        },
        'prices' => function ($query) {
            // Fetch all prices
        },
    ])
        ->where('is_active', true)
        ->get();

    $formattedShops = $shops->map(function ($shop) {
        $latestPrices = $shop->prices->groupBy(function ($price) {
            return $price->crop_id.'_'.$price->specification;
        })->map(function ($prices) {
            return $prices->sortByDesc('recorded_at')->first();
        })->values();

        $latestPrice = $shop->prices->sortByDesc('recorded_at')->first();

        return [
            'id' => $shop->id,
            'user_id' => $shop->user_id,
            'name' => $shop->name,
            'address' => $shop->address,
            'latitude' => $shop->latitude,
            'longitude' => $shop->longitude,
            'owner' => $shop->user->name,
            'phone' => $shop->user->phone,
            'classification' => $shop->classification,
            'views' => $shop->views,
            'subscribers_count' => $shop->user->subscribers_count ?? 0,
            'latest_price_at' => $latestPrice?->recorded_at?->toISOString(),
            'prices' => $latestPrices->map(function ($p) {
                $specLabel = $p->specification ? ' ('.ucfirst($p->specification).')' : '';

                return [
                    'crop_name' => ($p->crop->name ?? 'Unknown').$specLabel,
                    'price' => $p->price_per_kg,
                    'date' => $p->recorded_at->format('M d, Y'),
                ];
            }),
        ];
    });

    return response()->json($formattedShops);
});

// Increment shop view count (called when side panel is opened)
Route::post('/shops/{id}/view', function (int $id) {
    Shop::where('id', $id)->where('is_active', true)->increment('views');

    return response()->json(['ok' => true]);
});

// Provide forecast data for ApexCharts
Route::get('/forecast/{crop_id}', function ($crop_id, Request $request, PriceForecastService $forecastService) {
    $spec = $request->query('spec');
    $data = $forecastService->getForecast($crop_id, $spec);

    return response()->json($data)->withHeaders([
        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        'Pragma' => 'no-cache',
    ]);
});

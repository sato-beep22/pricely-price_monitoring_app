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
        }
    ])
    ->where('is_active', true)
    ->get();

    $shopIds = $shops->pluck('id');

    $allLatestPrices = \App\Models\Price::whereIn('shop_id', $shopIds)
        ->whereIn('id', function ($query) {
            $query->selectRaw('MAX(id)')
                ->from('prices')
                ->groupBy('shop_id', 'crop_id', 'specification');
        })
        ->with('crop')
        ->get()
        ->groupBy('shop_id');

    $formattedShops = $shops->map(function ($shop) use ($allLatestPrices) {
        $latestPrices = $allLatestPrices->get($shop->id, collect());
        
        $latestPrice = $latestPrices->sortByDesc('recorded_at')->first();

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
            })->values(),
        ];
    });

    return response()->json($formattedShops);
});

// Increment shop view count (called when side panel is opened)
Route::post('/shops/{id}/view', function (int $id) {
    Shop::where('id', $id)->where('is_active', true)->increment('views');

    return response()->json(['ok' => true]);
});

Route::get('/v2/forecast/{crop_id}', function ($crop_id, Request $request, PriceForecastService $forecastService) {
    $spec = $request->query('spec');
    $data = $forecastService->getForecast($crop_id, $spec);

    return response()->json($data)->withHeaders([
        'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
        'Pragma' => 'no-cache',
    ]);
});

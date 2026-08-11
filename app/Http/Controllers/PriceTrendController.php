<?php

namespace App\Http\Controllers;

use App\Models\Price;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PriceTrendController extends Controller
{
    public function index(Request $request)
    {
        $thirtyDaysAgo = Carbon::now()->subDays(30);

        // Fetch prices using recorded_at
        $allPrices = Price::with('crop')->where('recorded_at', '>=', $thirtyDaysAgo)->orderBy('recorded_at', 'asc')->get();
        
        $categories = [];
        for ($i = 29; $i >= 0; $i--) {
            $categories[] = Carbon::now()->subDays($i)->format('M d');
        }

        $cropNames = $allPrices->pluck('crop.name')->unique();
        $series = [];
        
        foreach ($cropNames as $cropName) {
            $data = [];
            $lastKnownPrice = null;
            for ($i = 29; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i)->format('Y-m-d');
                $avgPrice = $allPrices->where('crop.name', $cropName)
                                      ->filter(function($p) use ($date) {
                                          return Carbon::parse($p->recorded_at)->format('Y-m-d') === $date;
                                      })
                                      ->avg('price_per_kg');
                
                if ($avgPrice !== null) {
                    $lastKnownPrice = round($avgPrice, 2);
                }
                $data[] = $lastKnownPrice;
            }

            // Backfill any leading nulls with the first available price in the 30-day window
            $firstValid = collect($data)->first(function ($val) { return $val !== null; });
            $data = array_map(function ($val) use ($firstValid) {
                return $val === null ? ($firstValid ?? 0) : $val;
            }, $data);

            $series[] = [
                'name' => $cropName,
                'data' => $data
            ];
        }

        return response()->json([
            'categories' => $categories,
            'series' => $series
        ]);
    }
}

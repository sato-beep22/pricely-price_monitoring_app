<?php

namespace App\Services;

use App\Models\Price;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class PriceForecastService
{
    /**
     * Get historical price data and simple forecast for a crop.
     * 
     * @param int $cropId The crop ID
     * @param int $daysHistory Number of days of historical data to retrieve
     * @param int $daysForecast Number of days to forecast into the future
     * @return array
     */
    public function getForecast(int $cropId, ?string $spec = null, int $daysHistory = 30, int $daysForecast = 7): array
    {
        $startDate = Carbon::now()->subDays($daysHistory)->startOfDay();
        
        $query = Price::where('crop_id', $cropId)
            ->where('recorded_at', '>=', $startDate);
            
        if ($spec) {
            $query->where('specification', $spec);
        }

        // Get historical prices for the given crop, averaged by day
        $historicalPrices = $query->selectRaw('DATE(recorded_at) as date, AVG(price_per_kg) as avg_price')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($historicalPrices->isEmpty()) {
            return ['dates' => [], 'actual' => [], 'forecast' => []];
        }

        $dates = [];
        $actualPrices = [];
        $forecastPrices = [];

        // Populate historical data
        foreach ($historicalPrices as $record) {
            $dates[] = Carbon::parse($record->date)->format('Y-m-d');
            $actualPrices[] = round($record->avg_price, 2);
            $forecastPrices[] = null; // No forecast for past dates (or you could overlay it)
        }

        // Simple Moving Average (SMA) Forecasting
        // Use the last 5 days to calculate a trend
        $windowSize = min(5, count($actualPrices));
        $recentPrices = array_slice($actualPrices, -$windowSize);
        $lastPrice = end($actualPrices);
        
        // Calculate average daily change
        $totalChange = 0;
        for ($i = 1; $i < count($recentPrices); $i++) {
            $totalChange += ($recentPrices[$i] - $recentPrices[$i - 1]);
        }
        $avgDailyChange = count($recentPrices) > 1 ? $totalChange / (count($recentPrices) - 1) : 0;
        
        // Dampen the change for future forecast so it doesn't go to infinity
        $dampeningFactor = 0.8;

        $currentForecast = $lastPrice;
        $lastDate = Carbon::parse(end($dates));

        // Connect actual to forecast
        $forecastPrices[count($forecastPrices) - 1] = $lastPrice;

        // Generate future forecast dates
        for ($i = 1; $i <= $daysForecast; $i++) {
            $futureDate = $lastDate->copy()->addDays($i);
            $dates[] = $futureDate->format('Y-m-d');
            
            $currentForecast += ($avgDailyChange * pow($dampeningFactor, $i));
            // Ensure price doesn't go below 0
            $currentForecast = max(0, $currentForecast);
            
            $actualPrices[] = null; // No actual price for future
            $forecastPrices[] = round($currentForecast, 2);
        }

        return [
            'dates' => $dates,
            'actual' => $actualPrices,
            'forecast' => $forecastPrices,
        ];
    }
}

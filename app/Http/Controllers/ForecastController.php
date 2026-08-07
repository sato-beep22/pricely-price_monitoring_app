<?php

namespace App\Http\Controllers;

use App\Models\Crop;
use App\Services\PriceForecastService;

class ForecastController extends Controller
{
    /**
     * Show the price forecasting dashboard.
     */
    public function index(PriceForecastService $forecastService)
    {
        $crops = Crop::all();

        $marketSummary = [
            'SELL_NOW' => [],
            'HOLD' => [],
            'STABLE' => [],
        ];

        foreach ($crops as $crop) {
            $specs = array_map('trim', explode(',', $crop->specification));
            foreach ($specs as $spec) {
                if (empty($spec)) {
                    continue;
                }

                $forecast = $forecastService->getForecast($crop->id, $spec);
                $recommendation = $forecast['recommendation'] ?? 'NO_DATA';

                if (array_key_exists($recommendation, $marketSummary)) {
                    $marketSummary[$recommendation][] = [
                        'crop_name' => $crop->name,
                        'spec' => $spec,
                        'trend' => $forecast['trend_pct'],
                    ];
                }
            }
        }

        return view('forecast.index', compact('crops', 'marketSummary'));
    }
}

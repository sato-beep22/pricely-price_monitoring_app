<?php

namespace App\Services;

use App\Models\Price;
use Carbon\Carbon;

class PriceForecastService
{
    /**
     * Get historical price data and simple forecast for a crop,
     * enriched with farmer-friendly insights.
     *
     * @return array{dates: list<string>, actual: list<float|null>, forecast: list<float|null>, forecast_upper: list<float|null>, forecast_lower: list<float|null>, recommendation: string, recommendation_text: string, trend_pct: float, peak_day: array{date: string, price: float}|null, min_day: array{date: string, price: float}|null, accuracy_score: int}
     */
    public function getForecast(int $cropId, ?string $spec = null, int $daysHistory = 30, int $daysForecast = 7): array
    {
        $startDate = Carbon::now()->subDays($daysHistory)->startOfDay();

        $query = Price::where('crop_id', $cropId)
            ->where('recorded_at', '>=', $startDate);

        if ($spec) {
            $query->where('specification', $spec);
        }

        // Daily average historical prices
        $historicalPrices = $query
            ->selectRaw('DATE(recorded_at) as date, AVG(price_per_kg) as avg_price')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        if ($historicalPrices->isEmpty()) {
            return [
                'dates' => [],
                'actual' => [],
                'forecast' => [],
                'forecast_upper' => [],
                'forecast_lower' => [],
                'recommendation' => 'NO_DATA',
                'recommendation_text' => 'Not enough price data is available yet to generate a forecast.',
                'trend_pct' => 0,
                'peak_day' => null,
                'min_day' => null,
                'accuracy_score' => 0,
            ];
        }

        $dates = [];
        $actualPrices = [];
        $forecastPrices = [];

        foreach ($historicalPrices as $record) {
            $dates[] = Carbon::parse($record->date)->format('Y-m-d');
            $actualPrices[] = round((float) $record->avg_price, 2);
            $forecastPrices[] = null;
        }

        // --- Weighted Moving Average (WMA) + Linear Regression Forecasting ---
        // Use up to 14 days of history for a reliable regression slope.
        $windowSize = min(14, count($actualPrices));
        $recentPrices = array_slice($actualPrices, -$windowSize);
        $n = count($recentPrices);
        $lastPrice = (float) end($actualPrices);

        // -- WMA daily change --
        // More-recent days receive linearly higher weights (weight = index + 1).
        $wmaWeightSum = 0;
        $wmaWeightedChange = 0;
        for ($i = 1; $i < $n; $i++) {
            $weight = $i; // weight grows linearly: 1, 2, ..., n-1
            $wmaWeightSum += $weight;
            $wmaWeightedChange += $weight * ($recentPrices[$i] - $recentPrices[$i - 1]);
        }
        $wmaAvgDailyChange = $wmaWeightSum > 0 ? $wmaWeightedChange / $wmaWeightSum : 0;

        // -- Linear Regression slope (ordinary least squares) --
        $sumX = 0.0;
        $sumY = 0.0;
        $sumXY = 0.0;
        $sumX2 = 0.0;
        for ($i = 0; $i < $n; $i++) {
            $x = (float) ($i + 1);
            $y = $recentPrices[$i];
            $sumX += $x;
            $sumY += $y;
            $sumXY += $x * $y;
            $sumX2 += $x * $x;
        }
        $denominator = ($n * $sumX2 - $sumX * $sumX);
        $regressionSlope = $denominator !== 0.0
            ? ($n * $sumXY - $sumX * $sumY) / $denominator
            : 0.0;

        // Blend: 50 % WMA momentum + 50 % regression slope
        $blendedChange = ($wmaAvgDailyChange * 0.5) + ($regressionSlope * 0.5);

        // Hard price floor: forecast must not drop below 50 % of the last known price.
        $priceFloor = $lastPrice * 0.50;

        // Confidence band: ±4 % of last price, widening slightly each day
        $bandBase = $lastPrice * 0.04;

        $currentForecast = $lastPrice;
        $lastDate = Carbon::parse(end($dates));

        // Bridge actual to forecast series at the boundary
        $forecastPrices[count($forecastPrices) - 1] = $lastPrice;

        $forecastUpper = array_fill(0, count($actualPrices) - 1, null);
        $forecastLower = array_fill(0, count($actualPrices) - 1, null);
        $forecastUpper[] = $lastPrice;
        $forecastLower[] = $lastPrice;

        $futureForecastValues = [];
        $futureDates = [];

        for ($i = 1; $i <= $daysForecast; $i++) {
            $futureDate = $lastDate->copy()->addDays($i);
            $futureDates[] = $futureDate->format('Y-m-d');
            $dates[] = $futureDate->format('Y-m-d');

            // Dampen only from day 4 onward so the near-term stays responsive.
            $dampFactor = $i <= 3 ? 1.0 : pow(0.85, $i - 3);
            $currentForecast += $blendedChange * $dampFactor;
            $currentForecast = max($priceFloor, $currentForecast);
            $rounded = round($currentForecast, 2);

            $actualPrices[] = null;
            $forecastPrices[] = $rounded;
            $futureForecastValues[] = $rounded;

            // Band widens slightly the further out the projection goes
            $bandWidth = $bandBase * (1 + ($i * 0.05));
            $forecastUpper[] = round($rounded + $bandWidth, 2);
            $forecastLower[] = round(max(0, $rounded - $bandWidth), 2);
        }

        // --- Insight Calculations ---
        $peakValue = ! empty($futureForecastValues) ? max($futureForecastValues) : $lastPrice;
        $minValue = ! empty($futureForecastValues) ? min($futureForecastValues) : $lastPrice;

        $peakIndex = ! empty($futureForecastValues) ? array_search($peakValue, $futureForecastValues) : null;
        $minIndex = ! empty($futureForecastValues) ? array_search($minValue, $futureForecastValues) : null;

        $peakDay = $peakIndex !== null ? ['date' => $futureDates[$peakIndex], 'price' => $peakValue] : null;
        $minDay = $minIndex !== null ? ['date' => $futureDates[$minIndex], 'price' => $minValue] : null;

        // Percentage change from today's price to end of forecast window
        $endForecast = ! empty($futureForecastValues) ? end($futureForecastValues) : $lastPrice;
        $trendPct = $lastPrice > 0 ? round((($endForecast - $lastPrice) / $lastPrice) * 100, 1) : 0;

        [$recommendation, $recommendationText] = $this->buildRecommendation($trendPct, $peakDay, $minDay, $lastPrice);

        // --- Backtest Accuracy (simple: compare last 7 SMA forecasts to actuals) ---
        $accuracyScore = $this->calculateAccuracyScore($actualPrices, $windowSize);

        return [
            'dates' => $dates,
            'actual' => $actualPrices,
            'forecast' => $forecastPrices,
            'forecast_upper' => $forecastUpper,
            'forecast_lower' => $forecastLower,
            'recommendation' => $recommendation,
            'recommendation_text' => $recommendationText,
            'trend_pct' => $trendPct,
            'peak_day' => $peakDay,
            'min_day' => $minDay,
            'accuracy_score' => $accuracyScore,
        ];
    }

    /**
     * Determine a plain-language selling recommendation based on the trend.
     *
     * @param  array{date: string, price: float}|null  $peakDay
     * @param  array{date: string, price: float}|null  $minDay
     * @return array{0: string, 1: string}
     */
    private function buildRecommendation(float $trendPct, ?array $peakDay, ?array $minDay, float $lastPrice): array
    {
        if ($trendPct <= -3) {
            $drop = abs($trendPct);
            $text = "Prices are expected to drop by about {$drop}% over the next 7 days. "
                .'It is best to sell your harvest as soon as possible to avoid lower returns.';

            return ['SELL_NOW', $text];
        }

        if ($trendPct >= 3) {
            $peakDateFormatted = $peakDay ? Carbon::parse($peakDay['date'])->format('D, M j') : 'later this week';
            $peakPrice = $peakDay ? '₱'.number_format($peakDay['price'], 2) : '';
            $text = "Prices are projected to rise by about {$trendPct}% over the next 7 days. "
                .'Consider holding your harvest'
                .($peakDay ? " until around {$peakDateFormatted} when the peak of {$peakPrice}/kg is expected." : '.')
                .' Only hold if you have proper storage to avoid spoilage.';

            return ['HOLD', $text];
        }

        $text = 'Prices are expected to remain stable (within ±3%) over the next 7 days. '
            .'You can sell at your convenience without significant risk of missing a better price.';

        return ['STABLE', $text];
    }

    /**
     * Estimate forecast accuracy by backtesting the SMA against the last portion of historical data.
     * Returns a score between 0 and 100.
     *
     * @param  list<float|null>  $actualPrices
     */
    private function calculateAccuracyScore(array $actualPrices, int $windowSize): int
    {
        $historicalOnly = array_filter($actualPrices, fn ($p) => $p !== null);
        $historicalOnly = array_values($historicalOnly);
        $count = count($historicalOnly);

        if ($count < $windowSize + 2) {
            return 0;
        }

        $errors = [];
        $backtestDays = min(7, $count - $windowSize - 1);

        for ($i = 0; $i < $backtestDays; $i++) {
            $slice = array_slice($historicalOnly, $i, $windowSize);
            $predicted = array_sum($slice) / $windowSize;
            $actual = $historicalOnly[$i + $windowSize];
            $errors[] = abs($actual - $predicted) / ($actual > 0 ? $actual : 1);
        }

        if (empty($errors)) {
            return 0;
        }

        $mape = (array_sum($errors) / count($errors)) * 100;
        $score = max(0, min(100, (int) round(100 - $mape)));

        return $score;
    }
}

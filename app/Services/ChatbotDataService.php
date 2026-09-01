<?php

namespace App\Services;

use App\Models\CeilingPrice;
use App\Models\Crop;
use App\Models\Price;
use App\Models\Shop;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class ChatbotDataService
{
    /** Cache TTL in seconds (5 minutes). */
    private const CACHE_TTL = 300;

    /**
     * Build a full live data snapshot string to inject into the AI system prompt.
     */
    public function buildLiveSnapshot(): string
    {
        $timestamp = Carbon::now()->format('Y-m-d H:i');

        $lines = [
            "=== LIVE DATA SNAPSHOT (as of {$timestamp} PHT) ===",
            '',
            $this->formatPriceSnapshot(),
            '',
            $this->formatCeilingPriceSnapshot(),
            '',
            $this->formatMarketSummary(),
            '',
            '(Use these numbers to answer price and market questions accurately. Do not make up values outside this data.)',
        ];

        return implode("\n", $lines);
    }

    /**
     * Get the latest price stats per crop (average, min, max, number of shops reporting).
     * Results are grouped by crop name and specification.
     * Cached for 5 minutes.
     *
     * @return array<int, array{crop: string, specification: string|null, avg: string, min: string, max: string, shops: int}>
     */
    public function getPriceSnapshot(): array
    {
        return Cache::remember('chatbot.price_snapshot', self::CACHE_TTL, function () {
            $since = Carbon::now()->subDays(7);

            return Price::query()
                ->join('crops', 'prices.crop_id', '=', 'crops.id')
                ->where('prices.recorded_at', '>=', $since)
                ->select([
                    'crops.name as crop',
                    'prices.specification',
                    DB::raw('ROUND(AVG(prices.price_per_kg), 2) as avg_price'),
                    DB::raw('MIN(prices.price_per_kg) as min_price'),
                    DB::raw('MAX(prices.price_per_kg) as max_price'),
                    DB::raw('COUNT(DISTINCT prices.shop_id) as shop_count'),
                ])
                ->groupBy('crops.name', 'prices.specification')
                ->orderBy('crops.name')
                ->get()
                ->map(fn ($row) => [
                    'crop' => $row->crop,
                    'specification' => $row->specification,
                    'avg' => number_format((float) $row->avg_price, 2),
                    'min' => number_format((float) $row->min_price, 2),
                    'max' => number_format((float) $row->max_price, 2),
                    'shops' => (int) $row->shop_count,
                ])
                ->all();
        });
    }

    /**
     * Get the current DA ceiling prices per crop.
     * Returns the most recent effective ceiling price per crop + specification.
     * Cached for 5 minutes.
     *
     * @return array<int, array{crop: string, specification: string|null, max_price: string, effective_date: string}>
     */
    public function getCeilingPriceSnapshot(): array
    {
        return Cache::remember('chatbot.ceiling_snapshot', self::CACHE_TTL, function () {
            // Use a subquery to get the latest ceiling price per (crop_id, specification).
            $latest = CeilingPrice::query()
                ->join('crops', 'ceiling_prices.crop_id', '=', 'crops.id')
                ->where('ceiling_prices.effective_date', '<=', Carbon::today())
                ->select([
                    'crops.name as crop',
                    'ceiling_prices.specification',
                    'ceiling_prices.max_price',
                    'ceiling_prices.effective_date',
                ])
                ->orderBy('crops.name')
                ->orderByDesc('ceiling_prices.effective_date')
                ->get()
                ->unique(fn ($row) => $row->crop.'_'.($row->specification ?? ''))
                ->values();

            return $latest->map(fn ($row) => [
                'crop' => $row->crop,
                'specification' => $row->specification,
                'max_price' => number_format((float) $row->max_price, 2),
                'effective_date' => Carbon::parse($row->effective_date)->format('Y-m-d'),
            ])->all();
        });
    }

    /**
     * Get high-level market summary statistics.
     * Cached for 5 minutes.
     *
     * @return array{active_shops: int, total_crops: int, prices_this_week: int, prices_this_month: int}
     */
    public function getMarketSummary(): array
    {
        return Cache::remember('chatbot.market_summary', self::CACHE_TTL, function () {
            $now = Carbon::now();

            return [
                'active_shops' => Shop::where('is_active', true)->count(),
                'total_crops' => Crop::count(),
                'prices_this_week' => Price::where('recorded_at', '>=', $now->copy()->subDays(7))->count(),
                'prices_this_month' => Price::where('recorded_at', '>=', $now->copy()->startOfMonth())->count(),
            ];
        });
    }

    /**
     * Determine a simple price trend direction (Rising / Falling / Stable) for a crop
     * by comparing the average price of the last 3 days vs the prior 4 days.
     * Cached for 5 minutes.
     */
    public function getPriceTrend(int $cropId): string
    {
        return Cache::remember("chatbot.trend.{$cropId}", self::CACHE_TTL, function () use ($cropId) {
            $recent = Price::where('crop_id', $cropId)
                ->where('recorded_at', '>=', Carbon::now()->subDays(3))
                ->avg('price_per_kg');

            $older = Price::where('crop_id', $cropId)
                ->whereBetween('recorded_at', [Carbon::now()->subDays(7), Carbon::now()->subDays(4)])
                ->avg('price_per_kg');

            if ($recent === null || $older === null || $older == 0) {
                return 'Stable';
            }

            $changePercent = (($recent - $older) / $older) * 100;

            if ($changePercent >= 2) {
                return 'Rising';
            } elseif ($changePercent <= -2) {
                return 'Falling';
            }

            return 'Stable';
        });
    }

    /**
     * Format the price snapshot into a readable prompt section.
     */
    private function formatPriceSnapshot(): string
    {
        $rows = $this->getPriceSnapshot();

        if (empty($rows)) {
            return 'CROP PRICES (last 7 days): No price data available yet.';
        }

        $lines = ['CROP PRICES (last 7 days avg | min – max | shops reporting):'];

        foreach ($rows as $row) {
            $cropLabel = $row['specification']
                ? "{$row['crop']} ({$row['specification']})"
                : $row['crop'];

            $lines[] = "  - {$cropLabel}: avg ₱{$row['avg']}/kg | ₱{$row['min']} – ₱{$row['max']} | {$row['shops']} shop(s)";
        }

        return implode("\n", $lines);
    }

    /**
     * Format the ceiling price snapshot into a readable prompt section.
     */
    private function formatCeilingPriceSnapshot(): string
    {
        $rows = $this->getCeilingPriceSnapshot();

        if (empty($rows)) {
            return 'DA CEILING PRICES: No ceiling prices set yet.';
        }

        $lines = ['DA CEILING PRICES (maximum allowed price per DA):'];

        foreach ($rows as $row) {
            $cropLabel = $row['specification']
                ? "{$row['crop']} ({$row['specification']})"
                : $row['crop'];

            $lines[] = "  - {$cropLabel}: ₱{$row['max_price']}/kg (effective {$row['effective_date']})";
        }

        return implode("\n", $lines);
    }

    /**
     * Format the market summary into a readable prompt section.
     */
    private function formatMarketSummary(): string
    {
        $summary = $this->getMarketSummary();

        return implode("\n", [
            'MARKET SUMMARY:',
            "  - Active buyer shops: {$summary['active_shops']}",
            "  - Total crops tracked: {$summary['total_crops']}",
            "  - Price entries this week: {$summary['prices_this_week']}",
            "  - Price entries this month: {$summary['prices_this_month']}",
        ]);
    }
}

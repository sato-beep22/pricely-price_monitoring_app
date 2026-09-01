<?php

namespace Tests\Unit;

use App\Models\CeilingPrice;
use App\Models\Crop;
use App\Models\Price;
use App\Models\Shop;
use App\Services\ChatbotDataService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class ChatbotDataServiceTest extends TestCase
{
    use RefreshDatabase;

    private ChatbotDataService $service;

    protected function setUp(): void
    {
        parent::setUp();
        Cache::flush();
        $this->service = new ChatbotDataService;
    }

    public function test_get_price_snapshot_returns_empty_array_when_no_prices(): void
    {
        $result = $this->service->getPriceSnapshot();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_get_price_snapshot_returns_stats_for_recent_prices(): void
    {
        $crop = Crop::factory()->create(['name' => 'Palay']);
        $shop = Shop::factory()->create();

        Price::factory()->count(3)->create([
            'crop_id' => $crop->id,
            'shop_id' => $shop->id,
            'price_per_kg' => 22.00,
            'recorded_at' => now()->subDays(1),
        ]);

        $result = $this->service->getPriceSnapshot();

        $this->assertNotEmpty($result);
        $this->assertEquals('Palay', $result[0]['crop']);
        $this->assertEquals('22.00', $result[0]['avg']);
        $this->assertEquals('22.00', $result[0]['min']);
        $this->assertEquals('22.00', $result[0]['max']);
        $this->assertEquals(1, $result[0]['shops']);
    }

    public function test_get_price_snapshot_ignores_prices_older_than_7_days(): void
    {
        $crop = Crop::factory()->create(['name' => 'Mais']);
        $shop = Shop::factory()->create();

        Price::factory()->create([
            'crop_id' => $crop->id,
            'shop_id' => $shop->id,
            'price_per_kg' => 15.00,
            'recorded_at' => now()->subDays(30),
        ]);

        $result = $this->service->getPriceSnapshot();

        $this->assertEmpty($result);
    }

    public function test_get_ceiling_price_snapshot_returns_empty_when_none_set(): void
    {
        $result = $this->service->getCeilingPriceSnapshot();

        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_get_ceiling_price_snapshot_returns_current_ceiling_prices(): void
    {
        $crop = Crop::factory()->create(['name' => 'Palay']);

        CeilingPrice::factory()->create([
            'crop_id' => $crop->id,
            'max_price' => 23.00,
            'effective_date' => now()->subDays(5),
            'specification' => 'Dry',
        ]);

        $result = $this->service->getCeilingPriceSnapshot();

        $this->assertNotEmpty($result);
        $this->assertEquals('Palay', $result[0]['crop']);
        $this->assertEquals('Dry', $result[0]['specification']);
        $this->assertEquals('23.00', $result[0]['max_price']);
    }

    public function test_get_ceiling_price_snapshot_excludes_future_effective_dates(): void
    {
        $crop = Crop::factory()->create(['name' => 'Munggo']);

        CeilingPrice::factory()->create([
            'crop_id' => $crop->id,
            'max_price' => 70.00,
            'effective_date' => now()->addDays(10),
        ]);

        $result = $this->service->getCeilingPriceSnapshot();

        $this->assertEmpty($result);
    }

    public function test_get_market_summary_returns_correct_counts(): void
    {
        // CropFactory only has 3 unique names, so keep crop count ≤ 3 total.
        $crops = Crop::factory()->count(2)->create();
        Shop::factory()->count(2)->create(['is_active' => true]);
        Shop::factory()->count(2)->create(['is_active' => false]);

        $activeShop = Shop::factory()->create(['is_active' => true]);

        Price::factory()->count(5)->create([
            'shop_id' => $activeShop->id,
            'crop_id' => $crops->first()->id,
            'recorded_at' => now()->subDays(2),
        ]);

        Price::factory()->count(3)->create([
            'shop_id' => $activeShop->id,
            'crop_id' => $crops->first()->id,
            'recorded_at' => now()->subDays(20),
        ]);

        // Force fresh query by clearing the specific cached key before asserting.
        Cache::forget('chatbot.market_summary');
        $summary = $this->service->getMarketSummary();

        $this->assertEquals(3, $summary['active_shops']); // 2 + 1 active above
        $this->assertEquals(2, $summary['total_crops']);
        $this->assertEquals(5, $summary['prices_this_week']);
        $this->assertArrayHasKey('prices_this_month', $summary);
    }

    public function test_get_price_trend_returns_stable_when_no_data(): void
    {
        $crop = Crop::factory()->create();

        $trend = $this->service->getPriceTrend($crop->id);

        $this->assertEquals('Stable', $trend);
    }

    public function test_get_price_trend_returns_rising_when_prices_increased(): void
    {
        $crop = Crop::factory()->create();
        $shop = Shop::factory()->create();

        // Older prices (4-7 days ago): lower
        Price::factory()->count(3)->create([
            'crop_id' => $crop->id,
            'shop_id' => $shop->id,
            'price_per_kg' => 20.00,
            'recorded_at' => now()->subDays(5),
        ]);

        // Recent prices (last 3 days): higher
        Price::factory()->count(3)->create([
            'crop_id' => $crop->id,
            'shop_id' => $shop->id,
            'price_per_kg' => 25.00,
            'recorded_at' => now()->subDays(1),
        ]);

        $trend = $this->service->getPriceTrend($crop->id);

        $this->assertEquals('Rising', $trend);
    }

    public function test_get_price_trend_returns_falling_when_prices_decreased(): void
    {
        $crop = Crop::factory()->create();
        $shop = Shop::factory()->create();

        // Older prices: higher
        Price::factory()->count(3)->create([
            'crop_id' => $crop->id,
            'shop_id' => $shop->id,
            'price_per_kg' => 25.00,
            'recorded_at' => now()->subDays(5),
        ]);

        // Recent prices: lower
        Price::factory()->count(3)->create([
            'crop_id' => $crop->id,
            'shop_id' => $shop->id,
            'price_per_kg' => 20.00,
            'recorded_at' => now()->subDays(1),
        ]);

        $trend = $this->service->getPriceTrend($crop->id);

        $this->assertEquals('Falling', $trend);
    }

    public function test_build_live_snapshot_contains_expected_sections(): void
    {
        $snapshot = $this->service->buildLiveSnapshot();

        $this->assertStringContainsString('LIVE DATA SNAPSHOT', $snapshot);
        $this->assertStringContainsString('CROP PRICES', $snapshot);
        $this->assertStringContainsString('DA CEILING PRICES', $snapshot);
        $this->assertStringContainsString('MARKET SUMMARY', $snapshot);
    }

    public function test_price_snapshot_results_are_cached(): void
    {
        Cache::flush();

        $crop = Crop::factory()->create(['name' => 'Palay']);
        $shop = Shop::factory()->create();

        Price::factory()->create([
            'crop_id' => $crop->id,
            'shop_id' => $shop->id,
            'price_per_kg' => 20.00,
            'recorded_at' => now()->subDays(1),
        ]);

        // First call — hits DB
        $first = $this->service->getPriceSnapshot();

        // Create another price — should not appear because result is cached
        Price::factory()->create([
            'crop_id' => $crop->id,
            'shop_id' => $shop->id,
            'price_per_kg' => 99.00,
            'recorded_at' => now(),
        ]);

        // Second call — should return cached result
        $second = $this->service->getPriceSnapshot();

        $this->assertEquals($first, $second);
    }
}

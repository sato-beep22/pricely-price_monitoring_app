<?php

namespace Tests\Feature;

use App\Models\Crop;
use App\Models\User;
use App\Services\PriceForecastService;
use Database\Seeders\CropSeeder;
use Database\Seeders\PriceSeeder;
use Database\Seeders\UserSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PriceForecastTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CropSeeder::class);
        $this->seed(UserSeeder::class);
        $this->seed(PriceSeeder::class);
    }

    public function test_price_seeder_populates_30_days_of_historical_data(): void
    {
        $crop = Crop::first();
        $this->assertNotNull($crop);

        $spec = trim(explode(',', $crop->specification)[0]);

        $service = app(PriceForecastService::class);
        $forecast = $service->getForecast($crop->id, $spec);

        $this->assertGreaterThanOrEqual(30, count($forecast['dates']));
        $this->assertNotEquals('NO_DATA', $forecast['recommendation']);
    }

    public function test_forecast_page_renders_successfully_for_authenticated_users(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/forecast');

        $response->assertStatus(200);
        $response->assertSee('Price Forecasting');
        $response->assertSee('Market Trends');
    }
}

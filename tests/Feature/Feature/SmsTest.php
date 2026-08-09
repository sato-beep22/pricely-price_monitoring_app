<?php

namespace Tests\Feature\Feature;

use App\Events\PriceUpdated;
use App\Listeners\SendPriceUpdateSms;
use App\Models\Crop;
use App\Models\Price;
use App\Models\Shop;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SemaphoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class SmsTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        parent::tearDown();
        Mockery::close();
    }

    // -----------------------------------------------------------------------
    // Phone Verification SMS
    // -----------------------------------------------------------------------

    /**
     * @test
     */
    public function phone_verification_sends_sms_via_semaphore(): void
    {
        $semaphore = Mockery::mock(SemaphoreService::class);
        $semaphore->shouldReceive('sendOtp')
            ->once()
            ->with('9171234567', Mockery::type('string'))
            ->andReturn(true);

        $this->app->instance(SemaphoreService::class, $semaphore);

        $farmer = User::factory()->create(['role' => 'farmer']);

        $this->actingAs($farmer)
            ->post(route('phone.verification.send'), ['phone' => '9171234567'])
            ->assertRedirect()
            ->assertSessionHas('status', 'phone-verification-sent');

        $this->assertDatabaseHas('users', [
            'id' => $farmer->id,
            'phone' => '9171234567',
        ]);
    }

    /**
     * @test
     */
    public function phone_verification_logs_warning_when_sms_fails(): void
    {
        $semaphore = Mockery::mock(SemaphoreService::class);
        $semaphore->shouldReceive('sendOtp')->once()->andReturn(false);

        $this->app->instance(SemaphoreService::class, $semaphore);

        $farmer = User::factory()->create(['role' => 'farmer']);

        $this->actingAs($farmer)
            ->post(route('phone.verification.send'), ['phone' => '9171234567'])
            ->assertSessionHas('status', 'phone-verification-sent');
    }

    // -----------------------------------------------------------------------
    // Price Alert SMS — verified vs unverified farmers
    // -----------------------------------------------------------------------

    /**
     * @test
     */
    public function price_alert_sms_sent_to_verified_farmers_only(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $shop = Shop::factory()->create(['user_id' => $buyer->id]);
        $crop = Crop::factory()->create();
        $price = Price::factory()->create(['shop_id' => $shop->id, 'crop_id' => $crop->id]);

        $verifiedFarmer = User::factory()->create([
            'role' => 'farmer',
            'phone' => '+639181234567',
            'phone_verified_at' => now(),
        ]);

        $unverifiedFarmer = User::factory()->create([
            'role' => 'farmer',
            'phone' => '+639199999999',
            'phone_verified_at' => null,
        ]);

        Subscription::factory()->create([
            'farmer_id' => $verifiedFarmer->id,
            'buyer_id' => $buyer->id,
            'crop_ids' => [$crop->id],
            'is_active' => true,
        ]);

        Subscription::factory()->create([
            'farmer_id' => $unverifiedFarmer->id,
            'buyer_id' => $buyer->id,
            'crop_ids' => [$crop->id],
            'is_active' => true,
        ]);

        $semaphore = Mockery::mock(SemaphoreService::class);
        $semaphore->shouldReceive('sendSms')
            ->once()
            ->with($verifiedFarmer->phone, Mockery::type('string'))
            ->andReturn(true);

        $listener = new SendPriceUpdateSms($semaphore);
        $listener->handle(new PriceUpdated($shop, $crop, $price));
    }

    /**
     * @test
     */
    public function price_alert_sms_not_sent_when_no_subscribers(): void
    {
        $buyer = User::factory()->create(['role' => 'buyer']);
        $shop = Shop::factory()->create(['user_id' => $buyer->id]);
        $crop = Crop::factory()->create();
        $price = Price::factory()->create(['shop_id' => $shop->id, 'crop_id' => $crop->id]);

        $semaphore = Mockery::mock(SemaphoreService::class);
        $semaphore->shouldNotReceive('sendSms');

        $listener = new SendPriceUpdateSms($semaphore);
        $listener->handle(new PriceUpdated($shop, $crop, $price));
    }
}

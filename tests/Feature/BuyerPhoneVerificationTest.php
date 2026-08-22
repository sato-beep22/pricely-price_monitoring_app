<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SemaphoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Mockery;
use Tests\TestCase;

class BuyerPhoneVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_send_phone_verification_code(): void
    {
        $buyer = User::factory()->buyer()->create(['phone' => null]);

        $smsMock = Mockery::mock(SemaphoreService::class);
        $smsMock->shouldReceive('sendOtp')->once()->andReturn(true);
        $this->app->instance(SemaphoreService::class, $smsMock);

        $response = $this
            ->actingAs($buyer)
            ->from('/profile')
            ->post(route('buyer.phone.verification.send'), ['phone' => '09171234567']);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('status', 'phone-verification-sent');
        $this->assertSame('09171234567', $buyer->fresh()->phone);
        $this->assertNotNull($buyer->fresh()->phone_verification_code);
    }

    public function test_buyer_can_verify_phone_with_correct_code(): void
    {
        $code = '12345';
        $buyer = User::factory()->buyer()->create([
            'phone' => '09171234567',
            'phone_verified_at' => null,
            'phone_verification_code' => Hash::make($code),
            'phone_verification_expires_at' => now()->addMinutes(5),
        ]);

        $response = $this
            ->actingAs($buyer)
            ->from('/profile')
            ->post(route('buyer.phone.verification.verify'), ['code' => $code]);

        $response->assertRedirect('/profile');
        $response->assertSessionHas('status', 'phone-verified');
        $this->assertNotNull($buyer->fresh()->phone_verified_at);
        $this->assertNull($buyer->fresh()->phone_verification_code);
    }

    public function test_buyer_cannot_verify_with_wrong_code(): void
    {
        $buyer = User::factory()->buyer()->create([
            'phone' => '09171234567',
            'phone_verified_at' => null,
            'phone_verification_code' => Hash::make('12345'),
            'phone_verification_expires_at' => now()->addMinutes(5),
        ]);

        $response = $this
            ->actingAs($buyer)
            ->from('/profile')
            ->post(route('buyer.phone.verification.verify'), ['code' => '99999']);

        $response->assertSessionHasErrors(['code']);
        $this->assertNull($buyer->fresh()->phone_verified_at);
    }

    public function test_buyer_cannot_verify_with_expired_code(): void
    {
        $code = '12345';
        $buyer = User::factory()->buyer()->create([
            'phone' => '09171234567',
            'phone_verified_at' => null,
            'phone_verification_code' => Hash::make($code),
            'phone_verification_expires_at' => now()->subMinute(),
        ]);

        $response = $this
            ->actingAs($buyer)
            ->from('/profile')
            ->post(route('buyer.phone.verification.verify'), ['code' => $code]);

        $response->assertSessionHasErrors(['code']);
        $this->assertNull($buyer->fresh()->phone_verified_at);
    }

    public function test_farmer_cannot_use_buyer_phone_verification_routes(): void
    {
        $farmer = User::factory()->farmer()->create();

        $sendResponse = $this
            ->actingAs($farmer)
            ->post(route('buyer.phone.verification.send'), ['phone' => '09171234567']);

        $sendResponse->assertForbidden();

        $verifyResponse = $this
            ->actingAs($farmer)
            ->post(route('buyer.phone.verification.verify'), ['code' => '12345']);

        $verifyResponse->assertForbidden();
    }
}

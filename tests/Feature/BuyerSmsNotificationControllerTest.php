<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BuyerSmsNotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_buyer_can_enable_sms_notifications(): void
    {
        $buyer = User::factory()->buyer()->create([
            'phone_verified_at' => now(),
            'sms_notifications_enabled' => false,
        ]);

        $response = $this
            ->actingAs($buyer)
            ->from('/profile')
            ->patch(route('buyer.sms-notifications.toggle'));

        $response->assertRedirect('/profile');
        $response->assertSessionHas('status', 'sms-enabled');
        $this->assertTrue($buyer->fresh()->sms_notifications_enabled);
    }

    public function test_buyer_can_disable_sms_notifications(): void
    {
        $buyer = User::factory()->buyer()->create([
            'phone_verified_at' => now(),
            'sms_notifications_enabled' => true,
        ]);

        $response = $this
            ->actingAs($buyer)
            ->from('/profile')
            ->patch(route('buyer.sms-notifications.toggle'));

        $response->assertRedirect('/profile');
        $response->assertSessionHas('status', 'sms-disabled');
        $this->assertFalse($buyer->fresh()->sms_notifications_enabled);
    }

    public function test_farmer_cannot_access_sms_toggle_route(): void
    {
        $farmer = User::factory()->farmer()->create();

        $response = $this
            ->actingAs($farmer)
            ->patch(route('buyer.sms-notifications.toggle'));

        $response->assertForbidden();
    }

    public function test_guest_is_redirected_from_sms_toggle_route(): void
    {
        $response = $this->patch(route('buyer.sms-notifications.toggle'));

        $response->assertRedirect('/login');
    }
}

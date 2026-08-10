<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'farmer',
            'privacy_policy' => '1',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_register_without_email(): void
    {
        $response = $this->post('/register', [
            'name' => 'No Email User',
            'email' => '',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'farmer',
            'privacy_policy' => '1',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'No Email User',
            'email' => null,
        ]);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_registration_fails_without_privacy_policy_accepted(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'farmer',
        ]);

        $response->assertSessionHasErrors(['privacy_policy']);
        $this->assertGuest();
    }

    public function test_buyer_registration_requires_classification_and_privacy_policy(): void
    {
        $response = $this->post('/register', [
            'name' => 'Buyer User',
            'email' => 'buyer@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
            'role' => 'buyer',
            'buyer_classification' => 'trader',
            'privacy_policy' => '1',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'name' => 'Buyer User',
            'role' => 'buyer',
        ]);
        $response->assertRedirect(route('dashboard', absolute: false));
    }
}

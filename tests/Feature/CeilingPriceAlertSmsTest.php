<?php

namespace Tests\Feature;

use App\Events\CeilingPriceUpdated;
use App\Listeners\SendCeilingPriceAlertSms;
use App\Models\User;
use App\Services\SemaphoreService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class CeilingPriceAlertSmsTest extends TestCase
{
    use RefreshDatabase;

    /** @var array<int, array{crop_name: string, specification: string|null, old_max_price: string|null, new_max_price: string}> */
    private array $sampleUpdates = [
        [
            'crop_name' => 'Kamatis',
            'specification' => 'cherry',
            'old_max_price' => '20.00',
            'new_max_price' => '25.00',
        ],
    ];

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /**
     * Create a fresh Mockery mock of SemaphoreService and wire it directly
     * into the listener via constructor injection (no container binding needed).
     *
     * @return array{0: MockInterface&SemaphoreService, 1: SendCeilingPriceAlertSms}
     */
    private function makeListenerWithMock(): array
    {
        /** @var MockInterface&SemaphoreService $mock */
        $mock = Mockery::mock(SemaphoreService::class)->makePartial();

        return [$mock, new SendCeilingPriceAlertSms($mock)];
    }

    public function test_sms_is_sent_to_buyer_with_verified_phone_and_notifications_enabled(): void
    {
        $buyer = User::factory()->buyer()->create([
            'phone' => '09171234567',
            'phone_verified_at' => now(),
            'sms_notifications_enabled' => true,
        ]);

        [$mock, $listener] = $this->makeListenerWithMock();

        $mock->shouldReceive('sendSms')
            ->once()
            ->with($buyer->phone, Mockery::type('string'));

        $listener->handle(new CeilingPriceUpdated($this->sampleUpdates));
    }

    public function test_sms_is_not_sent_to_buyer_with_unverified_phone(): void
    {
        User::factory()->buyer()->create([
            'phone' => '09171234567',
            'phone_verified_at' => null,
            'sms_notifications_enabled' => true,
        ]);

        [$mock, $listener] = $this->makeListenerWithMock();
        $mock->shouldNotReceive('sendSms');

        $listener->handle(new CeilingPriceUpdated($this->sampleUpdates));
    }

    public function test_sms_is_not_sent_to_buyer_with_notifications_disabled(): void
    {
        User::factory()->buyer()->create([
            'phone' => '09171234567',
            'phone_verified_at' => now(),
            'sms_notifications_enabled' => false,
        ]);

        [$mock, $listener] = $this->makeListenerWithMock();
        $mock->shouldNotReceive('sendSms');

        $listener->handle(new CeilingPriceUpdated($this->sampleUpdates));
    }

    public function test_sms_is_not_sent_when_buyer_has_no_phone(): void
    {
        User::factory()->buyer()->create([
            'phone' => null,
            'phone_verified_at' => null,
            'sms_notifications_enabled' => true,
        ]);

        [$mock, $listener] = $this->makeListenerWithMock();
        $mock->shouldNotReceive('sendSms');

        $listener->handle(new CeilingPriceUpdated($this->sampleUpdates));
    }

    public function test_message_includes_price_change_from_old_to_new(): void
    {
        $buyer = User::factory()->buyer()->create([
            'phone' => '09171234567',
            'phone_verified_at' => now(),
            'sms_notifications_enabled' => true,
        ]);

        $capturedMessage = null;

        [$mock, $listener] = $this->makeListenerWithMock();
        $mock->shouldReceive('sendSms')
            ->once()
            ->withArgs(function (string $phone, string $message) use ($buyer, &$capturedMessage) {
                $capturedMessage = $message;

                return $phone === $buyer->phone;
            });

        $listener->handle(new CeilingPriceUpdated($this->sampleUpdates));

        $this->assertStringContainsString('Kamatis', $capturedMessage);
        $this->assertStringContainsString('cherry', $capturedMessage);
        $this->assertStringContainsString('20.00', $capturedMessage);
        $this->assertStringContainsString('25.00', $capturedMessage);
    }

    public function test_message_shows_only_new_price_when_price_unchanged(): void
    {
        User::factory()->buyer()->create([
            'phone' => '09171234567',
            'phone_verified_at' => now(),
            'sms_notifications_enabled' => true,
        ]);

        $updates = [
            [
                'crop_name' => 'Sibuyas',
                'specification' => null,
                'old_max_price' => '50.00',
                'new_max_price' => '50.00',
            ],
        ];

        $capturedMessage = null;

        [$mock, $listener] = $this->makeListenerWithMock();
        $mock->shouldReceive('sendSms')
            ->once()
            ->withArgs(function (string $phone, string $message) use (&$capturedMessage) {
                $capturedMessage = $message;

                return true;
            });

        $listener->handle(new CeilingPriceUpdated($updates));

        $this->assertStringContainsString('Max P50.00', $capturedMessage);
        $this->assertStringNotContainsString('mula', $capturedMessage);
    }

    public function test_no_sms_when_no_eligible_buyers(): void
    {
        // Only a farmer — ceiling price alerts do not go to farmers
        User::factory()->farmer()->create([
            'phone' => '09171234567',
            'phone_verified_at' => now(),
            'sms_notifications_enabled' => true,
        ]);

        [$mock, $listener] = $this->makeListenerWithMock();
        $mock->shouldNotReceive('sendSms');

        $listener->handle(new CeilingPriceUpdated($this->sampleUpdates));
    }
}

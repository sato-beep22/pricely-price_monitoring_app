<?php

namespace Tests\Feature\Admin;

use App\Models\Crop;
use App\Models\User;
use App\Services\DaPriceSyncService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class DaPriceSyncTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    #[Test]
    public function preview_requires_authentication(): void
    {
        $this->postJson(route('admin.da-price-sync.preview'), ['url' => 'https://da.gov.ph'])
            ->assertStatus(401);
    }

    #[Test]
    public function preview_is_forbidden_for_non_admins(): void
    {
        $farmer = User::factory()->create(['role' => 'farmer']);

        $this->actingAs($farmer)
            ->postJson(route('admin.da-price-sync.preview'), ['url' => 'https://da.gov.ph'])
            ->assertStatus(403);
    }

    #[Test]
    public function preview_validates_required_url(): void
    {
        $this->actingAs($this->admin)
            ->postJson(route('admin.da-price-sync.preview'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['url']);
    }

    #[Test]
    public function preview_validates_url_format(): void
    {
        $this->actingAs($this->admin)
            ->postJson(route('admin.da-price-sync.preview'), ['url' => 'not-a-url'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['url']);
    }

    #[Test]
    public function preview_returns_422_when_service_fails(): void
    {
        $this->mock(DaPriceSyncService::class, function (MockInterface $mock) {
            $mock->shouldReceive('syncFromUrl')
                ->once()
                ->andReturn([
                    'success' => false,
                    'prices' => [],
                    'source_url' => 'https://da.gov.ph/price-monitoring/',
                    'error' => 'Failed to fetch the URL (HTTP 404).',
                ]);
        });

        $this->actingAs($this->admin)
            ->postJson(route('admin.da-price-sync.preview'), ['url' => 'https://da.gov.ph/price-monitoring/'])
            ->assertStatus(422)
            ->assertJson(['success' => false]);
    }

    #[Test]
    public function preview_returns_enriched_prices_with_match_status(): void
    {
        $crop = Crop::factory()->create(['name' => 'Rice', 'specification' => 'well milled,regular milled']);

        $this->mock(DaPriceSyncService::class, function (MockInterface $mock) {
            $mock->shouldReceive('syncFromUrl')
                ->once()
                ->andReturn([
                    'success' => true,
                    'prices' => [
                        ['crop' => 'Rice', 'specification' => 'well milled', 'max_price' => 54.00],
                        ['crop' => 'Banana', 'specification' => 'regular', 'max_price' => 30.00],
                    ],
                    'source_url' => 'https://da.gov.ph/price-monitoring/',
                ]);
        });

        $response = $this->actingAs($this->admin)
            ->postJson(route('admin.da-price-sync.preview'), ['url' => 'https://da.gov.ph/price-monitoring/'])
            ->assertOk()
            ->assertJson(['success' => true]);

        $prices = $response->json('prices');
        $this->assertCount(2, $prices);

        $rice = collect($prices)->firstWhere('crop', 'Rice');
        $this->assertEquals('matched', $rice['status']);
        $this->assertEquals($crop->id, $rice['matched_crop_id']);

        $banana = collect($prices)->firstWhere('crop', 'Banana');
        $this->assertEquals('unmatched', $banana['status']);
        $this->assertNull($banana['matched_crop_id']);
    }

    #[Test]
    public function apply_requires_authentication(): void
    {
        $this->postJson(route('admin.da-price-sync.apply'), [])
            ->assertStatus(401);
    }

    #[Test]
    public function apply_validates_required_fields(): void
    {
        $this->actingAs($this->admin)
            ->postJson(route('admin.da-price-sync.apply'), [])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['prices', 'effective_date']);
    }

    #[Test]
    public function apply_saves_ceiling_prices_for_matched_crops(): void
    {
        $crop = Crop::factory()->create(['name' => 'Rice', 'specification' => 'well milled']);

        $this->actingAs($this->admin)
            ->postJson(route('admin.da-price-sync.apply'), [
                'prices' => [
                    [
                        'matched_crop_id' => $crop->id,
                        'specification' => 'well milled',
                        'max_price' => 54.00,
                        'crop' => 'Rice',
                        'status' => 'matched',
                    ],
                ],
                'effective_date' => '2026-08-16',
                'source_url' => 'https://da.gov.ph/price-monitoring/',
            ])
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('ceiling_prices', [
            'crop_id' => $crop->id,
            'specification' => 'well milled',
            'max_price' => 54.00,
            'admin_id' => $this->admin->id,
        ]);
    }

    #[Test]
    public function apply_updates_existing_ceiling_price_instead_of_duplicating(): void
    {
        $crop = Crop::factory()->create(['name' => 'Corn', 'specification' => 'yellow']);

        // First apply
        $this->actingAs($this->admin)->postJson(route('admin.da-price-sync.apply'), [
            'prices' => [['matched_crop_id' => $crop->id, 'specification' => 'yellow', 'max_price' => 30.00, 'status' => 'matched']],
            'effective_date' => '2026-08-16',
        ])->assertOk();

        // Second apply with updated price
        $this->actingAs($this->admin)->postJson(route('admin.da-price-sync.apply'), [
            'prices' => [['matched_crop_id' => $crop->id, 'specification' => 'yellow', 'max_price' => 35.00, 'status' => 'matched']],
            'effective_date' => '2026-08-16',
        ])->assertOk();

        $this->assertDatabaseCount('ceiling_prices', 1);
        $this->assertDatabaseHas('ceiling_prices', ['crop_id' => $crop->id, 'max_price' => 35.00]);
    }
}

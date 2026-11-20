<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ProviderProfile;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProviderSearchTest extends TestCase
{
    use RefreshDatabase;

    private function createProvider(array $userAttributes = [], array $profileAttributes = []): User
    {
        $provider = User::factory()->create(array_merge([
            'role'        => UserRole::PROVIDER,
            'status'      => 'active',
            'is_verified' => true,
        ], $userAttributes));

        ProviderProfile::create(array_merge([
            'user_id'       => $provider->id,
            'business_name' => 'Elite Handyman Pro',
            'category'      => 'Plumbing',
            'city'          => 'Tunis',
            'hourly_rate'   => 40.00,
            'bio'           => 'Licensed master craftsman.',
            'skills'        => ['Piping', 'Leak Detection'],
            'is_available'  => true,
        ], $profileAttributes));

        return $provider;
    }

    // =========================================================================
    // Basic Public Listing & Account Role Filtering
    // =========================================================================

    public function test_can_list_active_providers_publicly(): void
    {
        $this->createProvider(['name' => 'Provider One']);
        $this->createProvider(['name' => 'Provider Two']);

        $response = $this->getJson('/api/v1/providers');

        $response->assertOk()
            ->assertJsonPath('meta.total', 2);
    }

    public function test_suspended_and_client_accounts_are_excluded(): void
    {
        $this->createProvider(['name' => 'Active Provider']);
        $this->createProvider(['name' => 'Suspended Provider', 'status' => 'suspended']);
        User::factory()->create(['name' => 'Normal Client', 'role' => UserRole::CLIENT]);

        $response = $this->getJson('/api/v1/providers');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', 'Active Provider');
    }

    // =========================================================================
    // Multi-Criteria Filters
    // =========================================================================

    public function test_filter_providers_by_trade(): void
    {
        $this->createProvider(['name' => 'Plumber Pro'], ['category' => 'Plumbing']);
        $this->createProvider(['name' => 'Sparky Electric'], ['category' => 'Electricity']);

        $response = $this->getJson('/api/v1/providers?trade=Plumbing');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', 'Plumber Pro')
            ->assertJsonPath('data.0.trade', 'Plumbing');
    }

    public function test_filter_providers_by_city(): void
    {
        $this->createProvider(['name' => 'Tunis Pro'], ['city' => 'Tunis']);
        $this->createProvider(['name' => 'Sousse Pro'], ['city' => 'Sousse']);

        $response = $this->getJson('/api/v1/providers?location=Sousse');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', 'Sousse Pro')
            ->assertJsonPath('data.0.city', 'Sousse');
    }

    public function test_filter_providers_by_hourly_rate_range(): void
    {
        $this->createProvider(['name' => 'Cheap Pro'], ['hourly_rate' => 25.00]);
        $this->createProvider(['name' => 'Mid Pro'], ['hourly_rate' => 50.00]);
        $this->createProvider(['name' => 'Expensive Pro'], ['hourly_rate' => 90.00]);

        // Rate between 30 and 60
        $response = $this->getJson('/api/v1/providers?min_price=30&max_price=60');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', 'Mid Pro');
    }

    public function test_filter_providers_by_availability(): void
    {
        $this->createProvider(['name' => 'Available Pro'], ['is_available' => true]);
        $this->createProvider(['name' => 'Busy Pro'], ['is_available' => false]);

        $response = $this->getJson('/api/v1/providers?available_only=1');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', 'Available Pro');
    }

    public function test_filter_providers_by_keyword_and_skills(): void
    {
        $this->createProvider(['name' => 'Mounir Trabelsi'], ['skills' => ['Radiator Repair', 'Welding']]);
        $this->createProvider(['name' => 'Farouk Ben Amor'], ['skills' => ['Wiring', 'Lighting']]);

        // Search by skill keyword
        $response = $this->getJson('/api/v1/providers?keyword=Welding');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', 'Mounir Trabelsi');
    }

    public function test_filter_providers_by_minimum_rating(): void
    {
        $client = User::factory()->create(['role' => UserRole::CLIENT]);
        $highRatedPro = $this->createProvider(['name' => '5 Star Pro']);
        $lowRatedPro = $this->createProvider(['name' => '3 Star Pro']);

        $booking1 = \App\Models\Booking::factory()->create([
            'client_id'   => $client->id,
            'provider_id' => $highRatedPro->id,
            'status'      => 'completed',
        ]);
        $booking2 = \App\Models\Booking::factory()->create([
            'client_id'   => $client->id,
            'provider_id' => $lowRatedPro->id,
            'status'      => 'completed',
        ]);

        Review::create([
            'booking_id'  => $booking1->id,
            'client_id'   => $client->id,
            'provider_id' => $highRatedPro->id,
            'rating'      => 5,
            'comment'     => 'Outstanding craftsmanship!',
        ]);

        Review::create([
            'booking_id'  => $booking2->id,
            'client_id'   => $client->id,
            'provider_id' => $lowRatedPro->id,
            'rating'      => 3,
            'comment'     => 'Average work.',
        ]);

        // Filter min_rating = 4.5
        $response = $this->getJson('/api/v1/providers?min_rating=4.5');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.name', '5 Star Pro');
    }

    // =========================================================================
    // Sorting
    // =========================================================================

    public function test_sort_providers_by_hourly_rate(): void
    {
        $this->createProvider(['name' => 'Medium Rate'], ['hourly_rate' => 45.00]);
        $this->createProvider(['name' => 'Low Rate'], ['hourly_rate' => 20.00]);
        $this->createProvider(['name' => 'High Rate'], ['hourly_rate' => 95.00]);

        $response = $this->getJson('/api/v1/providers?sort=price_asc');

        $response->assertOk()
            ->assertJsonPath('data.0.name', 'Low Rate')
            ->assertJsonPath('data.1.name', 'Medium Rate')
            ->assertJsonPath('data.2.name', 'High Rate');
    }

    // =========================================================================
    // Metadata Endpoint & Web Blade View
    // =========================================================================

    public function test_provider_filters_metadata_endpoint(): void
    {
        $this->createProvider(['name' => 'Pro One'], ['category' => 'Carpentry', 'city' => 'Bizerte', 'hourly_rate' => 65.00]);

        $response = $this->getJson('/api/v1/providers/filters');

        $response->assertOk()
            ->assertJsonStructure(['trades', 'cities', 'max_hourly_rate']);
    }

    public function test_web_providers_directory_page_is_accessible(): void
    {
        $this->createProvider(['name' => 'Pro Directory User']);

        $response = $this->get('/providers');

        $response->assertOk()
            ->assertSee('Find Skilled Pros in Tunisia')
            ->assertSee('Pro Directory User');
    }
}

<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ProviderProfile;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicServiceSearchTest extends TestCase
{
    use RefreshDatabase;

    private function createProviderWithService(
        array $userOverrides = [],
        array $profileOverrides = [],
        array $serviceOverrides = []
    ): array {
        $provider = User::factory()->create(array_merge([
            'role'        => UserRole::PROVIDER,
            'status'      => 'active',
            'is_verified' => true,
        ], $userOverrides));

        $profile = ProviderProfile::create(array_merge([
            'user_id'       => $provider->id,
            'business_name' => 'Expert Pro Services',
            'category'      => 'Plumbing',
            'city'          => 'Tunis',
            'hourly_rate'   => 40.00,
            'is_available'  => true,
            'skills'        => ['Emergency Repair', 'Pipe Replacement'],
        ], $profileOverrides));

        $service = Service::factory()->approved()->create(array_merge([
            'provider_id'    => $provider->id,
            'title'          => 'Emergency Pipe Leak Repair',
            'description'    => 'Fast urgent water leak repair and pipe fixing in Tunis',
            'category'       => 'Plumbing',
            'price'          => 75.00,
            'duration_hours' => 1.5,
            'is_active'      => true,
            'status'         => 'approved',
        ], $serviceOverrides));

        return compact('provider', 'profile', 'service');
    }

    public function test_guest_can_search_services_by_keyword(): void
    {
        $this->createProviderWithService(
            [],
            ['city' => 'Tunis'],
            ['title' => 'Emergency Pipe Leak Repair', 'description' => 'Fixing broken pipes']
        );

        $this->createProviderWithService(
            [],
            ['city' => 'Sousse'],
            ['title' => 'Electrical Panel Installation', 'description' => 'Safe rewiring and breakers']
        );

        $response = $this->getJson('/api/v1/services?keyword=leak');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'Emergency Pipe Leak Repair']);

        $response->assertJsonMissing(['title' => 'Electrical Panel Installation']);
    }

    public function test_guest_can_filter_services_by_trade_or_category(): void
    {
        $category = ServiceCategory::factory()->create(['name' => 'HVAC & Climate', 'slug' => 'hvac-climate']);

        $this->createProviderWithService(
            [],
            ['category' => 'Plumbing'],
            ['title' => 'Plumbing Drain Cleaning', 'category' => 'Plumbing']
        );

        $this->createProviderWithService(
            [],
            ['category' => 'HVAC & Climate'],
            ['title' => 'Air Conditioning Maintenance', 'category' => 'HVAC & Climate', 'category_id' => $category->id]
        );

        // Filter by category slug
        $responseSlug = $this->getJson('/api/v1/services?trade=hvac-climate');
        $responseSlug->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'Air Conditioning Maintenance']);

        // Filter by category string
        $responseString = $this->getJson('/api/v1/services?trade=Plumbing');
        $responseString->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'Plumbing Drain Cleaning']);

        // Filter by category_id
        $responseId = $this->getJson("/api/v1/services?category_id={$category->id}");
        $responseId->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'Air Conditioning Maintenance']);
    }

    public function test_guest_can_filter_services_by_location_city(): void
    {
        $this->createProviderWithService(
            [],
            ['city' => 'Tunis'],
            ['title' => 'Tunis Home Cleaning']
        );

        $this->createProviderWithService(
            [],
            ['city' => 'Sfax'],
            ['title' => 'Sfax Home Cleaning']
        );

        $response = $this->getJson('/api/v1/services?location=Tunis');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'Tunis Home Cleaning']);

        $response->assertJsonMissing(['title' => 'Sfax Home Cleaning']);
    }

    public function test_guest_search_excludes_pending_or_rejected_services(): void
    {
        // Approved service
        $this->createProviderWithService([], [], ['title' => 'Approved Active Service', 'status' => 'approved', 'is_active' => true]);

        // Pending service
        $this->createProviderWithService([], [], ['title' => 'Pending Moderation Service', 'status' => 'pending', 'is_active' => false]);

        // Rejected service
        $this->createProviderWithService([], [], ['title' => 'Rejected Spam Service', 'status' => 'rejected', 'is_active' => false]);

        $response = $this->getJson('/api/v1/services');

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'Approved Active Service'])
            ->assertJsonMissing(['title' => 'Pending Moderation Service'])
            ->assertJsonMissing(['title' => 'Rejected Spam Service']);
    }

    public function test_guest_search_excludes_inactive_services(): void
    {
        $this->createProviderWithService([], [], [
            'title'     => 'Inactive Service By Provider',
            'status'    => 'approved',
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/v1/services');

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_guest_search_excludes_services_of_suspended_providers(): void
    {
        // Provider suspended by admin
        $this->createProviderWithService(
            ['status' => 'suspended'],
            [],
            ['title' => 'Suspended Provider Service', 'status' => 'approved', 'is_active' => true]
        );

        $response = $this->getJson('/api/v1/services');

        $response->assertOk()
            ->assertJsonCount(0, 'data');
    }

    public function test_guest_can_filter_by_price_range_and_sort(): void
    {
        $this->createProviderWithService([], [], ['title' => 'Cheap Fix', 'price' => 30.00]);
        $this->createProviderWithService([], [], ['title' => 'Moderate Work', 'price' => 100.00]);
        $this->createProviderWithService([], [], ['title' => 'Premium Renovation', 'price' => 350.00]);

        $responsePrice = $this->getJson('/api/v1/services?min_price=50&max_price=200');
        $responsePrice->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonFragment(['title' => 'Moderate Work']);

        $responseSortDesc = $this->getJson('/api/v1/services?sort=price_desc');
        $responseSortDesc->assertOk();
        $this->assertEquals('Premium Renovation', $responseSortDesc->json('data.0.title'));

        $responseSortAsc = $this->getJson('/api/v1/services?sort=price_asc');
        $responseSortAsc->assertOk();
        $this->assertEquals('Cheap Fix', $responseSortAsc->json('data.0.title'));
    }

    public function test_guest_can_view_single_service_details_via_api(): void
    {
        $data = $this->createProviderWithService(
            ['name' => 'Mohamed Ben Ali', 'is_verified' => true],
            ['city' => 'Ariana', 'business_name' => 'Ben Ali Climatisation'],
            ['title' => 'AC Gas Refill', 'price' => 85.00]
        );

        $response = $this->getJson("/api/v1/services/{$data['service']->id}");

        $response->assertOk()
            ->assertJsonPath('data.title', 'AC Gas Refill')
            ->assertJsonPath('data.price', 85)
            ->assertJsonPath('data.provider.name', 'Mohamed Ben Ali')
            ->assertJsonPath('data.provider.is_verified', true)
            ->assertJsonPath('data.provider.profile.city', 'Ariana');
    }

    public function test_guest_can_view_filters_metadata(): void
    {
        ServiceCategory::factory()->create(['name' => 'Painting & Wall', 'is_active' => true]);
        $this->createProviderWithService([], ['city' => 'Bizerte'], ['title' => 'Wall Painting']);

        $response = $this->getJson('/api/v1/services/filters');

        $response->assertOk()
            ->assertJsonStructure(['categories', 'cities'])
            ->assertJsonFragment(['name' => 'Painting & Wall']);

        $this->assertContains('Bizerte', $response->json('cities'));
    }

    public function test_guest_can_access_web_services_index_and_search(): void
    {
        $this->createProviderWithService(
            ['name' => 'Youssef Electric', 'is_verified' => true],
            ['city' => 'Tunis'],
            ['title' => 'Full House Rewiring', 'price' => 200.00]
        );

        $response = $this->get('/services?keyword=Rewiring&location=Tunis');

        $response->assertOk()
            ->assertSee('Full House Rewiring')
            ->assertSee('Tunis')
            ->assertSee('Youssef Electric')
            ->assertSee('Verified')
            ->assertSee('200.00')
            ->assertSee('modalRegisterLink')
            ->assertSee("encodeURIComponent('/services/' + serviceId)", false);
    }

    public function test_guest_can_view_web_service_detail_page(): void
    {
        $data = $this->createProviderWithService(
            ['name' => 'Sami Trabelsi', 'is_verified' => true],
            ['city' => 'Sousse', 'business_name' => 'Sousse Carpentry Studio'],
            ['title' => 'Custom Wooden Shelving', 'price' => 150.00]
        );

        $response = $this->get("/services/{$data['service']->id}");

        $response->assertOk()
            ->assertSee('Custom Wooden Shelving')
            ->assertSee('Sousse Carpentry Studio')
            ->assertSee('Sami Trabelsi')
            ->assertSee('150.00')
            ->assertSee('Sign In to Book This Service')
            ->assertSee('New Client? Create Account')
            ->assertSee("openGuestAuthPrompt('save this provider')", false)
            ->assertSee('getSafePostAuthRedirect', false)
            ->assertSee('redirect=%2Fservices%2F' . $data['service']->id, false);
    }

    public function test_unapproved_service_returns_404_on_web_show(): void
    {
        $data = $this->createProviderWithService([], [], [
            'status'    => 'pending',
            'is_active' => false,
        ]);

        $response = $this->get("/services/{$data['service']->id}");

        $response->assertNotFound();
    }

    public function test_filter_services_by_provider_availability(): void
    {
        $this->createProviderWithService([], ['is_available' => true], ['title' => 'Available Plumber Service']);
        $this->createProviderWithService([], ['is_available' => false], ['title' => 'Offline Plumber Service']);

        $response = $this->getJson('/api/v1/services?available_only=1');

        $response->assertOk()
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.title', 'Available Plumber Service');
    }
}

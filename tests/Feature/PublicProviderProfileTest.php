<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\ProviderProfile;
use App\Models\Review;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicProviderProfileTest extends TestCase
{
    use RefreshDatabase;

    private function createProviderWithFullProfile(array $userOverrides = [], array $profileOverrides = []): User
    {
        $provider = User::factory()->create(array_merge([
            'name'        => 'Tarek Mansour',
            'role'        => UserRole::PROVIDER,
            'status'      => 'active',
            'is_verified' => true,
        ], $userOverrides));

        ProviderProfile::create(array_merge([
            'user_id'          => $provider->id,
            'business_name'    => 'Mansour HVAC & Heat',
            'category'         => 'HVAC & Climate',
            'city'             => 'Tunis',
            'hourly_rate'      => 45.00,
            'bio'              => 'Licensed thermal engineer with over 10 years experience.',
            'skills'           => ['Compressor Repair', 'Gas Recharging', 'Smart Thermostats'],
            'is_available'     => true,
            'portfolio_photos' => [
                [
                    'url'     => 'https://images.unsplash.com/photo-1621905251189-08b45d6a269e',
                    'title'   => 'Commercial Split AC Installation',
                    'caption' => 'Installed inverter climate system in office building',
                ],
                [
                    'url'     => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758',
                    'title'   => 'Ductwork Ventilation Maintenance',
                    'caption' => 'Cleaned air circulation ducts',
                ],
            ],
        ], $profileOverrides));

        return $provider;
    }

    public function test_guest_can_fetch_provider_public_profile_via_api(): void
    {
        $provider = $this->createProviderWithFullProfile();

        // Add 1 approved active service
        Service::factory()->approved()->create([
            'provider_id'    => $provider->id,
            'title'          => 'AC Filter Cleaning & Gas Top-up',
            'category'       => 'HVAC & Climate',
            'price'          => 90.00,
            'duration_hours' => 2.0,
            'is_active'      => true,
            'status'         => 'approved',
        ]);

        // Add 1 unapproved service (must not appear)
        Service::factory()->create([
            'provider_id'    => $provider->id,
            'title'          => 'Pending AC Service',
            'status'         => 'pending',
            'is_active'      => false,
        ]);

        // Add client and review
        $client = User::factory()->create(['role' => UserRole::CLIENT, 'name' => 'Amine Karray']);
        $booking = Booking::factory()->create([
            'client_id'      => $client->id,
            'provider_id'    => $provider->id,
            'category'       => 'HVAC & Climate',
            'service_title'  => 'Repair consultation',
            'total_price'    => 90.00,
            'scheduled_at'   => now()->subDays(3),
            'status'         => 'completed',
        ]);

        Review::create([
            'booking_id'  => $booking->id,
            'client_id'   => $client->id,
            'provider_id' => $provider->id,
            'rating'      => 5,
            'comment'     => 'Excellent fast repair, highly recommended professional!',
        ]);

        $response = $this->getJson("/api/v1/providers/{$provider->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $provider->id)
            ->assertJsonPath('data.name', 'Tarek Mansour')
            ->assertJsonPath('data.is_verified', true)
            ->assertJsonPath('data.trade', 'HVAC & Climate')
            ->assertJsonPath('data.business_name', 'Mansour HVAC & Heat')
            ->assertJsonPath('data.hourly_rate', 45)
            ->assertJsonPath('data.city', 'Tunis')
            ->assertJsonPath('data.stats.average_rating', 5)
            ->assertJsonPath('data.stats.reviews_count', 1)
            ->assertJsonPath('data.stats.completed_jobs', 1)
            ->assertJsonCount(3, 'data.skills')
            ->assertJsonCount(2, 'data.portfolio_photos')
            ->assertJsonCount(1, 'data.services')
            ->assertJsonPath('data.services.0.title', 'AC Filter Cleaning & Gas Top-up')
            ->assertJsonCount(1, 'data.reviews')
            ->assertJsonPath('data.reviews.0.client_name', 'Amine Karray')
            ->assertJsonPath('data.reviews.0.rating', 5);
    }

    public function test_suspended_provider_returns_404_on_api(): void
    {
        $provider = $this->createProviderWithFullProfile(['status' => 'suspended']);

        $response = $this->getJson("/api/v1/providers/{$provider->id}");

        $response->assertNotFound();
    }

    public function test_non_provider_user_returns_404_on_api(): void
    {
        $client = User::factory()->create(['role' => UserRole::CLIENT]);

        $response = $this->getJson("/api/v1/providers/{$client->id}");

        $response->assertNotFound();
    }

    public function test_guest_can_view_public_provider_profile_web_page(): void
    {
        $provider = $this->createProviderWithFullProfile();

        Service::factory()->approved()->create([
            'provider_id'    => $provider->id,
            'title'          => 'Emergency Boiler Maintenance',
            'price'          => 120.00,
            'is_active'      => true,
            'status'         => 'approved',
        ]);

        $client = User::factory()->create(['role' => UserRole::CLIENT, 'name' => 'Salma Ben Amor']);
        $booking = Booking::factory()->create([
            'client_id'      => $client->id,
            'provider_id'    => $provider->id,
            'category'       => 'HVAC & Climate',
            'service_title'  => 'Boiler maintenance',
            'total_price'    => 120.00,
            'scheduled_at'   => now()->subDays(2),
            'status'         => 'completed',
        ]);

        Review::create([
            'booking_id'  => $booking->id,
            'client_id'   => $client->id,
            'provider_id' => $provider->id,
            'rating'      => 5,
            'comment'     => 'Very clean job and on time.',
        ]);

        $response = $this->get("/providers/{$provider->id}");

        $response->assertOk()
            ->assertSee('Tarek Mansour')
            ->assertSee('Mansour HVAC & Heat')
            ->assertSee('HVAC & Climate')
            ->assertSee('45.00')
            ->assertSee('Emergency Boiler Maintenance')
            ->assertSee('Compressor Repair')
            ->assertSee('Commercial Split AC Installation')
            ->assertSee('Salma Ben Amor')
            ->assertSee('Very clean job and on time.')
            ->assertSee('Verified Pro');
    }

    public function test_suspended_provider_returns_404_on_web_page(): void
    {
        $provider = $this->createProviderWithFullProfile(['status' => 'suspended']);

        $response = $this->get("/providers/{$provider->id}");

        $response->assertNotFound();
    }
}

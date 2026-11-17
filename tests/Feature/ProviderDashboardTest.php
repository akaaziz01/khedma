<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_provider_can_fetch_real_dashboard_data(): void
    {
        $provider = User::create([
            'name' => 'Hassan Pro',
            'email' => 'hassan@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::PROVIDER,
            'phone' => '+212600112233',
        ]);
        $provider->providerProfile()->create([
            'business_name' => 'Hassan HVAC Solutions',
            'category' => 'HVAC & AC',
            'hourly_rate' => 180.00,
            'city' => 'Casablanca',
            'is_available' => true,
        ]);

        $client = User::create([
            'name' => 'Test Client',
            'email' => 'client@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::CLIENT,
        ]);

        // Add 1 completed booking
        $b1 = Booking::create([
            'booking_number' => 'KHD-TEST-001',
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'service_title' => 'AC Cleaning',
            'category' => 'HVAC & AC',
            'status' => 'completed',
            'total_price' => 300.00,
            'scheduled_at' => now()->subDay(),
            'address' => 'Anfa, Casablanca',
            'city' => 'Casablanca',
        ]);

        // Add 1 pending lead
        Booking::create([
            'booking_number' => 'KHD-TEST-002',
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'service_title' => 'AC Gas Refill',
            'category' => 'HVAC & AC',
            'status' => 'pending',
            'total_price' => 450.00,
            'scheduled_at' => now()->addDay(),
            'address' => 'Gueliz, Marrakech',
            'city' => 'Marrakech',
        ]);

        $token = $provider->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->getJson('/api/v1/provider/dashboard');

        $response->assertStatus(200)
            ->assertJsonPath('metrics.total_revenue', 300)
            ->assertJsonPath('metrics.completed_jobs', 1)
            ->assertJsonPath('metrics.pending_leads', 1)
            ->assertJsonPath('provider.name', 'Hassan Pro')
            ->assertJsonCount(2, 'bookings');
    }

    public function test_provider_can_update_booking_status(): void
    {
        $provider = User::create([
            'name' => 'Hassan Pro',
            'email' => 'hassan2@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::PROVIDER,
        ]);
        $provider->providerProfile()->create([
            'category' => 'Plumbing',
        ]);

        $client = User::create([
            'name' => 'Test Client',
            'email' => 'client2@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::CLIENT,
        ]);

        $booking = Booking::create([
            'booking_number' => 'KHD-TEST-003',
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'service_title' => 'Pipe Fixing',
            'category' => 'Plumbing',
            'status' => 'pending',
            'total_price' => 200.00,
            'scheduled_at' => now()->addHours(3),
            'address' => 'Casablanca',
            'city' => 'Casablanca',
        ]);

        $token = $provider->createToken('test_token')->plainTextToken;

        // Accept booking
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson("/api/v1/provider/bookings/{$booking->id}/status", [
                'status' => 'accepted',
                'provider_notes' => 'I will be there on time.',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('booking.status', 'accepted');

        $this->assertDatabaseHas('bookings', [
            'id' => $booking->id,
            'status' => 'accepted',
            'provider_notes' => 'I will be there on time.',
        ]);
    }

    public function test_provider_can_toggle_availability(): void
    {
        $provider = User::create([
            'name' => 'Hassan Pro',
            'email' => 'hassan3@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::PROVIDER,
        ]);
        $provider->providerProfile()->create([
            'category' => 'Plumbing',
            'is_available' => true,
        ]);

        $token = $provider->createToken('test_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/provider/availability');

        $response->assertStatus(200)
            ->assertJsonPath('is_available', false);

        $this->assertDatabaseHas('provider_profiles', [
            'user_id' => $provider->id,
            'is_available' => false,
        ]);
    }
}

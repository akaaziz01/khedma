<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderServiceManagementTest extends TestCase
{
    use RefreshDatabase;

    private function createProvider(): User
    {
        $provider = User::create([
            'name' => 'Foued Trabelsi',
            'email' => 'foued@khedma.tn',
            'password' => 'Password123!',
            'role' => UserRole::PROVIDER,
            'phone' => '+216 20 999 888',
        ]);

        $provider->providerProfile()->create([
            'business_name' => 'Trabelsi Tech & Home',
            'category' => 'Plumbing',
            'hourly_rate' => 40.00,
            'city' => 'Tunis',
        ]);

        return $provider;
    }

    public function test_provider_can_create_a_service_with_rate_and_duration(): void
    {
        $provider = $this->createProvider();
        $token = $provider->createToken('auth_token')->plainTextToken;

        ServiceCategory::create([
            'name' => 'Plumbing',
            'slug' => 'plumbing',
            'icon' => 'bi-droplet',
            'is_active' => true,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/provider/services', [
                'title' => 'Water Heater Thermostat Replacement',
                'category' => 'Plumbing',
                'price' => 65.50,
                'duration_hours' => 2.0,
                'description' => 'Diagnosing and replacing faulty heating elements or thermostats with 6-month warranty.',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('service.title', 'Water Heater Thermostat Replacement')
            ->assertJsonPath('service.price', '65.50')
            ->assertJsonPath('service.duration_hours', '2.0');

        $this->assertDatabaseHas('services', [
            'provider_id' => $provider->id,
            'title' => 'Water Heater Thermostat Replacement',
            'price' => 65.50,
        ]);
    }

    public function test_provider_can_edit_existing_service_rates_and_details(): void
    {
        $provider = $this->createProvider();
        $token = $provider->createToken('auth_token')->plainTextToken;

        $service = Service::create([
            'provider_id' => $provider->id,
            'title' => 'Pipe Leak Repair',
            'category' => 'Plumbing',
            'price' => 45.00,
            'duration_hours' => 1.0,
            'description' => 'Fixing minor pipe leaks.',
            'status' => 'approved',
            'is_active' => true,
        ]);

        // Provider updates the service rate and title
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->putJson("/api/v1/provider/services/{$service->id}", [
                'title' => 'Emergency Copper Pipe Leak Repair & Pressure Testing',
                'category' => 'Plumbing',
                'price' => 55.00,
                'duration_hours' => 1.5,
                'description' => 'Emergency response, copper pipe repair, leak detection, and water pressure inspection.',
                'is_active' => true,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('service.title', 'Emergency Copper Pipe Leak Repair & Pressure Testing')
            ->assertJsonPath('service.price', '55.00')
            ->assertJsonPath('service.duration_hours', '1.5');

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
            'provider_id' => $provider->id,
            'price' => 55.00,
            'duration_hours' => 1.5,
        ]);
    }

    public function test_provider_cannot_edit_service_belonging_to_another_provider(): void
    {
        $provider1 = $this->createProvider();
        $provider2 = User::create([
            'name' => 'Other Pro',
            'email' => 'other@khedma.tn',
            'password' => 'Password123!',
            'role' => UserRole::PROVIDER,
        ]);

        $otherService = Service::create([
            'provider_id' => $provider2->id,
            'title' => 'Other Service',
            'category' => 'Electrical',
            'price' => 80.00,
            'status' => 'approved',
            'is_active' => true,
        ]);

        $token1 = $provider1->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token1)
            ->putJson("/api/v1/provider/services/{$otherService->id}", [
                'title' => 'Hacked Title',
                'category' => 'Electrical',
                'price' => 10.00,
            ]);

        $response->assertStatus(404);

        $this->assertDatabaseHas('services', [
            'id' => $otherService->id,
            'price' => 80.00,
        ]);
    }

    public function test_provider_can_delete_a_service_with_no_active_bookings(): void
    {
        $provider = $this->createProvider();
        $token = $provider->createToken('auth_token')->plainTextToken;

        $service = Service::create([
            'provider_id' => $provider->id,
            'title' => 'Old Obsolete Service',
            'category' => 'Plumbing',
            'price' => 20.00,
            'status' => 'approved',
            'is_active' => true,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/v1/provider/services/{$service->id}");

        $response->assertStatus(200);

        $this->assertDatabaseMissing('services', [
            'id' => $service->id,
        ]);
    }

    public function test_provider_cannot_delete_service_with_active_bookings(): void
    {
        $provider = $this->createProvider();
        $token = $provider->createToken('auth_token')->plainTextToken;

        $client = User::create([
            'name' => 'Client User',
            'email' => 'client.active@khedma.tn',
            'password' => 'Password123!',
            'role' => UserRole::CLIENT,
        ]);

        $service = Service::create([
            'provider_id' => $provider->id,
            'title' => 'Active Booked Service',
            'category' => 'Plumbing',
            'price' => 60.00,
            'status' => 'approved',
            'is_active' => true,
        ]);

        Booking::create([
            'booking_number' => 'BK-ACTIVE-01',
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'service_id' => $service->id,
            'service_title' => $service->title,
            'category' => 'Plumbing',
            'status' => 'accepted', // active booking!
            'total_price' => 60.00,
            'scheduled_at' => now()->addDays(2),
            'address' => 'Ariana Center',
            'city' => 'Ariana',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/v1/provider/services/{$service->id}");

        $response->assertStatus(422)
            ->assertJsonPath('message', 'Cannot delete service with active bookings. Please complete or cancel those bookings first.');

        $this->assertDatabaseHas('services', [
            'id' => $service->id,
        ]);
    }

    public function test_client_cannot_manage_provider_services(): void
    {
        $client = User::create([
            'name' => 'Regular Client',
            'email' => 'regular@khedma.tn',
            'password' => 'Password123!',
            'role' => UserRole::CLIENT,
        ]);

        $token = $client->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/provider/services', [
                'title' => 'Client Trying To Create',
                'category' => 'Plumbing',
                'price' => 100.00,
            ]);

        // A client calling provider service endpoint should be denied or not permitted
        $this->assertDatabaseMissing('services', [
            'title' => 'Client Trying To Create',
        ]);
    }
}

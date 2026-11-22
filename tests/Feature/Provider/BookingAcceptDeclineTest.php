<?php

namespace Tests\Feature\Provider;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\ProviderProfile;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BookingAcceptDeclineTest extends TestCase
{
    use RefreshDatabase;

    private function createProvider(array $overrides = []): User
    {
        $provider = User::factory()->create(array_merge([
            'role'   => UserRole::PROVIDER,
            'status' => 'active',
        ], $overrides));

        ProviderProfile::create([
            'user_id'    => $provider->id,
            'category'   => 'Plumbing',
            'city'       => 'Tunis',
            'hourly_rate'=> 40.00,
            'bio'        => 'Expert plumber.',
            'is_available' => true,
        ]);

        return $provider;
    }

    private function createClient(array $overrides = []): User
    {
        return User::factory()->create(array_merge([
            'role'   => UserRole::CLIENT,
            'status' => 'active',
        ], $overrides));
    }

    private function createBooking(User $provider, User $client, string $status = 'pending'): Booking
    {
        return Booking::create([
            'booking_number' => 'BK-' . strtoupper(Str::random(8)),
            'client_id'      => $client->id,
            'provider_id'    => $provider->id,
            'service_title'  => 'Pipe Leak Repair',
            'category'       => 'Plumbing',
            'status'         => $status,
            'total_price'    => 85.00,
            'scheduled_at'   => now()->addDay(),
            'address'        => '12 Rue Habib Bourguiba',
            'city'           => 'Tunis',
        ]);
    }

    // =========================================================================
    // 1. Accept Booking
    // =========================================================================

    public function test_provider_can_accept_pending_booking(): void
    {
        $provider = $this->createProvider();
        $client   = $this->createClient();
        $booking  = $this->createBooking($provider, $client, 'pending');

        Sanctum::actingAs($provider);

        $response = $this->postJson("/api/v1/provider/bookings/{$booking->id}/accept");

        $response->assertStatus(200)
            ->assertJsonPath('booking.status', 'accepted');

        $this->assertEquals('accepted', $booking->fresh()->status);
        $this->assertNotNull($booking->fresh()->accepted_at);
    }

    public function test_provider_cannot_accept_already_accepted_booking(): void
    {
        $provider = $this->createProvider();
        $client   = $this->createClient();
        $booking  = $this->createBooking($provider, $client, 'accepted');

        Sanctum::actingAs($provider);

        $response = $this->postJson("/api/v1/provider/bookings/{$booking->id}/accept");

        $response->assertStatus(422);
    }

    public function test_provider_cannot_accept_another_providers_booking(): void
    {
        $providerA = $this->createProvider();
        $providerB = $this->createProvider();
        $client    = $this->createClient();
        $booking   = $this->createBooking($providerA, $client, 'pending');

        Sanctum::actingAs($providerB);

        // Should 404 since it doesn't belong to providerB
        $response = $this->postJson("/api/v1/provider/bookings/{$booking->id}/accept");

        $response->assertStatus(404);
    }

    public function test_client_cannot_call_provider_accept_endpoint(): void
    {
        $provider = $this->createProvider();
        $client   = $this->createClient();
        $booking  = $this->createBooking($provider, $client, 'pending');

        Sanctum::actingAs($client);

        // Client uses provider endpoint — should return 403 or 404 (not provider)
        $response = $this->postJson("/api/v1/provider/bookings/{$booking->id}/accept");

        $this->assertContains($response->status(), [403, 404]);
    }

    // =========================================================================
    // 2. Decline Booking
    // =========================================================================

    public function test_provider_can_decline_pending_booking_with_reason(): void
    {
        $provider = $this->createProvider();
        $client   = $this->createClient();
        $booking  = $this->createBooking($provider, $client, 'pending');

        Sanctum::actingAs($provider);

        $response = $this->postJson("/api/v1/provider/bookings/{$booking->id}/decline", [
            'declined_reason' => 'I am fully booked that week and cannot accommodate this request.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('booking.status', 'declined');

        $fresh = $booking->fresh();
        $this->assertEquals('declined', $fresh->status);
        $this->assertNotNull($fresh->declined_at);
        $this->assertStringContainsString('fully booked', $fresh->declined_reason);
    }

    public function test_provider_cannot_decline_without_reason(): void
    {
        $provider = $this->createProvider();
        $client   = $this->createClient();
        $booking  = $this->createBooking($provider, $client, 'pending');

        Sanctum::actingAs($provider);

        $response = $this->postJson("/api/v1/provider/bookings/{$booking->id}/decline", [
            'declined_reason' => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['declined_reason']);
    }

    public function test_provider_cannot_decline_accepted_booking(): void
    {
        $provider = $this->createProvider();
        $client   = $this->createClient();
        $booking  = $this->createBooking($provider, $client, 'accepted');

        Sanctum::actingAs($provider);

        $response = $this->postJson("/api/v1/provider/bookings/{$booking->id}/decline", [
            'declined_reason' => 'Changed my mind about this booking.',
        ]);

        $response->assertStatus(422);
        $this->assertEquals('accepted', $booking->fresh()->status);
    }

    // =========================================================================
    // 3. Immutable States
    // =========================================================================

    public function test_declined_booking_cannot_be_re_accepted(): void
    {
        $provider = $this->createProvider();
        $client   = $this->createClient();
        $booking  = $this->createBooking($provider, $client, 'declined');

        Sanctum::actingAs($provider);

        $response = $this->postJson("/api/v1/provider/bookings/{$booking->id}/accept");

        $response->assertStatus(422);
        $this->assertEquals('declined', $booking->fresh()->status);
    }

    public function test_completed_booking_cannot_be_changed(): void
    {
        $provider = $this->createProvider();
        $client   = $this->createClient();
        $booking  = $this->createBooking($provider, $client, 'completed');

        Sanctum::actingAs($provider);

        $response = $this->postJson("/api/v1/provider/bookings/{$booking->id}/accept");
        $response->assertStatus(422);

        $response2 = $this->postJson("/api/v1/provider/bookings/{$booking->id}/decline", [
            'declined_reason' => 'Trying to decline completed booking.',
        ]);
        $response2->assertStatus(422);

        $this->assertEquals('completed', $booking->fresh()->status);
    }

    // =========================================================================
    // 4. Lifecycle Transitions
    // =========================================================================

    public function test_provider_can_start_accepted_booking(): void
    {
        $provider = $this->createProvider();
        $client   = $this->createClient();
        $booking  = $this->createBooking($provider, $client, 'accepted');

        Sanctum::actingAs($provider);

        $response = $this->postJson("/api/v1/provider/bookings/{$booking->id}/start");

        $response->assertStatus(200)
            ->assertJsonPath('booking.status', 'in_progress');

        $this->assertEquals('in_progress', $booking->fresh()->status);
    }

    public function test_provider_can_complete_in_progress_booking(): void
    {
        $provider = $this->createProvider();
        $client   = $this->createClient();
        $booking  = $this->createBooking($provider, $client, 'in_progress');

        Sanctum::actingAs($provider);

        $response = $this->postJson("/api/v1/provider/bookings/{$booking->id}/complete");

        $response->assertStatus(200)
            ->assertJsonPath('booking.status', 'completed');

        $fresh = $booking->fresh();
        $this->assertEquals('completed', $fresh->status);
        $this->assertNotNull($fresh->completed_at);
    }

    // =========================================================================
    // 5. Generic updateBookingStatus — Transition Guard
    // =========================================================================

    public function test_generic_endpoint_rejects_invalid_transition(): void
    {
        $provider = $this->createProvider();
        $client   = $this->createClient();
        $booking  = $this->createBooking($provider, $client, 'completed');

        Sanctum::actingAs($provider);

        $response = $this->postJson("/api/v1/provider/bookings/{$booking->id}/status", [
            'status' => 'pending',
        ]);

        // 'pending' is no longer a valid target status (not in allowed list)
        $response->assertStatus(422);
    }
}

<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StripePaymentTest extends TestCase
{
    use RefreshDatabase;

    private User $client;
    private User $provider;
    private Booking $booking;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = User::factory()->create([
            'role'     => 'client',
            'name'     => 'Test Client',
            'email'    => 'client@khedma.tn',
            'password' => bcrypt('password123'),
        ]);

        $this->provider = User::factory()->create([
            'role'     => 'provider',
            'name'     => 'Test Provider',
            'email'    => 'provider@khedma.tn',
            'password' => bcrypt('password123'),
        ]);

        $service = Service::create([
            'provider_id' => $this->provider->id,
            'title'       => 'Plumbing Repair',
            'category'    => 'Plumbing',
            'description' => 'Fix leaking pipe',
            'price'       => 120.00,
            'city'        => 'Tunis',
            'is_active'   => true,
        ]);

        $this->booking = Booking::create([
            'booking_number' => 'BKG-TEST-12345',
            'client_id'      => $this->client->id,
            'provider_id'    => $this->provider->id,
            'service_id'     => $service->id,
            'service_title'  => $service->title,
            'category'       => $service->category,
            'status'         => 'pending',
            'payment_status' => 'unpaid',
            'total_price'    => 120.00,
            'scheduled_at'   => now()->addDays(2),
            'address'        => 'Avenue Habib Bourguiba',
            'city'           => 'Tunis',
        ]);
    }

    public function test_client_can_fetch_their_bookings_with_payment_status(): void
    {
        $response = $this->actingAs($this->client, 'sanctum')
            ->getJson('/api/v1/client/bookings');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'booking_number',
                        'service_title',
                        'category',
                        'status',
                        'payment_status',
                        'is_paid',
                        'total_price',
                    ],
                ],
            ]);

        $this->assertEquals('unpaid', $response->json('data.0.payment_status'));
        $this->assertFalse($response->json('data.0.is_paid'));
    }

    public function test_client_can_check_payment_status_of_their_booking(): void
    {
        $response = $this->actingAs($this->client, 'sanctum')
            ->getJson("/api/v1/client/bookings/{$this->booking->id}/payment-status");

        $response->assertStatus(200)
            ->assertJson([
                'booking_id'     => $this->booking->id,
                'booking_number' => 'BKG-TEST-12345',
                'payment_status' => 'unpaid',
                'is_paid'        => false,
            ]);
    }

    public function test_client_can_create_a_direct_booking(): void
    {
        $response = $this->actingAs($this->client, 'sanctum')
            ->postJson('/api/v1/client/bookings', [
                'provider_id'   => $this->provider->id,
                'service_title' => 'Emergency Leak Fix',
                'category'      => 'Plumbing',
                'total_price'   => 80.00,
                'scheduled_at'  => now()->addDays(1)->toIso8601String(),
                'address'       => '15 Avenue de Paris',
                'city'          => 'Tunis',
                'client_notes'  => 'Urgent please',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.service_title', 'Emergency Leak Fix')
            ->assertJsonPath('data.status', 'pending')
            ->assertJsonPath('data.payment_status', 'unpaid')
            ->assertJsonPath('data.total_price', '80.00')
            ->assertJsonPath('data.commission_rate', '10.00')
            ->assertJsonPath('data.commission_amount', '8.00')
            ->assertJsonPath('data.provider_amount', '72.00');

        $this->assertDatabaseHas('bookings', [
            'client_id'         => $this->client->id,
            'provider_id'       => $this->provider->id,
            'service_title'     => 'Emergency Leak Fix',
            'commission_rate'   => 10.00,
            'commission_amount' => 8.00,
            'provider_amount'   => 72.00,
        ]);
    }

    public function test_another_user_cannot_view_or_pay_foreign_booking(): void
    {
        $intruder = User::factory()->create(['role' => 'client']);

        $response = $this->actingAs($intruder, 'sanctum')
            ->postJson("/api/v1/client/bookings/{$this->booking->id}/stripe-checkout");

        $response->assertStatus(403);
    }

    public function test_client_can_create_stripe_checkout_session(): void
    {
        $response = $this->actingAs($this->client, 'sanctum')
            ->postJson("/api/v1/client/bookings/{$this->booking->id}/stripe-checkout");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'checkout_url',
                'session_id',
                'booking_id',
            ]);

        $this->assertStringStartsWith('https://checkout.stripe.com', $response->json('checkout_url'));
    }

    public function test_webhook_marks_booking_as_paid_on_checkout_session_completed(): void
    {
        $payload = [
            'id'   => 'evt_test_webhook_123',
            'type' => 'checkout.session.completed',
            'data' => [
                'object' => [
                    'id'             => 'cs_test_session_abc123',
                    'payment_intent' => 'pi_test_intent_987',
                    'payment_status' => 'paid',
                    'metadata'       => [
                        'booking_id'     => (string) $this->booking->id,
                        'booking_number' => $this->booking->booking_number,
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/stripe/webhook', $payload);

        $response->assertStatus(200)
            ->assertJson(['received' => true]);

        $this->booking->refresh();

        $this->assertEquals('paid', $this->booking->payment_status);
        $this->assertEquals('stripe', $this->booking->payment_method);
        $this->assertEquals('pi_test_intent_987', $this->booking->stripe_payment_intent_id);
        $this->assertEquals('cs_test_session_abc123', $this->booking->stripe_session_id);
        $this->assertTrue($this->booking->isPaid());
    }

    public function test_success_and_cancel_views_are_accessible(): void
    {
        $this->get('/payment/success?session_id=cs_test_123&booking_id=' . $this->booking->id)
            ->assertStatus(200)
            ->assertSee('Paiement Confirmé', false);

        $this->get('/payment/cancel?booking_id=' . $this->booking->id)
            ->assertStatus(200)
            ->assertSee('Paiement non finalisé', false);
    }
}

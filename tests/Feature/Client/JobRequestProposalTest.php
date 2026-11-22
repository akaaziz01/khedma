<?php

namespace Tests\Feature\Client;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\JobRequest;
use App\Models\Proposal;
use App\Models\ProviderProfile;
use App\Models\Review;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class JobRequestProposalTest extends TestCase
{
    use RefreshDatabase;

    private function createClient(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'role'   => UserRole::CLIENT,
            'status' => 'active',
        ], $attributes));
    }

    private function createProvider(array $userAttributes = [], array $profileAttributes = []): User
    {
        $provider = User::factory()->create(array_merge([
            'role'        => UserRole::PROVIDER,
            'status'      => 'active',
            'is_verified' => true,
        ], $userAttributes));

        ProviderProfile::create(array_merge([
            'user_id'       => $provider->id,
            'business_name' => 'Expert Craft',
            'category'      => 'Plumbing',
            'city'          => 'Tunis',
            'hourly_rate'   => 45.00,
            'bio'           => 'Licensed professional with certified expertise.',
            'skills'        => ['Plumbing', 'Piping'],
            'is_available'  => true,
        ], $profileAttributes));

        return $provider;
    }

    // =========================================================================
    // 1. Job Requests Creation & Permissions
    // =========================================================================

    public function test_client_can_create_a_job_request(): void
    {
        $client = $this->createClient();
        Sanctum::actingAs($client);

        $payload = [
            'title'          => 'Emergency pipe leak in kitchen',
            'category'       => 'Plumbing',
            'description'    => 'Water leaking under the main kitchen sink. Needs immediate repair and pipe sealing.',
            'budget'         => 120.00,
            'city'           => 'Tunis',
            'address'        => '12 Rue Habib Bourguiba, Tunis',
            'urgency'        => 'urgent',
            'preferred_date' => now()->addDay()->format('Y-m-d H:i:s'),
        ];

        $response = $this->postJson('/api/v1/client/job-requests', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Emergency pipe leak in kitchen')
            ->assertJsonPath('data.category', 'Plumbing')
            ->assertJsonPath('data.city', 'Tunis')
            ->assertJsonPath('data.status', 'open');

        $this->assertDatabaseHas('job_requests', [
            'client_id' => $client->id,
            'title'     => 'Emergency pipe leak in kitchen',
            'city'      => 'Tunis',
            'status'    => 'open',
        ]);
    }

    public function test_client_can_post_request_using_location_and_deadline_fields(): void
    {
        $client = $this->createClient();
        Sanctum::actingAs($client);

        $response = $this->postJson('/api/v1/client/job-requests', [
            'title'       => 'Install an air conditioner',
            'category'    => 'HVAC',
            'description' => 'Install a split air conditioner in the living room.',
            'budget'      => 650,
            'location'    => 'Sousse, Hammam Sousse',
            'address'     => 'Rue des Palmiers',
            'deadline'    => now()->addWeek()->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.location', 'Sousse, Hammam Sousse')
            ->assertJsonPath('data.budget', 650);

        $this->assertDatabaseHas('job_requests', [
            'client_id' => $client->id,
            'location'  => 'Sousse, Hammam Sousse',
            'budget'    => 650,
            'status'    => 'open',
        ]);
    }

    public function test_job_request_requires_budget_location_and_future_deadline(): void
    {
        $client = $this->createClient();
        Sanctum::actingAs($client);

        $response = $this->postJson('/api/v1/client/job-requests', [
            'title'       => 'Repair an electrical outlet',
            'category'    => 'Electricity',
            'description' => 'The outlet does not work and needs professional repair.',
            'budget'      => 0,
            'deadline'    => now()->subDay()->format('Y-m-d H:i:s'),
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['budget', 'location', 'deadline']);
    }

    public function test_client_cannot_create_job_request_with_invalid_data(): void
    {
        $client = $this->createClient();
        Sanctum::actingAs($client);

        $response = $this->postJson('/api/v1/client/job-requests', [
            'title'       => '',
            'category'    => '',
            'description' => 'Too short',
            'city'        => '',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'category', 'description', 'city']);
    }

    public function test_provider_cannot_create_job_request_as_client(): void
    {
        $provider = $this->createProvider();
        Sanctum::actingAs($provider);

        $response = $this->postJson('/api/v1/client/job-requests', [
            'title'       => 'Some test job',
            'category'    => 'Plumbing',
            'description' => 'Valid length description of the job needing doing.',
            'city'        => 'Tunis',
        ]);

        $response->assertStatus(403);
    }

    // =========================================================================
    // 2. Provider Proposal Submission
    // =========================================================================

    public function test_provider_can_submit_proposal_for_open_job_request(): void
    {
        $client = $this->createClient();
        $job = JobRequest::factory()->create([
            'client_id' => $client->id,
            'category'  => 'Plumbing',
            'city'      => 'Tunis',
            'status'    => 'open',
        ]);

        $provider = $this->createProvider();
        Sanctum::actingAs($provider);

        $payload = [
            'price'                    => 95.00,
            'delivery_time'            => '1 day',
            'delivery_days'            => 1,
            'estimated_duration_hours' => 2.5,
            'message'                  => 'I can come tomorrow morning at 9am with all required copper pipe replacement parts.',
        ];

        $response = $this->postJson("/api/v1/provider/job-requests/{$job->id}/proposals", $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.price', 95)
            ->assertJsonPath('data.delivery_time', '1 day')
            ->assertJsonPath('data.message', $payload['message'])
            ->assertJsonPath('data.delivery_days', 1)
            ->assertJsonPath('data.status', 'pending');

        $this->assertDatabaseHas('proposals', [
            'job_request_id' => $job->id,
            'provider_id'    => $provider->id,
            'price'          => 95.00,
            'status'         => 'pending',
        ]);

        // Job status moves to in_review
        $this->assertEquals('in_review', $job->fresh()->status);
    }

    public function test_provider_submitting_again_updates_existing_pending_proposal(): void
    {
        $client = $this->createClient();
        $job = JobRequest::factory()->create(['client_id' => $client->id]);
        $provider = $this->createProvider();

        $proposal = Proposal::factory()->create([
            'job_request_id' => $job->id,
            'provider_id'    => $provider->id,
            'price'          => 150.00,
            'status'         => 'pending',
        ]);

        Sanctum::actingAs($provider);

        $response = $this->postJson("/api/v1/provider/job-requests/{$job->id}/proposals", [
            'price'         => 120.00,
            'delivery_time' => 'Same day',
            'delivery_days' => 1,
            'message'       => 'Updated price to offer a discount.',
        ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.price', 120);

        $this->assertEquals(1, Proposal::where('job_request_id', $job->id)->where('provider_id', $provider->id)->count());
        $this->assertEquals(120.00, $proposal->fresh()->price);
    }

    public function test_provider_cannot_submit_proposal_on_awarded_request(): void
    {
        $client = $this->createClient();
        $job = JobRequest::factory()->create([
            'client_id' => $client->id,
            'status'    => 'awarded',
        ]);
        $provider = $this->createProvider();
        Sanctum::actingAs($provider);

        $response = $this->postJson("/api/v1/provider/job-requests/{$job->id}/proposals", [
            'price'         => 100.00,
            'delivery_time' => '2 days',
            'message'       => 'I can do this.',
        ]);

        $response->assertStatus(422);
    }

    public function test_provider_must_supply_price_delivery_time_and_message_for_proposal(): void
    {
        $client = $this->createClient();
        $job = JobRequest::factory()->create(['client_id' => $client->id]);
        $provider = $this->createProvider();
        Sanctum::actingAs($provider);

        $response = $this->postJson("/api/v1/provider/job-requests/{$job->id}/proposals", [
            'price'         => 0,
            'delivery_time' => '',
            'message'       => 'Short',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price', 'delivery_time', 'message']);

        $this->assertDatabaseMissing('proposals', [
            'job_request_id' => $job->id,
            'provider_id' => $provider->id,
        ]);
    }

    // =========================================================================
    // 3. Client Proposal Comparison & Badges
    // =========================================================================

    public function test_client_can_compare_proposals_side_by_side_with_badges(): void
    {
        $client = $this->createClient();
        $job = JobRequest::factory()->create(['client_id' => $client->id]);

        // Provider A: Lowest price (60 TND), 3 days delivery, no reviews
        $providerA = $this->createProvider(['name' => 'Provider Cheaper']);
        $propA = Proposal::factory()->create([
            'job_request_id' => $job->id,
            'provider_id'    => $providerA->id,
            'price'          => 60.00,
            'delivery_time'  => '3 days',
            'delivery_days'  => 3,
            'status'         => 'pending',
        ]);

        // Provider B: Fastest delivery (1 day), 110 TND, 4.0 rating
        $providerB = $this->createProvider(['name' => 'Provider Fast']);
        Review::factory()->create([
            'provider_id' => $providerB->id,
            'client_id'   => $client->id,
            'rating'      => 4,
        ]);
        $propB = Proposal::factory()->create([
            'job_request_id' => $job->id,
            'provider_id'    => $providerB->id,
            'price'          => 110.00,
            'delivery_time'  => '1 day',
            'delivery_days'  => 1,
            'status'         => 'pending',
        ]);

        // Provider C: Top rated (5.0 rating), 90 TND, 2 days delivery
        $providerC = $this->createProvider(['name' => 'Provider Top']);
        Review::factory()->create([
            'provider_id' => $providerC->id,
            'client_id'   => $client->id,
            'rating'      => 5,
        ]);
        $propC = Proposal::factory()->create([
            'job_request_id' => $job->id,
            'provider_id'    => $providerC->id,
            'price'          => 90.00,
            'delivery_time'  => '2 days',
            'delivery_days'  => 2,
            'status'         => 'pending',
        ]);

        Sanctum::actingAs($client);

        $response = $this->getJson("/api/v1/client/job-requests/{$job->id}/compare");

        $response->assertStatus(200)
            ->assertJsonPath('summary.proposals_count', 3)
            ->assertJsonPath('summary.lowest_price', 60.0)
            ->assertJsonPath('summary.fastest_days', 1)
            ->assertJsonPath('summary.top_rating', 5.0);

        $proposals = $response->json('proposals');

        // Verify lowest price badge
        $proposalA = collect($proposals)->firstWhere('id', $propA->id);
        $this->assertTrue($proposalA['is_lowest_price']);
        $this->assertFalse($proposalA['is_fastest_delivery']);

        // Verify fastest delivery badge
        $proposalB = collect($proposals)->firstWhere('id', $propB->id);
        $this->assertTrue($proposalB['is_fastest_delivery']);
        $this->assertFalse($proposalB['is_lowest_price']);

        // Verify top rated badge
        $proposalC = collect($proposals)->firstWhere('id', $propC->id);
        $this->assertTrue($proposalC['is_top_rated']);
    }

    public function test_client_can_sort_comparison_by_delivery_time(): void
    {
        $client = $this->createClient();
        $job = JobRequest::factory()->create(['client_id' => $client->id]);

        $p1 = $this->createProvider();
        Proposal::factory()->create([
            'job_request_id' => $job->id,
            'provider_id'    => $p1->id,
            'price'          => 50.00,
            'delivery_days'  => 5,
        ]);

        $p2 = $this->createProvider();
        Proposal::factory()->create([
            'job_request_id' => $job->id,
            'provider_id'    => $p2->id,
            'price'          => 120.00,
            'delivery_days'  => 1,
        ]);

        Sanctum::actingAs($client);

        $response = $this->getJson("/api/v1/client/job-requests/{$job->id}/compare?sort=delivery_asc");

        $response->assertStatus(200);
        $proposals = $response->json('proposals');
        $this->assertEquals(1, $proposals[0]['delivery_days']);
        $this->assertEquals(5, $proposals[1]['delivery_days']);
    }

    // =========================================================================
    // 4. Accepting & Declining Proposals
    // =========================================================================

    public function test_client_accepting_proposal_awards_request_rejects_competitors_and_creates_booking(): void
    {
        $client = $this->createClient();
        $job = JobRequest::factory()->create([
            'client_id' => $client->id,
            'title'     => 'Fix water heater leak',
            'category'  => 'Plumbing',
            'city'      => 'Tunis',
            'status'    => 'open',
        ]);

        $winningProvider = $this->createProvider();
        $winningProp = Proposal::factory()->create([
            'job_request_id' => $job->id,
            'provider_id'    => $winningProvider->id,
            'price'          => 80.00,
            'delivery_time'  => 'Tomorrow',
            'delivery_days'  => 1,
            'status'         => 'pending',
        ]);

        $competingProvider = $this->createProvider();
        $competingProp = Proposal::factory()->create([
            'job_request_id' => $job->id,
            'provider_id'    => $competingProvider->id,
            'price'          => 130.00,
            'delivery_time'  => '4 days',
            'delivery_days'  => 4,
            'status'         => 'pending',
        ]);

        Sanctum::actingAs($client);

        $response = $this->postJson("/api/v1/client/proposals/{$winningProp->id}/accept");

        $response->assertStatus(200)
            ->assertJsonPath('data.proposal.status', 'accepted')
            ->assertJsonStructure(['data' => ['proposal', 'booking' => ['id', 'booking_number', 'status', 'total_price']]]);

        // Winning proposal is accepted
        $this->assertEquals('accepted', $winningProp->fresh()->status);
        $this->assertNotNull($winningProp->fresh()->accepted_at);

        // Competing proposal is rejected
        $this->assertEquals('rejected', $competingProp->fresh()->status);
        $this->assertStringContainsString('accepted another proposal', $competingProp->fresh()->rejection_reason);

        // Job request is awarded
        $freshJob = $job->fresh();
        $this->assertEquals('awarded', $freshJob->status);
        $this->assertEquals($winningProp->id, $freshJob->accepted_proposal_id);
        $this->assertNotNull($freshJob->booking_id);

        // Booking record was automatically generated
        $this->assertDatabaseHas('bookings', [
            'id'            => $freshJob->booking_id,
            'client_id'     => $client->id,
            'provider_id'   => $winningProvider->id,
            'service_title' => 'Fix water heater leak',
            'category'      => 'Plumbing',
            'status'        => 'confirmed',
            'total_price'   => 80.00,
        ]);
    }

    public function test_client_cannot_accept_another_clients_proposal(): void
    {
        $clientA = $this->createClient();
        $clientB = $this->createClient();

        $job = JobRequest::factory()->create(['client_id' => $clientA->id]);
        $provider = $this->createProvider();
        $proposal = Proposal::factory()->create([
            'job_request_id' => $job->id,
            'provider_id'    => $provider->id,
            'status'         => 'pending',
        ]);

        Sanctum::actingAs($clientB);

        $response = $this->postJson("/api/v1/client/proposals/{$proposal->id}/accept");

        $response->assertStatus(403);
    }

    public function test_client_can_decline_proposal(): void
    {
        $client = $this->createClient();
        $job = JobRequest::factory()->create(['client_id' => $client->id]);
        $provider = $this->createProvider();
        $proposal = Proposal::factory()->create([
            'job_request_id' => $job->id,
            'provider_id'    => $provider->id,
            'status'         => 'pending',
        ]);

        Sanctum::actingAs($client);

        $response = $this->postJson("/api/v1/client/proposals/{$proposal->id}/reject", [
            'reason' => 'Price is outside my expected range.',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'rejected');

        $this->assertEquals('rejected', $proposal->fresh()->status);
        $this->assertEquals('Price is outside my expected range.', $proposal->fresh()->rejection_reason);
    }

    // =========================================================================
    // 5. Provider Feed & Proposal History
    // =========================================================================

    public function test_provider_can_view_open_job_feed_matching_their_trade(): void
    {
        $client = $this->createClient();
        $job = JobRequest::factory()->create([
            'client_id' => $client->id,
            'category'  => 'Plumbing',
            'city'      => 'Tunis',
            'location'  => 'Tunis, La Marsa',
            'deadline'  => now()->addWeek(),
            'status'    => 'open',
        ]);
        JobRequest::factory()->create([
            'client_id' => $client->id,
            'category'  => 'Painting',
            'city'      => 'Tunis',
            'status'    => 'open',
        ]);

        $provider = $this->createProvider([], ['category' => 'Plumbing']);
        Proposal::factory()->create([
            'job_request_id' => $job->id,
            'provider_id' => $provider->id,
            'price' => 130,
            'delivery_time' => '3 days',
            'delivery_days' => 3,
            'message' => 'I can complete the installation within three business days.',
            'status' => 'pending',
        ]);
        Sanctum::actingAs($provider);

        $response = $this->getJson('/api/v1/provider/job-requests?category=Plumbing');

        $response->assertStatus(200)
            ->assertJsonPath('meta.total', 1)
            ->assertJsonPath('data.0.category', 'Plumbing')
            ->assertJsonPath('data.0.location', 'Tunis, La Marsa')
            ->assertJsonPath('data.0.has_applied', true)
            ->assertJsonPath('data.0.my_proposal_delivery_time', '3 days')
            ->assertJsonPath('data.0.my_proposal_message', 'I can complete the installation within three business days.');
    }

    public function test_client_cannot_view_provider_job_request_feed(): void
    {
        Sanctum::actingAs($this->createClient());

        $this->getJson('/api/v1/provider/job-requests')
            ->assertStatus(403);
    }
}

<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\IncidentReport;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncidentReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_client_can_submit_an_issue_report(): void
    {
        $client = User::factory()->create(['role' => UserRole::CLIENT]);

        $response = $this->actingAs($client)->postJson('/api/v1/reports', [
            'type' => 'issue',
            'subject' => 'Payment page is unavailable',
            'details' => 'The payment page displays an error when I try to submit a payment.',
        ]);

        $response->assertCreated()
            ->assertJsonPath('report.type', 'issue')
            ->assertJsonPath('report.status', 'open');

        $this->assertDatabaseHas('incident_reports', [
            'reporter_id' => $client->id,
            'reported_client_id' => null,
            'booking_id' => null,
            'type' => 'issue',
        ]);
    }

    public function test_provider_can_report_a_client_from_their_booking(): void
    {
        $provider = User::factory()->create(['role' => UserRole::PROVIDER]);
        $client = User::factory()->create(['role' => UserRole::CLIENT]);
        $booking = Booking::factory()->create([
            'provider_id' => $provider->id,
            'client_id' => $client->id,
        ]);

        $response = $this->actingAs($provider)->postJson('/api/v1/reports', [
            'type' => 'client',
            'booking_id' => $booking->id,
            'subject' => 'Client did not attend',
            'details' => 'The client did not attend the scheduled appointment and did not respond.',
        ]);

        $response->assertCreated()
            ->assertJsonPath('report.reported_client_id', $client->id)
            ->assertJsonPath('report.booking_id', $booking->id);

        $this->assertDatabaseHas('incident_reports', [
            'reporter_id' => $provider->id,
            'reported_client_id' => $client->id,
            'booking_id' => $booking->id,
            'type' => 'client',
        ]);
    }

    public function test_client_cannot_report_another_client(): void
    {
        $client = User::factory()->create(['role' => UserRole::CLIENT]);

        $this->actingAs($client)
            ->postJson('/api/v1/reports', [
                'type' => 'client',
                'booking_id' => 1,
                'subject' => 'Report',
                'details' => 'This client report is not allowed.',
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('incident_reports', 0);
    }

    public function test_provider_cannot_report_a_client_from_another_providers_booking(): void
    {
        $provider = User::factory()->create(['role' => UserRole::PROVIDER]);
        $booking = Booking::factory()->create();

        $this->actingAs($provider)
            ->postJson('/api/v1/reports', [
                'type' => 'client',
                'booking_id' => $booking->id,
                'subject' => 'Client report',
                'details' => 'This booking belongs to another provider.',
            ])
            ->assertNotFound();

        $this->assertDatabaseCount('incident_reports', 0);
    }

    public function test_issue_report_requires_subject_and_details(): void
    {
        $client = User::factory()->create(['role' => UserRole::CLIENT]);

        $this->actingAs($client)
            ->postJson('/api/v1/reports', ['type' => 'issue'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['subject', 'details']);
    }

    public function test_admin_can_review_and_update_reports_but_other_users_cannot(): void
    {
        $report = IncidentReport::factory()->create();
        $admin = User::factory()->create(['role' => UserRole::ADMIN]);
        $provider = User::factory()->create(['role' => UserRole::PROVIDER]);

        $this->actingAs($provider)
            ->getJson('/api/v1/admin/reports')
            ->assertForbidden();

        $this->actingAs($admin)
            ->getJson('/api/v1/admin/reports')
            ->assertOk()
            ->assertJsonPath('data.0.id', $report->id);

        $this->actingAs($admin)
            ->patchJson("/api/v1/admin/reports/{$report->id}", [
                'status' => 'in_review',
                'admin_notes' => 'Contacting the reporter for more information.',
            ])
            ->assertOk()
            ->assertJsonPath('report.status', 'in_review');

        $this->assertDatabaseHas('incident_reports', [
            'id' => $report->id,
            'status' => 'in_review',
            'admin_notes' => 'Contacting the reporter for more information.',
        ]);
    }

    public function test_report_submission_requires_authentication(): void
    {
        $this->postJson('/api/v1/reports', [
            'type' => 'issue',
            'subject' => 'Broken page',
            'details' => 'This page does not load correctly.',
        ])->assertUnauthorized();
    }
}

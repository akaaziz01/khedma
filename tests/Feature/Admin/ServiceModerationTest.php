<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceModerationTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function admin(): User
    {
        return User::factory()->create(['role' => UserRole::ADMIN]);
    }

    private function pendingService(array $overrides = []): Service
    {
        return Service::factory()->create(array_merge([
            'status'    => 'pending',
            'is_active' => false,
        ], $overrides));
    }

    // =========================================================================
    // List services
    // =========================================================================

    public function test_admin_can_list_all_services(): void
    {
        Service::factory()->count(5)->create();

        $this->actingAs($this->admin())
            ->getJson('/api/v1/admin/services')
            ->assertOk()
            ->assertJsonPath('pagination.total', 5);
    }

    public function test_admin_can_filter_services_by_status(): void
    {
        Service::factory()->count(3)->create(['status' => 'pending']);
        Service::factory()->count(2)->create(['status' => 'approved']);

        $this->actingAs($this->admin())
            ->getJson('/api/v1/admin/services?status=pending')
            ->assertOk()
            ->assertJsonPath('pagination.total', 3);
    }

    public function test_admin_can_filter_services_by_category(): void
    {
        $cat = ServiceCategory::factory()->create();
        Service::factory()->count(2)->create(['category_id' => $cat->id]);
        Service::factory()->create();

        $this->actingAs($this->admin())
            ->getJson("/api/v1/admin/services?category_id={$cat->id}")
            ->assertOk()
            ->assertJsonPath('pagination.total', 2);
    }

    public function test_non_admin_cannot_list_services(): void
    {
        $provider = User::factory()->create(['role' => UserRole::PROVIDER]);

        $this->actingAs($provider)
            ->getJson('/api/v1/admin/services')
            ->assertForbidden();
    }

    // =========================================================================
    // Approve
    // =========================================================================

    public function test_admin_can_approve_a_pending_service(): void
    {
        $service = $this->pendingService();

        $this->actingAs($this->admin())
            ->postJson("/api/v1/admin/services/{$service->id}/moderate", [
                'action' => 'approve',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'approved')
            ->assertJsonPath('data.is_active', true);

        $this->assertDatabaseHas('services', [
            'id'     => $service->id,
            'status' => 'approved',
        ]);
    }

    public function test_approve_records_moderated_by_and_moderated_at(): void
    {
        $admin   = $this->admin();
        $service = $this->pendingService();

        $this->actingAs($admin)
            ->postJson("/api/v1/admin/services/{$service->id}/moderate", ['action' => 'approve'])
            ->assertOk();

        $this->assertDatabaseHas('services', [
            'id'           => $service->id,
            'moderated_by' => $admin->id,
        ]);
        $this->assertNotNull($service->fresh()->moderated_at);
    }

    // =========================================================================
    // Reject
    // =========================================================================

    public function test_admin_can_reject_a_service_with_a_reason(): void
    {
        $service = $this->pendingService();

        $this->actingAs($this->admin())
            ->postJson("/api/v1/admin/services/{$service->id}/moderate", [
                'action'           => 'reject',
                'rejection_reason' => 'Service description is too vague.',
            ])
            ->assertOk()
            ->assertJsonPath('data.status', 'rejected')
            ->assertJsonPath('data.is_active', false);

        $this->assertDatabaseHas('services', [
            'id'               => $service->id,
            'status'           => 'rejected',
            'rejection_reason' => 'Service description is too vague.',
        ]);
    }

    public function test_reject_requires_a_rejection_reason(): void
    {
        $service = $this->pendingService();

        $this->actingAs($this->admin())
            ->postJson("/api/v1/admin/services/{$service->id}/moderate", [
                'action' => 'reject',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['rejection_reason']);
    }

    // =========================================================================
    // Toggle active
    // =========================================================================

    public function test_admin_can_deactivate_an_approved_service(): void
    {
        $service = Service::factory()->create(['status' => 'approved', 'is_active' => true]);

        $this->actingAs($this->admin())
            ->patchJson("/api/v1/admin/services/{$service->id}/active", ['is_active' => false])
            ->assertOk()
            ->assertJsonPath('data.is_active', false);
    }

    public function test_admin_cannot_activate_a_pending_service(): void
    {
        $service = $this->pendingService();

        $this->actingAs($this->admin())
            ->patchJson("/api/v1/admin/services/{$service->id}/active", ['is_active' => true])
            ->assertUnprocessable();
    }

    // =========================================================================
    // Delete
    // =========================================================================

    public function test_admin_can_delete_a_service_with_no_active_bookings(): void
    {
        $service = $this->pendingService();

        $this->actingAs($this->admin())
            ->deleteJson("/api/v1/admin/services/{$service->id}")
            ->assertOk();

        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }

    public function test_admin_cannot_delete_service_with_active_bookings(): void
    {
        $service = Service::factory()->create(['status' => 'approved']);
        Booking::factory()->create([
            'service_id' => $service->id,
            'status'     => 'accepted',
        ]);

        $this->actingAs($this->admin())
            ->deleteJson("/api/v1/admin/services/{$service->id}")
            ->assertUnprocessable();
    }

    public function test_admin_can_delete_service_that_only_has_cancelled_bookings(): void
    {
        $service = Service::factory()->create(['status' => 'approved']);
        Booking::factory()->create([
            'service_id' => $service->id,
            'status'     => 'cancelled',
        ]);

        $this->actingAs($this->admin())
            ->deleteJson("/api/v1/admin/services/{$service->id}")
            ->assertOk();

        $this->assertDatabaseMissing('services', ['id' => $service->id]);
    }

    // =========================================================================
    // Auth guards
    // =========================================================================

    public function test_guest_cannot_moderate_services(): void
    {
        $service = $this->pendingService();

        $this->postJson("/api/v1/admin/services/{$service->id}/moderate", ['action' => 'approve'])
            ->assertUnauthorized();
    }

    public function test_provider_cannot_moderate_services(): void
    {
        $provider = User::factory()->create(['role' => UserRole::PROVIDER]);
        $service  = $this->pendingService();

        $this->actingAs($provider)
            ->postJson("/api/v1/admin/services/{$service->id}/moderate", ['action' => 'approve'])
            ->assertForbidden();
    }
}


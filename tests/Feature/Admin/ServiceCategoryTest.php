<?php

namespace Tests\Feature\Admin;

use App\Enums\UserRole;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceCategoryTest extends TestCase
{
    use RefreshDatabase;

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function admin(): User
    {
        return User::factory()->create(['role' => UserRole::ADMIN]);
    }

    private function provider(): User
    {
        return User::factory()->create(['role' => UserRole::PROVIDER]);
    }

    // =========================================================================
    // Public listing
    // =========================================================================

    public function test_anyone_can_list_active_categories(): void
    {
        ServiceCategory::factory()->count(3)->create(['is_active' => true]);
        ServiceCategory::factory()->create(['is_active' => false]);

        $response = $this->getJson('/api/v1/categories');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    public function test_admin_can_list_all_categories_including_inactive(): void
    {
        ServiceCategory::factory()->count(2)->create(['is_active' => true]);
        ServiceCategory::factory()->create(['is_active' => false]);

        $response = $this->actingAs($this->admin())
            ->getJson('/api/v1/categories?include_inactive=true');

        $response->assertOk()
            ->assertJsonCount(3, 'data');
    }

    // =========================================================================
    // Create
    // =========================================================================

    public function test_admin_can_create_a_category(): void
    {
        $response = $this->actingAs($this->admin())
            ->postJson('/api/v1/admin/categories', [
                'name'        => 'Plumbing',
                'description' => 'Water and pipe services',
                'icon'        => '🔧',
                'sort_order'  => 1,
            ]);

        $response->assertCreated()
            ->assertJsonPath('data.name', 'Plumbing')
            ->assertJsonPath('data.slug', 'plumbing');

        $this->assertDatabaseHas('service_categories', ['name' => 'Plumbing', 'slug' => 'plumbing']);
    }

    public function test_category_name_must_be_unique(): void
    {
        ServiceCategory::factory()->create(['name' => 'Plumbing']);

        $this->actingAs($this->admin())
            ->postJson('/api/v1/admin/categories', ['name' => 'Plumbing'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['name']);
    }

    public function test_non_admin_cannot_create_a_category(): void
    {
        $this->actingAs($this->provider())
            ->postJson('/api/v1/admin/categories', ['name' => 'Plumbing'])
            ->assertForbidden();
    }

    public function test_guest_cannot_create_a_category(): void
    {
        $this->postJson('/api/v1/admin/categories', ['name' => 'Plumbing'])
            ->assertUnauthorized();
    }

    // =========================================================================
    // Update
    // =========================================================================

    public function test_admin_can_update_a_category(): void
    {
        $category = ServiceCategory::factory()->create(['name' => 'Old Name']);

        $this->actingAs($this->admin())
            ->patchJson("/api/v1/admin/categories/{$category->id}", ['name' => 'New Name'])
            ->assertOk()
            ->assertJsonPath('data.name', 'New Name')
            ->assertJsonPath('data.slug', 'new-name');
    }

    public function test_admin_can_deactivate_a_category(): void
    {
        $category = ServiceCategory::factory()->create(['is_active' => true]);

        $this->actingAs($this->admin())
            ->patchJson("/api/v1/admin/categories/{$category->id}", ['is_active' => false])
            ->assertOk()
            ->assertJsonPath('data.is_active', false);
    }

    // =========================================================================
    // Delete
    // =========================================================================

    public function test_admin_can_soft_delete_an_empty_category(): void
    {
        $category = ServiceCategory::factory()->create();

        $this->actingAs($this->admin())
            ->deleteJson("/api/v1/admin/categories/{$category->id}")
            ->assertOk()
            ->assertJsonFragment(['message' => "Category \"{$category->name}\" has been removed."]);

        $this->assertSoftDeleted('service_categories', ['id' => $category->id]);
    }

    public function test_admin_cannot_delete_category_with_approved_services(): void
    {
        $category = ServiceCategory::factory()->create();
        Service::factory()->create([
            'category_id' => $category->id,
            'status'      => 'approved',
        ]);

        $this->actingAs($this->admin())
            ->deleteJson("/api/v1/admin/categories/{$category->id}")
            ->assertUnprocessable()
            ->assertJsonStructure(['message']);
    }

    public function test_admin_can_delete_category_that_only_has_pending_services(): void
    {
        $category = ServiceCategory::factory()->create();
        Service::factory()->create([
            'category_id' => $category->id,
            'status'      => 'pending',
        ]);

        $this->actingAs($this->admin())
            ->deleteJson("/api/v1/admin/categories/{$category->id}")
            ->assertOk();

        $this->assertSoftDeleted('service_categories', ['id' => $category->id]);
    }
}


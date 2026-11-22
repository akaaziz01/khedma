<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\ProviderProfile;
use App\Models\Review;
use App\Models\Service;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProviderCompareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_or_client_can_compare_providers_via_api(): void
    {
        $provider1 = User::factory()->create([
            'role'   => UserRole::PROVIDER,
            'name'   => 'Plumber One',
            'status' => 'active',
        ]);
        ProviderProfile::create([
            'user_id'          => $provider1->id,
            'business_name'    => 'Plumbing Pros',
            'category'         => 'Plumbing',
            'city'             => 'Tunis',
            'hourly_rate'      => 40.00,
            'experience_years' => 5,
            'is_available'     => true,
            'skills'           => ['Pipes', 'Drains'],
        ]);

        $provider2 = User::factory()->create([
            'role'   => UserRole::PROVIDER,
            'name'   => 'Plumber Two',
            'status' => 'active',
        ]);
        ProviderProfile::create([
            'user_id'          => $provider2->id,
            'business_name'    => 'Quick Plumbing',
            'category'         => 'Plumbing',
            'city'             => 'Ariana',
            'hourly_rate'      => 30.00,
            'experience_years' => 10,
            'is_available'     => true,
            'skills'           => ['Boilers', 'Pipes'],
        ]);

        $response = $this->getJson("/api/v1/providers/compare?ids={$provider1->id},{$provider2->id}");

        $response->assertOk()
            ->assertJsonStructure([
                'success',
                'providers' => [
                    '*' => [
                        'id',
                        'name',
                        'trade',
                        'city',
                        'rates' => ['hourly_rate'],
                        'experience' => ['years'],
                        'reviews' => ['average_rating', 'reviews_count'],
                        'highlights',
                    ]
                ],
                'available_providers',
                'selected_ids',
            ]);

        $providers = $response->json('providers');
        $this->assertCount(2, $providers);

        // Provider 2 has lower rate (30 vs 40) and more experience (10 vs 5)
        $p2Data = collect($providers)->firstWhere('id', $provider2->id);
        $highlights = collect($p2Data['highlights'])->pluck('label')->all();
        $this->assertContains('Best Hourly Rate', $highlights);
        $this->assertContains('Most Experienced', $highlights);
    }

    public function test_compare_web_page_renders_side_by_side_matrix(): void
    {
        $provider1 = User::factory()->create([
            'role'   => UserRole::PROVIDER,
            'name'   => 'Youssef Compare',
            'status' => 'active',
        ]);
        ProviderProfile::create([
            'user_id'          => $provider1->id,
            'business_name'    => 'Compare Pro Business',
            'category'         => 'Plumbing',
            'city'             => 'Tunis',
            'hourly_rate'      => 45.00,
            'experience_years' => 8,
            'is_available'     => true,
        ]);

        $provider2 = User::factory()->create([
            'role'   => UserRole::PROVIDER,
            'name'   => 'Karim Compare',
            'status' => 'active',
        ]);
        ProviderProfile::create([
            'user_id'          => $provider2->id,
            'business_name'    => 'Karim Heating',
            'category'         => 'Plumbing',
            'city'             => 'Tunis',
            'hourly_rate'      => 40.00,
            'experience_years' => 5,
            'is_available'     => true,
        ]);

        $response = $this->get("/compare?ids={$provider1->id},{$provider2->id}");

        $response->assertStatus(200);
        $response->assertSee('Compare Providers with Confidence');
        $response->assertSee('Compare Pro Business');
        $response->assertSee('Karim Heating');
        $response->assertSee('45');
        $response->assertSee('8 Years');
        $response->assertSee('compareModalRegisterLink');
        $response->assertSee("encodeURIComponent('/providers/' + providerId)", false);
    }
}

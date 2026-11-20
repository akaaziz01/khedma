<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProviderPortfolioTest extends TestCase
{
    use RefreshDatabase;

    private function createProvider(): User
    {
        $provider = User::create([
            'name'     => 'Khedma Pro Craftsman',
            'email'    => 'craft@khedma.tn',
            'password' => 'Password123!',
            'role'     => UserRole::PROVIDER,
            'phone'    => '+216 20 123 456',
        ]);

        $provider->providerProfile()->create([
            'business_name'    => 'Craftsman Pro Services',
            'category'         => 'Plumbing',
            'hourly_rate'      => 45.00,
            'experience_years' => 6,
            'city'             => 'Tunis',
            'portfolio_photos' => [],
        ]);

        return $provider;
    }

    private function createClient(): User
    {
        return User::create([
            'name'     => 'Client User',
            'email'    => 'client@khedma.tn',
            'password' => 'Password123!',
            'role'     => UserRole::CLIENT,
            'phone'    => '+216 21 000 111',
        ]);
    }

    public function test_provider_can_upload_a_photo_of_previous_work_file(): void
    {
        Storage::fake('public');

        $provider = $this->createProvider();
        $token = $provider->createToken('auth_token')->plainTextToken;

        $file = UploadedFile::fake()->image('copper_pipes_renovation.jpg', 800, 600);

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/v1/provider/portfolio', [
                'photo'   => $file,
                'title'   => 'Bathroom Copper Plumbing Renovation',
                'caption' => 'Installed complete copper manifold and high-pressure valves with zero leaks.',
            ]);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'message',
            'photo' => ['id', 'url', 'title', 'caption', 'created_at'],
            'portfolio_photos',
        ]);

        $data = $response->json();
        $this->assertEquals('Bathroom Copper Plumbing Renovation', $data['photo']['title']);
        $this->assertNotEmpty($data['photo']['url']);

        // Verify photo exists on fake disk
        $relativeUrl = str_replace('/storage/', '', $data['photo']['url']);
        Storage::disk('public')->assertExists($relativeUrl);

        // Verify persisted in database
        $freshProfile = $provider->fresh()->providerProfile;
        $this->assertCount(1, $freshProfile->portfolio_photos);
        $this->assertEquals('Bathroom Copper Plumbing Renovation', $freshProfile->portfolio_photos[0]['title']);
    }

    public function test_provider_can_add_work_photo_via_url(): void
    {
        $provider = $this->createProvider();
        $token = $provider->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/provider/portfolio', [
                'photo_url' => 'https://images.unsplash.com/photo-1581244277943-fe4a9c777189',
                'title'     => 'AC Inverter Split Unit Installation',
                'caption'   => 'Clean hidden conduit and bracket mounting in Tunis.',
            ]);

        $response->assertStatus(201);
        $freshProfile = $provider->fresh()->providerProfile;
        $this->assertCount(1, $freshProfile->portfolio_photos);
        $this->assertEquals('https://images.unsplash.com/photo-1581244277943-fe4a9c777189', $freshProfile->portfolio_photos[0]['url']);
    }

    public function test_provider_cannot_upload_without_photo_or_title(): void
    {
        $provider = $this->createProvider();
        $token = $provider->createToken('auth_token')->plainTextToken;

        // Missing photo/url
        $res1 = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/provider/portfolio', [
                'title' => 'Missing Photo',
            ]);
        $res1->assertStatus(422);

        // Missing title
        $file = UploadedFile::fake()->image('test.png');
        $res2 = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/v1/provider/portfolio', [
                'photo' => $file,
            ]);
        $res2->assertStatus(422);
    }

    public function test_provider_can_delete_a_portfolio_photo(): void
    {
        Storage::fake('public');

        $provider = $this->createProvider();
        $token = $provider->createToken('auth_token')->plainTextToken;

        // Upload first photo
        $file = UploadedFile::fake()->image('kitchen_pipes.jpg');
        $uploadRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->post('/api/v1/provider/portfolio', [
                'photo' => $file,
                'title' => 'Kitchen Sink Piping',
            ]);
        $uploadRes->assertStatus(201);
        $photoId = $uploadRes->json('photo.id');

        // Delete photo
        $deleteRes = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->deleteJson("/api/v1/provider/portfolio/{$photoId}");

        $deleteRes->assertStatus(200);
        $deleteRes->assertJsonFragment(['message' => 'Portfolio photo removed successfully.']);

        $freshProfile = $provider->fresh()->providerProfile;
        $this->assertCount(0, $freshProfile->portfolio_photos);
    }

    public function test_client_cannot_upload_to_provider_portfolio(): void
    {
        $client = $this->createClient();
        $token = $client->createToken('auth_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('/api/v1/provider/portfolio', [
                'photo_url' => 'https://images.unsplash.com/photo-1581244277943-fe4a9c777189',
                'title'     => 'Unauthorized Attempt',
            ]);

        $response->assertStatus(403);
    }

    public function test_guest_cannot_upload_portfolio(): void
    {
        $response = $this->postJson('/api/v1/provider/portfolio', [
            'photo_url' => 'https://images.unsplash.com/photo-1581244277943-fe4a9c777189',
            'title'     => 'Guest Attempt',
        ]);

        $response->assertStatus(401);
    }
}

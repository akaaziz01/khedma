<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Conversation;
use App\Models\JobRequest;
use App\Models\ProviderProfile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_access_conversations(): void
    {
        $this->getJson('/api/v1/conversations')->assertStatus(401);
        $this->postJson('/api/v1/conversations', ['provider_id' => 1])->assertStatus(401);
    }

    public function test_client_can_start_conversation_with_provider_and_send_message(): void
    {
        $client = User::create([
            'name' => 'Mariem Client',
            'email' => 'mariem@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::CLIENT,
            'phone' => '+21620000001',
        ]);

        $provider = User::create([
            'name' => 'Youssef Pro',
            'email' => 'youssef@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::PROVIDER,
            'phone' => '+21620000002',
        ]);

        Sanctum::actingAs($client);

        // 1. Start conversation with initial message
        $startResponse = $this->postJson('/api/v1/conversations', [
            'provider_id' => $provider->id,
            'message' => 'Hello Youssef, are you available tomorrow morning for a plumbing check?',
        ]);

        $startResponse->assertStatus(201)
            ->assertJsonPath('conversation.client_id', $client->id)
            ->assertJsonPath('conversation.provider_id', $provider->id);

        $convId = $startResponse->json('conversation.id');

        $this->assertDatabaseHas('conversations', [
            'id' => $convId,
            'client_id' => $client->id,
            'provider_id' => $provider->id,
        ]);

        $this->assertDatabaseHas('messages', [
            'conversation_id' => $convId,
            'sender_id' => $client->id,
            'body' => 'Hello Youssef, are you available tomorrow morning for a plumbing check?',
        ]);

        // 2. Provider replies
        Sanctum::actingAs($provider);

        $replyResponse = $this->postJson("/api/v1/conversations/{$convId}/messages", [
            'body' => 'Yes Mariem! I can come around 10:00 AM. Please share your location.',
        ]);

        $replyResponse->assertStatus(201)
            ->assertJsonPath('message.body', 'Yes Mariem! I can come around 10:00 AM. Please share your location.');

        $clientNotification = $client->notifications()->first();
        $this->assertSame('new_message', $clientNotification->data['type']);
        $this->assertSame('/messages', $clientNotification->data['url']);

        // 3. Client checks messages and marks as read
        Sanctum::actingAs($client);

        $messagesResponse = $this->getJson("/api/v1/conversations/{$convId}/messages");
        $messagesResponse->assertStatus(200)
            ->assertJsonCount(2, 'messages');

        // Verify incoming message is marked read
        $this->assertDatabaseMissing('messages', [
            'conversation_id' => $convId,
            'sender_id' => $provider->id,
            'read_at' => null,
        ]);

        // 4. Verify conversation list shows unread count and latest message
        $listResponse = $this->getJson('/api/v1/conversations');
        $listResponse->assertStatus(200)
            ->assertJsonPath('conversations.0.participant.name', 'Youssef Pro')
            ->assertJsonPath('conversations.0.unread_count', 0);
    }

    public function test_provider_can_start_a_conversation_with_a_client_from_a_job_request(): void
    {
        $client = User::factory()->create(['role' => UserRole::CLIENT]);
        $provider = User::factory()->create(['role' => UserRole::PROVIDER]);
        ProviderProfile::create([
            'user_id' => $provider->id,
            'business_name' => 'Expert Craft',
            'category' => 'Plumbing',
            'city' => 'Tunis',
            'bio' => 'Licensed professional with certified expertise.',
            'skills' => ['Plumbing'],
            'hourly_rate' => 45.00,
            'is_available' => true,
        ]);
        $jobRequest = JobRequest::factory()->create([
            'client_id' => $client->id,
            'category' => 'Plumbing',
        ]);

        Sanctum::actingAs($provider);

        $feedResponse = $this->getJson('/api/v1/provider/job-requests')
            ->assertOk()
            ->assertJsonPath('data.0.client_id', $client->id)
            ->assertJsonPath('data.0.client_name', $client->name);

        config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);
        $this->get('/dashboard')
            ->assertOk()
            ->assertSee('Message Client')
            ->assertSee('startChatWithUser');

        $response = $this->postJson('/api/v1/conversations', [
            'recipient_id' => $feedResponse->json('data.0.client_id'),
            'job_request_id' => $jobRequest->id,
        ]);

        $response->assertCreated()
            ->assertJsonPath('conversation.client_id', $client->id)
            ->assertJsonPath('conversation.provider_id', $provider->id)
            ->assertJsonPath('conversation.job_request_id', $jobRequest->id);
    }

    public function test_user_cannot_start_a_conversation_with_another_user_of_the_same_role(): void
    {
        $client = User::factory()->create(['role' => UserRole::CLIENT]);
        $otherClient = User::factory()->create(['role' => UserRole::CLIENT]);

        Sanctum::actingAs($client);

        $this->postJson('/api/v1/conversations', [
            'recipient_id' => $otherClient->id,
        ])->assertUnprocessable();

        $this->assertDatabaseCount('conversations', 0);
    }

    public function test_conversation_cannot_be_linked_to_another_clients_job_request(): void
    {
        $client = User::factory()->create(['role' => UserRole::CLIENT]);
        $otherClient = User::factory()->create(['role' => UserRole::CLIENT]);
        $provider = User::factory()->create(['role' => UserRole::PROVIDER]);
        $jobRequest = JobRequest::factory()->create(['client_id' => $otherClient->id]);

        Sanctum::actingAs($provider);

        $this->postJson('/api/v1/conversations', [
            'recipient_id' => $client->id,
            'job_request_id' => $jobRequest->id,
        ])->assertUnprocessable();

        $this->assertDatabaseCount('conversations', 0);
    }

    public function test_non_participant_cannot_access_or_send_messages(): void
    {
        $client = User::create([
            'name' => 'Client A',
            'email' => 'ca@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::CLIENT,
        ]);

        $provider = User::create([
            'name' => 'Provider B',
            'email' => 'pb@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::PROVIDER,
        ]);

        $stranger = User::create([
            'name' => 'Stranger C',
            'email' => 'sc@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::CLIENT,
        ]);

        $conversation = Conversation::create([
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'last_message_at' => now(),
        ]);

        Sanctum::actingAs($stranger);

        $this->getJson("/api/v1/conversations/{$conversation->id}/messages")
            ->assertStatus(403);

        $this->postJson("/api/v1/conversations/{$conversation->id}/messages", [
            'body' => 'Intruder message',
        ])->assertStatus(403);
    }
}

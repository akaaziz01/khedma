<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Conversation;
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

    public function test_client_can_initialize_conversation_without_message(): void
    {
        $client = User::create([
            'name' => 'Sara Client',
            'email' => 'sara@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::CLIENT,
        ]);

        $provider = User::create([
            'name' => 'Amine Pro',
            'email' => 'amine@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::PROVIDER,
        ]);

        Sanctum::actingAs($client);

        $res = $this->postJson('/api/v1/conversations', [
            'provider_id' => $provider->id,
        ]);

        $res->assertStatus(201)
            ->assertJsonPath('conversation.client_id', $client->id)
            ->assertJsonPath('conversation.provider_id', $provider->id);

        $this->assertDatabaseHas('conversations', [
            'client_id' => $client->id,
            'provider_id' => $provider->id,
        ]);
    }

    public function test_chat_buttons_and_icons_are_rendered_across_cards_and_detail_pages(): void
    {
        $provider = User::create([
            'name' => 'Walid Pro',
            'email' => 'walid@example.com',
            'password' => 'Secret123!',
            'role' => UserRole::PROVIDER,
            'status' => 'active',
        ]);

        $service = \App\Models\Service::create([
            'provider_id' => $provider->id,
            'title' => 'Electrical Inspection',
            'description' => 'Fast electrical diagnostics and fixes.',
            'category' => 'Electricity',
            'price' => 80.00,
            'duration_hours' => 1,
            'status' => 'approved',
            'is_active' => true,
        ]);

        // 1. Service Card in Catalog (services.index)
        $servicesResponse = $this->get(route('services.index'));
        $servicesResponse->assertStatus(200);
        $servicesResponse->assertSee(route('chat', ['provider_id' => $provider->id], false));

        // 2. Service Detail Page (services.show)
        $serviceShowResponse = $this->get(route('services.show', $service->id));
        $serviceShowResponse->assertStatus(200);
        $serviceShowResponse->assertSee(route('chat', ['provider_id' => $provider->id], false));

        // 3. Provider Card in Directory (providers.index)
        $providersResponse = $this->get(route('providers.index'));
        $providersResponse->assertStatus(200);
        $providersResponse->assertSee(route('chat', ['provider_id' => $provider->id], false));

        // 4. Provider Profile Page (providers.show)
        $providerShowResponse = $this->get(route('providers.show', $provider->id));
        $providerShowResponse->assertStatus(200);
        $providerShowResponse->assertSee(route('chat', ['provider_id' => $provider->id], false));
    }
}

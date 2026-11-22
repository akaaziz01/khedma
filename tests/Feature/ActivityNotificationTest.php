<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Models\Booking;
use App\Models\Conversation;
use App\Models\JobRequest;
use App\Models\ProviderProfile;
use App\Models\User;
use App\Notifications\ActivityNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ActivityNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_proposal_notifies_the_client_who_owns_the_job_request(): void
    {
        $client = User::factory()->create(['role' => UserRole::CLIENT]);
        $provider = User::factory()->create(['role' => UserRole::PROVIDER]);
        $job = JobRequest::factory()->create([
            'client_id' => $client->id,
            'title' => 'Repair kitchen sink',
        ]);

        $this->actingAs($provider)
            ->postJson("/api/v1/provider/job-requests/{$job->id}/proposals", [
                'price' => 125,
                'delivery_time' => '2 days',
                'delivery_days' => 2,
                'message' => 'I can repair your kitchen sink and replace the damaged pipe.',
            ])
            ->assertCreated();

        $this->assertSame('new_proposal', $client->notifications()->first()->data['type']);
        $this->assertSame(0, $provider->notifications()->count());
    }

    public function test_new_message_notifies_the_other_conversation_participant(): void
    {
        $client = User::factory()->create(['role' => UserRole::CLIENT]);
        $provider = User::factory()->create(['role' => UserRole::PROVIDER]);
        $conversation = Conversation::create([
            'client_id' => $client->id,
            'provider_id' => $provider->id,
        ]);

        $this->actingAs($client)
            ->postJson("/api/v1/conversations/{$conversation->id}/messages", [
                'body' => 'Could you share your availability for tomorrow?',
            ])
            ->assertCreated();

        $notification = $provider->notifications()->first();
        $this->assertSame('new_message', $notification->data['type']);
        $this->assertSame('/messages', $notification->data['url']);
        $this->assertSame(0, $client->notifications()->count());
    }

    public function test_new_job_request_notifies_providers_in_the_matching_trade(): void
    {
        $client = User::factory()->create(['role' => UserRole::CLIENT]);
        $matchingProvider = User::factory()->create(['role' => UserRole::PROVIDER]);
        $otherProvider = User::factory()->create(['role' => UserRole::PROVIDER]);

        ProviderProfile::create([
            'user_id' => $matchingProvider->id,
            'business_name' => 'Expert Craft',
            'category' => 'Plumbing',
            'city' => 'Tunis',
            'bio' => 'Licensed professional with certified expertise.',
            'skills' => ['Plumbing'],
            'hourly_rate' => 45.00,
            'is_available' => true,
        ]);
        ProviderProfile::create([
            'user_id' => $otherProvider->id,
            'business_name' => 'Electrical Experts',
            'category' => 'Electrical',
            'city' => 'Tunis',
            'bio' => 'Experienced electrical service provider.',
            'skills' => ['Electrical'],
            'hourly_rate' => 50.00,
            'is_available' => true,
        ]);

        $this->actingAs($client)
            ->postJson('/api/v1/client/job-requests', [
                'title' => 'Repair kitchen sink',
                'category' => 'Plumbing',
                'description' => 'The kitchen sink pipe is leaking and needs repair.',
                'budget' => 125,
                'city' => 'Tunis',
                'deadline' => now()->addDay()->toDateString(),
            ])
            ->assertCreated();

        $notification = $matchingProvider->notifications()->first();
        $this->assertSame('new_request', $notification->data['type']);
        $this->assertSame('/dashboard', $notification->data['url']);
        $this->assertStringContainsString('Repair kitchen sink', $notification->data['message']);
        $this->assertSame(0, $otherProvider->notifications()->count());
        $this->assertSame(0, $client->notifications()->count());
    }

    public function test_booking_status_change_notifies_the_client(): void
    {
        $client = User::factory()->create(['role' => UserRole::CLIENT]);
        $provider = User::factory()->create(['role' => UserRole::PROVIDER]);
        $booking = Booking::factory()->create([
            'client_id' => $client->id,
            'provider_id' => $provider->id,
            'status' => 'pending',
        ]);

        $this->actingAs($provider)
            ->postJson("/api/v1/provider/bookings/{$booking->id}/accept")
            ->assertOk();

        $notification = $client->notifications()->first();
        $this->assertSame('booking_status', $notification->data['type']);
        $this->assertSame('Booking accepted', $notification->data['title']);
    }

    public function test_users_only_see_and_mark_their_own_notifications_read(): void
    {
        $user = User::factory()->create(['role' => UserRole::CLIENT]);
        $otherUser = User::factory()->create(['role' => UserRole::PROVIDER]);
        $user->notify(new ActivityNotification('new_message', 'New message', 'You have a message.', '/messages'));
        $otherUser->notify(new ActivityNotification('new_proposal', 'New proposal', 'A proposal arrived.', '/dashboard'));
        $notification = $user->notifications()->first();
        $otherNotification = $otherUser->notifications()->first();

        $this->getJson('/api/v1/notifications')
            ->assertUnauthorized();

        $this->actingAs($user)
            ->getJson('/api/v1/notifications')
            ->assertOk()
            ->assertJsonPath('unread_count', 1)
            ->assertJsonPath('notifications.0.id', $notification->id);

        $this->actingAs($user)
            ->postJson("/api/v1/notifications/{$otherNotification->id}/read")
            ->assertNotFound();

        $this->actingAs($user)
            ->postJson("/api/v1/notifications/{$notification->id}/read")
            ->assertOk()
            ->assertJsonPath('unread_count', 0);

        $this->assertNotNull($notification->fresh()->read_at);
        $this->assertNull($otherNotification->fresh()->read_at);
    }

    public function test_user_can_mark_all_notifications_read(): void
    {
        $user = User::factory()->create(['role' => UserRole::CLIENT]);
        $user->notify(new ActivityNotification('new_message', 'Message one', 'First message.', '/messages'));
        $user->notify(new ActivityNotification('new_message', 'Message two', 'Second message.', '/messages'));

        $this->actingAs($user)
            ->postJson('/api/v1/notifications/read-all')
            ->assertOk()
            ->assertJsonPath('unread_count', 0);

        $this->assertSame(0, $user->unreadNotifications()->count());
    }

    public function test_application_layout_renders_the_live_notification_menu(): void
    {
        config(['app.key' => 'base64:'.base64_encode(random_bytes(32))]);

        $this->get('/')
            ->assertOk()
            ->assertSee('notificationItems')
            ->assertSee('loadNotifications')
            ->assertDontSee('5-Star Review Received');
    }
}

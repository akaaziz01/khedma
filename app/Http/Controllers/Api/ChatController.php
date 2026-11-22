<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use App\Notifications\ActivityNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    /**
     * Get all conversations for the authenticated user.
     */
    public function conversations(Request $request): JsonResponse
    {
        $user = $request->user();

        $conversations = Conversation::where('client_id', $user->id)
            ->orWhere('provider_id', $user->id)
            ->with([
                'client:id,name,role',
                'provider:id,name,role',
                'jobRequest:id,title,category,budget',
                'booking:id,booking_number,service_title,total_price,status',
                'messages' => function ($query) {
                    $query->latest()->limit(1);
                },
            ])
            ->orderBy('last_message_at', 'desc')
            ->get()
            ->map(function ($conv) use ($user) {
                $otherUser = (int) $conv->client_id === (int) $user->id ? $conv->provider : $conv->client;
                if (! $otherUser) {
                    return null;
                }

                $unreadCount = Message::where('conversation_id', $conv->id)
                    ->where('sender_id', '!=', $user->id)
                    ->whereNull('read_at')
                    ->count();

                return [
                    'id' => $conv->id,
                    'participant' => [
                        'id' => $otherUser->id,
                        'name' => $otherUser->name,
                        'role' => $otherUser->role?->value ?? $otherUser->role,
                        'avatar_url' => $otherUser->avatar_url,
                    ],
                    'job_request' => $conv->jobRequest,
                    'booking' => $conv->booking,
                    'last_message' => $conv->messages->first(),
                    'unread_count' => $unreadCount,
                    'last_message_at' => $conv->last_message_at?->toISOString(),
                    'created_at' => $conv->created_at?->toISOString(),
                ];
            })
            ->filter()
            ->values();

        return response()->json([
            'conversations' => $conversations,
        ]);
    }

    /**
     * Start or retrieve an existing conversation with another user.
     */
    public function startConversation(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'provider_id' => ['nullable', 'exists:users,id'],
            'recipient_id' => ['nullable', 'exists:users,id'],
            'job_request_id' => ['nullable', 'exists:job_requests,id'],
            'booking_id' => ['nullable', 'exists:bookings,id'],
            'message' => ['nullable', 'string', 'max:3000'],
        ]);

        $recipientId = $validated['provider_id'] ?? $validated['recipient_id'] ?? null;

        if (! $recipientId || (int) $recipientId === (int) $user->id) {
            return response()->json([
                'message' => 'Invalid recipient specified.',
            ], 422);
        }

        $recipient = User::findOrFail($recipientId);

        // Determine client_id and provider_id roles
        if ($user->isClient() && $recipient->isProvider()) {
            $clientId = $user->id;
            $providerId = $recipient->id;
        } elseif ($user->isProvider() && $recipient->isClient()) {
            $clientId = $recipient->id;
            $providerId = $user->id;
        } else {
            $clientId = $user->id;
            $providerId = $recipient->id;
        }

        $conversation = Conversation::firstOrCreate(
            [
                'client_id' => $clientId,
                'provider_id' => $providerId,
            ],
            [
                'job_request_id' => $validated['job_request_id'] ?? null,
                'booking_id' => $validated['booking_id'] ?? null,
                'last_message_at' => now(),
            ]
        );

        if (! empty($validated['job_request_id']) && ! $conversation->job_request_id) {
            $conversation->job_request_id = $validated['job_request_id'];
            $conversation->save();
        }

        if (! empty($validated['message'])) {
            $conversation->messages()->create([
                'sender_id' => $user->id,
                'body' => $validated['message'],
            ]);
            $conversation->last_message_at = now();
            $conversation->save();
            $this->notifyRecipient($conversation, $user);
        }

        return response()->json([
            'conversation' => $conversation->load([
                'client:id,name,role',
                'provider:id,name,role',
                'jobRequest',
                'booking',
            ]),
        ], 201);
    }

    /**
     * Get messages in a conversation and mark incoming messages as read.
     */
    public function messages(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $conversation = Conversation::with(['client', 'provider', 'jobRequest', 'booking'])->findOrFail($id);

        if ((int) $conversation->client_id !== (int) $user->id && (int) $conversation->provider_id !== (int) $user->id && ! $user->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized access to this conversation.',
            ], 403);
        }

        // Mark unread incoming messages as read
        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $conversation->messages()
            ->with('sender:id,name')
            ->get();

        $otherUser = (int) $conversation->client_id === (int) $user->id ? $conversation->provider : $conversation->client;

        return response()->json([
            'conversation_id' => $conversation->id,
            'participant' => [
                'id' => $otherUser?->id,
                'name' => $otherUser?->name ?? 'User',
                'role' => $otherUser?->role?->value ?? $otherUser?->role ?? 'user',
                'avatar_url' => $otherUser?->avatar_url,
            ],
            'job_request' => $conversation->jobRequest,
            'booking' => $conversation->booking,
            'messages' => $messages,
        ]);
    }

    /**
     * Send a new message in a conversation.
     */
    public function sendMessage(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $conversation = Conversation::findOrFail($id);

        if ((int) $conversation->client_id !== (int) $user->id && (int) $conversation->provider_id !== (int) $user->id && ! $user->isAdmin()) {
            return response()->json([
                'message' => 'Unauthorized access to this conversation.',
            ], 403);
        }

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:3000'],
        ]);

        $message = $conversation->messages()->create([
            'sender_id' => $user->id,
            'body' => $validated['body'],
        ]);

        $conversation->last_message_at = now();
        $conversation->save();
        $this->notifyRecipient($conversation, $user);

        return response()->json([
            'message' => $message->load('sender:id,name'),
        ], 201);
    }

    private function notifyRecipient(Conversation $conversation, User $sender): void
    {
        $recipient = $conversation->client_id === $sender->id
            ? $conversation->provider
            : $conversation->client;

        $recipient->notify(new ActivityNotification(
            'new_message',
            'New message',
            "{$sender->name} sent you a message.",
            '/messages',
        ));
    }
}

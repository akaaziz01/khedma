<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\Checkout\Session as StripeSession;
use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\Webhook;

class StripePaymentController extends Controller
{
    /**
     * List all bookings for the authenticated client.
     */
    public function clientBookings(Request $request): JsonResponse
    {
        $client = $request->user();

        $bookings = Booking::where('client_id', $client->id)
            ->with(['provider.providerProfile', 'service'])
            ->latest()
            ->get()
            ->map(function ($b) {
                return [
                    'id'                     => $b->id,
                    'booking_number'         => $b->booking_number,
                    'service_title'          => $b->service_title,
                    'category'               => $b->category,
                    'status'                 => $b->status,
                    'payment_status'         => $b->payment_status ?? 'unpaid',
                    'payment_method'         => $b->payment_method,
                    'is_paid'                => $b->isPaid(),
                    'total_price'            => (float) $b->total_price,
                    'scheduled_at'           => $b->scheduled_at?->toIso8601String(),
                    'scheduled_at_formatted' => $b->scheduled_at?->format('d M Y, H:i'),
                    'address'                => $b->address,
                    'city'                   => $b->city,
                    'paid_at'                => $b->paid_at?->toIso8601String(),
                    'provider'               => [
                        'id'     => $b->provider?->id,
                        'name'   => $b->provider?->name,
                        'avatar' => $b->provider?->providerProfile?->avatar,
                    ],
                ];
            });

        return response()->json([
            'data' => $bookings,
        ]);
    }

    /**
     * Create a new booking request as a client.
     */
    public function storeBooking(Request $request): JsonResponse
    {
        $client = $request->user();
        if (! $client->isClient()) {
            return response()->json(['message' => 'Only clients can book services.'], 403);
        }

        $validated = $request->validate([
            'provider_id'   => 'required|exists:users,id',
            'service_id'    => 'nullable|exists:services,id',
            'service_title' => 'required|string|max:255',
            'category'      => 'nullable|string|max:255',
            'total_price'   => 'required|numeric|min:1',
            'scheduled_at'  => 'required|date',
            'address'       => 'required|string|max:255',
            'city'          => 'nullable|string|max:255',
            'client_notes'  => 'nullable|string|max:1000',
        ]);

        if ((int) $validated['provider_id'] === $client->id) {
            return response()->json(['message' => 'You cannot book your own service.'], 422);
        }

        $bookingNumber = 'BKG-' . strtoupper(Str::random(8));

        $booking = Booking::create([
            'booking_number' => $bookingNumber,
            'client_id'      => $client->id,
            'provider_id'    => $validated['provider_id'],
            'service_id'     => $validated['service_id'] ?? null,
            'service_title'  => $validated['service_title'],
            'category'       => $validated['category'] ?? 'General Maintenance',
            'status'         => 'pending',
            'payment_status' => 'unpaid',
            'total_price'    => $validated['total_price'],
            'scheduled_at'   => $validated['scheduled_at'],
            'address'        => $validated['address'],
            'city'           => $validated['city'] ?? 'Tunis',
            'client_notes'   => $validated['client_notes'] ?? null,
        ]);

        return response()->json([
            'message' => 'Booking request submitted successfully!',
            'data'    => $booking,
        ], 201);
    }

    /**
     * Create a Stripe Checkout Session for a given booking.
     */
    public function createCheckoutSession(Request $request, int $bookingId): JsonResponse
    {
        $user = $request->user();
        $booking = Booking::with(['service', 'provider'])->find($bookingId);

        if (! $booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        // Authorization check: only the client who created the booking can pay for it
        if ($booking->client_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized. You can only pay for your own bookings.'], 403);
        }

        // Check if already paid
        if ($booking->isPaid()) {
            return response()->json([
                'message'        => 'This booking has already been paid.',
                'payment_status' => $booking->payment_status,
            ], 422);
        }

        $secretKey = config('stripe.secret');
        if (empty($secretKey)) {
            return response()->json([
                'message' => 'Stripe secret key is not configured in .env',
            ], 500);
        }

        $stripe = new StripeClient($secretKey);
        $currency = strtolower(config('stripe.currency', 'eur'));

        // Stripe requires amount in the smallest currency unit (e.g. cents for EUR/USD)
        $amountInCents = (int) round(((float) $booking->total_price) * 100);
        if ($amountInCents < 50) {
            $amountInCents = 50; // Minimum Stripe transaction amount (0.50 EUR/USD)
        }

        $baseUrl = rtrim($request->header('Origin') ?: $request->getSchemeAndHttpHost(), '/');
        if (empty($baseUrl) || str_contains($baseUrl, 'null')) {
            $baseUrl = rtrim(config('app.url', url('/')), '/');
        }

        $successUrl = "{$baseUrl}/payment/success?session_id={CHECKOUT_SESSION_ID}&booking_id={$booking->id}";
        $cancelUrl = "{$baseUrl}/payment/cancel?booking_id={$booking->id}";

        try {
            $session = $stripe->checkout->sessions->create([
                'mode'                 => 'payment',
                'customer_email'       => $user->email,
                'client_reference_id'  => (string) $booking->id,
                'line_items'           => [[
                    'price_data' => [
                        'currency'     => $currency,
                        'unit_amount'  => $amountInCents,
                        'product_data' => [
                            'name'        => 'KHEDMA: ' . ($booking->service_title ?: 'Service Booking'),
                            'description' => "Booking #{$booking->booking_number} with provider " . ($booking->provider?->name ?? 'Specialist'),
                        ],
                    ],
                    'quantity'   => 1,
                ]],
                'metadata' => [
                    'booking_id'     => (string) $booking->id,
                    'booking_number' => (string) $booking->booking_number,
                    'client_id'      => (string) $user->id,
                    'provider_id'    => (string) $booking->provider_id,
                ],
                'success_url' => $successUrl,
                'cancel_url'  => $cancelUrl,
            ]);

            // Save the session ID on the booking
            $booking->update([
                'stripe_session_id' => $session->id,
                'payment_method'    => 'stripe',
                'payment_status'    => 'pending',
            ]);

            return response()->json([
                'message'      => 'Stripe checkout session created successfully.',
                'checkout_url' => $session->url,
                'session_id'   => $session->id,
                'booking_id'   => $booking->id,
            ]);
        } catch (Exception $e) {
            Log::error('Stripe Checkout Creation Error: ' . $e->getMessage());

            return response()->json([
                'message' => 'Unable to create Stripe checkout session: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Check payment status and verify with Stripe if needed.
     */
    public function checkStatus(Request $request, int $bookingId): JsonResponse
    {
        $user = $request->user();
        $booking = Booking::with('provider')->find($bookingId);

        if (! $booking) {
            return response()->json(['message' => 'Booking not found.'], 404);
        }

        if ($booking->client_id !== $user->id && $booking->provider_id !== $user->id && ! $user->isAdmin()) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        // If not marked paid in DB, but has a stripe_session_id, sync directly with Stripe
        if (! $booking->isPaid() && $booking->stripe_session_id) {
            $secretKey = config('stripe.secret');
            if (! empty($secretKey)) {
                try {
                    $stripe = new StripeClient($secretKey);
                    $session = $stripe->checkout->sessions->retrieve($booking->stripe_session_id);

                    if ($session->payment_status === 'paid') {
                        $paymentIntentId = is_string($session->payment_intent)
                            ? $session->payment_intent
                            : ($session->payment_intent?->id ?? 'pi_checkout_' . $session->id);

                        $booking->markAsPaid($paymentIntentId, $session->id);
                        $booking->refresh();
                    }
                } catch (Exception $e) {
                    Log::warning('Stripe Session Direct Sync Warning: ' . $e->getMessage());
                }
            }
        }

        return response()->json([
            'booking_id'               => $booking->id,
            'booking_number'           => $booking->booking_number,
            'payment_status'           => $booking->payment_status,
            'payment_method'           => $booking->payment_method,
            'paid_at'                  => $booking->paid_at?->toIso8601String(),
            'total_price'              => (float) $booking->total_price,
            'is_paid'                  => $booking->isPaid(),
            'stripe_payment_intent_id' => $booking->stripe_payment_intent_id,
        ]);
    }

    /**
     * Handle incoming Stripe Webhook events.
     */
    public function handleWebhook(Request $request): JsonResponse
    {
        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        $webhookSecret = config('stripe.webhook_secret');
        $secretKey = config('stripe.secret');

        Stripe::setApiKey($secretKey);

        $event = null;

        // Verify webhook signature if secret is defined
        if (! empty($webhookSecret) && ! empty($sigHeader)) {
            try {
                $event = Webhook::constructEvent($payload, $sigHeader, $webhookSecret);
            } catch (Exception $e) {
                Log::error('Stripe Webhook Signature Verification Failed: ' . $e->getMessage());
                return response()->json(['error' => 'Invalid signature'], 400);
            }
        } else {
            // In local/dev environments where webhook secret is not set yet
            $data = json_decode($payload, true);
            if (isset($data['type'])) {
                try {
                    $event = \Stripe\Event::constructFrom($data);
                } catch (Exception $e) {
                    Log::error('Stripe Webhook payload parsing failed: ' . $e->getMessage());
                    return response()->json(['error' => 'Invalid payload'], 400);
                }
            }
        }

        if (! $event) {
            return response()->json(['error' => 'No event parsed'], 400);
        }

        Log::info('Stripe Webhook Received: ' . $event->type);

        switch ($event->type) {
            case 'checkout.session.completed':
                $session = $event->data->object;
                $this->processCheckoutSessionCompleted($session);
                break;

            case 'payment_intent.succeeded':
                $paymentIntent = $event->data->object;
                Log::info('PaymentIntent succeeded: ' . $paymentIntent->id);
                break;

            case 'payment_intent.payment_failed':
                $paymentIntent = $event->data->object;
                Log::warning('PaymentIntent failed: ' . $paymentIntent->id);
                break;
        }

        return response()->json(['received' => true]);
    }

    /**
     * Mark booking as paid from a completed Checkout Session.
     */
    protected function processCheckoutSessionCompleted(object $session): void
    {
        $bookingId = $session->metadata->booking_id ?? $session->client_reference_id ?? null;

        $booking = null;
        if ($bookingId) {
            $booking = Booking::find($bookingId);
        }

        if (! $booking && ! empty($session->id)) {
            $booking = Booking::where('stripe_session_id', $session->id)->first();
        }

        if (! $booking) {
            Log::warning("Stripe Webhook: Booking not found for session {$session->id}");
            return;
        }

        $paymentIntentId = is_string($session->payment_intent)
            ? $session->payment_intent
            : ($session->payment_intent->id ?? 'pi_completed_' . $session->id);

        $booking->markAsPaid($paymentIntentId, $session->id);

        // If booking is pending, automatically confirm it
        if ($booking->status === 'pending') {
            $booking->update([
                'status'      => 'accepted',
                'accepted_at' => now(),
            ]);
        }

        Log::info("Booking #{$booking->booking_number} marked as PAID via Stripe Checkout session {$session->id}");
    }
}

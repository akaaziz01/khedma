<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Review;
use App\Notifications\ActivityNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientBookingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $client = $request->user();

        $query = Booking::where('client_id', $client->id)
            ->with(['provider:id,name,email,phone', 'provider.providerProfile', 'review'])
            ->latest('scheduled_at');

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $perPage = min(max((int) $request->query('per_page', 15), 1), 50);
        $bookings = $query->paginate($perPage);

        return response()->json([
            'data' => $bookings->items(),
            'meta' => [
                'current_page' => $bookings->currentPage(),
                'last_page' => $bookings->lastPage(),
                'per_page' => $bookings->perPage(),
                'total' => $bookings->total(),
            ],
        ]);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $booking = Booking::where('client_id', $request->user()->id)
            ->with(['provider:id,name,email,phone', 'provider.providerProfile', 'review'])
            ->findOrFail($id);

        return response()->json(['booking' => $booking]);
    }

    public function confirm(Request $request, int $id): JsonResponse
    {
        $booking = Booking::where('client_id', $request->user()->id)
            ->findOrFail($id);

        if ($booking->status !== 'completed') {
            return response()->json([
                'message' => 'You can confirm the work only after the provider marks it as completed.',
            ], 422);
        }

        if ($booking->client_confirmed_at) {
            return response()->json([
                'message' => 'This work has already been confirmed.',
            ], 422);
        }

        $booking->update([
            'client_confirmed_at' => now(),
        ]);

        $booking->provider->notify(new ActivityNotification(
            'booking_confirmed',
            'Client confirmed completed work',
            "The client confirmed completion of booking #{$booking->booking_number}.",
            '/provider/bookings',
        ));

        return response()->json([
            'message' => 'Work completed and confirmed successfully.',
            'booking' => $booking->fresh(['provider', 'review']),
        ]);
    }

    public function review(Request $request, int $id): JsonResponse
    {
        $validated = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        $booking = Booking::where('client_id', $request->user()->id)
            ->with('review')
            ->findOrFail($id);

        if ($booking->status !== 'completed') {
            return response()->json([
                'message' => 'Reviews can be submitted only for completed bookings.',
            ], 422);
        }

        if ($booking->review) {
            return response()->json([
                'message' => 'A review has already been submitted for this booking.',
            ], 422);
        }

        $review = Review::create([
            'booking_id' => $booking->id,
            'client_id' => $request->user()->id,
            'provider_id' => $booking->provider_id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'] ?? null,
            'status' => 'approved',
        ]);

        $booking->provider->notify(new ActivityNotification(
            'new_review',
            'New review received',
            "A client left a {$review->rating}-star review.",
            '/provider/dashboard',
        ));

        return response()->json([
            'message' => 'Review submitted successfully.',
            'review' => $review,
        ], 201);
    }

    public function receipts(Request $request): JsonResponse
    {
        $perPage = min(max((int) $request->query('per_page', 15), 1), 50);
        $receipts = Booking::where('client_id', $request->user()->id)
            ->where('status', 'completed')
            ->with(['provider:id,name,email'])
            ->latest('completed_at')
            ->paginate($perPage);

        $items = collect($receipts->items())->map(fn (Booking $booking) => [
            'receipt_number' => 'RCP-' . $booking->booking_number,
            'booking_number' => $booking->booking_number,
            'service_title' => $booking->service_title,
            'provider_name' => $booking->provider?->name,
            'amount' => (float) $booking->total_price,
            'currency' => 'TND',
            'completed_at' => $booking->completed_at?->toISOString(),
        ]);

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $receipts->currentPage(),
                'last_page' => $receipts->lastPage(),
                'per_page' => $receipts->perPage(),
                'total' => $receipts->total(),
            ],
        ]);
    }

    public function receipt(Request $request, int $id): JsonResponse
    {
        $booking = Booking::where('client_id', $request->user()->id)
            ->where('status', 'completed')
            ->with(['provider:id,name,email'])
            ->findOrFail($id);

        return response()->json([
            'receipt' => [
                'receipt_number' => 'RCP-' . $booking->booking_number,
                'booking_number' => $booking->booking_number,
                'service_title' => $booking->service_title,
                'provider_name' => $booking->provider?->name,
                'amount' => (float) $booking->total_price,
                'currency' => 'TND',
                'completed_at' => $booking->completed_at?->toISOString(),
                'issued_at' => now()->toISOString(),
            ],
        ]);
    }

}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ReviewModerationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Review::with([
            'booking:id,booking_number',
            'client:id,name,email',
            'provider:id,name,email',
        ])->latest();

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        $perPage = min(max((int) $request->query('per_page', 15), 1), 50);
        $reviews = $query->paginate($perPage);

        return response()->json([
            'data' => $reviews->items(),
            'meta' => [
                'current_page' => $reviews->currentPage(),
                'last_page' => $reviews->lastPage(),
                'per_page' => $reviews->perPage(),
                'total' => $reviews->total(),
            ],
        ]);
    }

    public function moderate(Request $request, Review $review): JsonResponse
    {
        $validated = $request->validate([
            'action' => ['required', Rule::in(['approve', 'hide', 'remove'])],
            'reason' => ['nullable', 'string', 'max:2000'],
        ]);

        $review->update([
            'status' => $validated['action'] === 'approve' ? 'approved' : 'hidden',
            'moderation_reason' => $validated['reason'] ?? null,
            'moderated_by' => $request->user()->id,
            'moderated_at' => now(),
        ]);

        return response()->json([
            'message' => $validated['action'] === 'approve'
                ? 'Review approved successfully.'
                : 'Review hidden successfully.',
            'review' => $review->fresh(),
        ]);
    }
}

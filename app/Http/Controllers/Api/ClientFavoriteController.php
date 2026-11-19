<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreFavoriteProviderRequest;
use App\Http\Resources\FavoriteProviderResource;
use App\Models\FavoriteProvider;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClientFavoriteController extends Controller
{
    /**
     * List all providers saved by the authenticated client.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = FavoriteProvider::where('client_id', $user->id)
            ->with([
                'provider.providerProfile',
                'provider.providerReviews',
                'provider.providerBookings',
            ])
            ->latest();

        // Optional search by provider name or trade
        $search = trim((string) $request->input('search', $request->input('q', '')));
        if ($search !== '') {
            $query->whereHas('provider', function ($pq) use ($search) {
                $pq->where('name', 'like', "%{$search}%")
                    ->orWhereHas('providerProfile', function ($ppq) use ($search) {
                        $ppq->where('category', 'like', "%{$search}%")
                            ->orWhere('business_name', 'like', "%{$search}%")
                            ->orWhere('city', 'like', "%{$search}%");
                    });
            });
        }

        // Optional filter by trade/category
        $trade = trim((string) $request->input('trade', $request->input('category', '')));
        if ($trade !== '') {
            $query->whereHas('provider.providerProfile', function ($ppq) use ($trade) {
                $ppq->where('category', 'like', "%{$trade}%");
            });
        }

        $perPage = min(max((int) $request->input('per_page', 12), 1), 50);
        $favorites = $query->paginate($perPage);

        return response()->json([
            'data' => FavoriteProviderResource::collection($favorites->items()),
            'meta' => [
                'current_page' => $favorites->currentPage(),
                'last_page'    => $favorites->lastPage(),
                'per_page'     => $favorites->perPage(),
                'total'        => $favorites->total(),
            ],
        ]);
    }

    /**
     * Save a provider to the client's favorites.
     */
    public function store(StoreFavoriteProviderRequest $request, int $providerId): JsonResponse
    {
        $client = $request->user();

        // Prevent self-favoriting
        if ($client->id === $providerId) {
            return response()->json([
                'message' => 'You cannot add your own account to favorites.',
            ], 422);
        }

        // Find target user
        $target = User::find($providerId);
        if (! $target) {
            return response()->json([
                'message' => 'Provider not found.',
            ], 404);
        }

        // Target must be a provider
        if (! $target->isProvider()) {
            return response()->json([
                'message' => 'Only active service providers can be added to favorites.',
            ], 422);
        }

        // Target must be active
        if ($target->status !== 'active') {
            return response()->json([
                'message' => 'This provider is currently inactive or suspended.',
            ], 422);
        }

        $favorite = $client->favorite($target, $request->input('notes'));
        $favorite->load([
            'provider.providerProfile',
            'provider.providerReviews',
            'provider.providerBookings',
        ]);

        return response()->json([
            'message'      => "{$target->name} has been added to your favorites.",
            'is_favorited' => true,
            'data'         => new FavoriteProviderResource($favorite),
        ], 201);
    }

    /**
     * Remove a provider from the client's favorites.
     */
    public function destroy(Request $request, int $providerId): JsonResponse
    {
        $client = $request->user();

        $client->unfavorite($providerId);

        return response()->json([
            'message'      => 'Provider removed from your favorites.',
            'is_favorited' => false,
        ]);
    }

    /**
     * Check if a specific provider is favorited by the current client.
     */
    public function status(Request $request, int $providerId): JsonResponse
    {
        $client = $request->user();
        $isFavorited = $client ? $client->hasFavorited($providerId) : false;

        return response()->json([
            'provider_id'  => $providerId,
            'is_favorited' => $isFavorited,
        ]);
    }
}

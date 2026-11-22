<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Client\StoreJobRequest;
use App\Http\Resources\JobRequestResource;
use App\Http\Resources\ProposalResource;
use App\Models\JobRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobRequestController extends Controller
{
    /**
     * List job requests posted by the authenticated client.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();

        $query = JobRequest::where('client_id', $user->id)
            ->with(['proposals.provider.providerProfile', 'acceptedProposal.provider'])
            ->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('category', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $perPage = min(max((int) $request->input('per_page', 10), 1), 50);
        $requests = $query->paginate($perPage);

        return response()->json([
            'data' => JobRequestResource::collection($requests->items()),
            'meta' => [
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
                'per_page' => $requests->perPage(),
                'total' => $requests->total(),
            ],
        ]);
    }

    /**
     * Store a newly created job request (Client only).
     */
    public function store(StoreJobRequest $request): JsonResponse
    {
        $client = $request->user();
        $location = trim((string) ($request->input('location') ?: $request->input('address') ?: $request->input('city')));
        $deadline = $request->input('deadline') ?: $request->input('preferred_date');

        $jobRequest = JobRequest::create([
            'request_number' => JobRequest::generateRequestNumber(),
            'client_id' => $client->id,
            'title' => $request->input('title'),
            'category' => $request->input('category'),
            'description' => $request->input('description'),
            'budget' => $request->input('budget'),
            'location' => $location,
            'city' => $request->input('city') ?: $location,
            'address' => $request->input('address'),
            'urgency' => $request->input('urgency', 'medium'),
            'deadline' => $deadline,
            'preferred_date' => $deadline,
            'status' => 'open',
        ]);

        return response()->json([
            'message' => 'Your job request has been published successfully. Providers in your trade will be notified.',
            'data' => new JobRequestResource($jobRequest),
        ], 201);
    }

    /**
     * Display a specific job request.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $user = $request->user();

        $jobRequest = JobRequest::with([
            'client',
            'proposals.provider.providerProfile',
            'proposals.provider.providerReviews',
            'proposals.provider.providerBookings',
            'acceptedProposal.provider.providerProfile',
            'booking',
        ])->find($id);

        if (! $jobRequest) {
            return response()->json(['message' => 'Job request not found.'], 404);
        }

        // Authorization: client owner or provider viewing open requests
        if ($user->isClient() && $jobRequest->client_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized access to this request.'], 403);
        }

        return response()->json([
            'data' => new JobRequestResource($jobRequest),
        ]);
    }

    /**
     * Dedicated proposal comparison endpoint for client.
     * Computes lowest price, fastest delivery, highest rated provider, and comparison matrix.
     */
    public function compare(Request $request, int $id): JsonResponse
    {
        $client = $request->user();

        $jobRequest = JobRequest::with([
            'client',
            'proposals.provider.providerProfile',
            'proposals.provider.providerReviews',
            'proposals.provider.providerBookings',
        ])->find($id);

        if (! $jobRequest) {
            return response()->json(['message' => 'Job request not found.'], 404);
        }

        if ($jobRequest->client_id !== $client->id) {
            return response()->json(['message' => 'Unauthorized to view proposal comparison for this request.'], 403);
        }

        $proposals = $jobRequest->proposals;

        if ($proposals->isEmpty()) {
            return response()->json([
                'job_request' => new JobRequestResource($jobRequest),
                'summary' => [
                    'proposals_count' => 0,
                    'lowest_price' => null,
                    'highest_price' => null,
                    'average_price' => null,
                    'fastest_days' => null,
                    'top_rating' => null,
                ],
                'proposals' => [],
            ]);
        }

        // Compute metrics
        $prices = $proposals->pluck('price')->map(fn ($p) => (float) $p);
        $minPrice = $prices->min();
        $maxPrice = $prices->max();
        $avgPrice = round($prices->avg(), 2);

        // Find fastest delivery (by numeric delivery_days if present, else fallback)
        $deliveryDaysList = $proposals->whereNotNull('delivery_days')->pluck('delivery_days');
        $minDays = $deliveryDaysList->isNotEmpty() ? $deliveryDaysList->min() : null;

        // Compute ratings for providers
        $ratingsMap = [];
        foreach ($proposals as $prop) {
            $revs = $prop->provider?->providerReviews;
            $ratingsMap[$prop->id] = ($revs && $revs->isNotEmpty()) ? round((float) $revs->avg('rating'), 1) : 0.0;
        }
        $maxRating = ! empty($ratingsMap) ? max($ratingsMap) : 0.0;

        // Decorate proposals with comparison flags
        $decoratedProposals = $proposals->map(function ($prop) use ($minPrice, $minDays, $ratingsMap, $maxRating, $request) {
            $isLowestPrice = ((float) $prop->price) == $minPrice;
            $isFastestDelivery = ($minDays !== null && $prop->delivery_days !== null && $prop->delivery_days == $minDays);
            $isTopRated = ($maxRating > 0 && isset($ratingsMap[$prop->id]) && $ratingsMap[$prop->id] == $maxRating);

            $resource = new ProposalResource($prop);
            $resource->setComparisonFlags([
                'is_lowest_price' => $isLowestPrice,
                'is_fastest_delivery' => $isFastestDelivery,
                'is_top_rated' => $isTopRated,
            ]);

            return $resource->toArray($request);
        });

        // Sort by lowest price by default or client requested sort
        $sortBy = $request->input('sort', 'price_asc');
        $sortedCollection = collect($decoratedProposals);

        if ($sortBy === 'price_desc') {
            $sortedCollection = $sortedCollection->sortByDesc('price')->values();
        } elseif ($sortBy === 'delivery_asc') {
            $sortedCollection = $sortedCollection->sortBy('delivery_days')->values();
        } elseif ($sortBy === 'rating_desc') {
            $sortedCollection = $sortedCollection->sortByDesc(fn ($p) => $p['provider']['rating'])->values();
        } else {
            $sortedCollection = $sortedCollection->sortBy('price')->values();
        }

        return response()->json([
            'job_request' => new JobRequestResource($jobRequest),
            'summary' => [
                'proposals_count' => $proposals->count(),
                'lowest_price' => $minPrice,
                'lowest_price_formatted' => number_format($minPrice, 2).' TND',
                'highest_price' => $maxPrice,
                'average_price' => $avgPrice,
                'average_price_formatted' => number_format($avgPrice, 2).' TND',
                'fastest_days' => $minDays,
                'top_rating' => $maxRating,
            ],
            'proposals' => $sortedCollection,
        ]);
    }

    /**
     * Provider feed: list open job requests matching provider's trade or city.
     */
    public function providerFeed(Request $request): JsonResponse
    {
        $provider = $request->user();
        if (! $provider->isProvider()) {
            return response()->json(['message' => 'Only providers can view the service request feed.'], 403);
        }

        $request->validate([
            'q' => ['sometimes', 'nullable', 'string', 'max:100'],
        ]);

        $profile = $provider->providerProfile;

        $query = JobRequest::whereIn('status', ['open', 'in_review'])
            ->with(['client', 'proposals' => function ($q) use ($provider) {
                $q->where('provider_id', $provider->id);
            }])
            ->latest();

        // Optional filter by category
        if ($category = $request->input('category', $profile?->category)) {
            $query->where('category', 'like', "%{$category}%");
        }

        // Optional filter by city
        if ($city = $request->input('city')) {
            $query->where('city', 'like', "%{$city}%");
        }

        // Optional filter by urgency
        if ($urgency = $request->input('urgency')) {
            $query->where('urgency', $urgency);
        }

        if ($search = trim((string) $request->input('q', ''))) {
            $query->where(function ($searchQuery) use ($search) {
                $searchQuery->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('request_number', 'like', "%{$search}%");
            });
        }

        $perPage = min(max((int) $request->input('per_page', 12), 1), 50);
        $requests = $query->paginate($perPage);

        $items = collect($requests->items())->map(function ($req) use ($provider) {
            $myProposal = $req->proposals->firstWhere('provider_id', $provider->id);

            return [
                'id' => $req->id,
                'request_number' => $req->request_number,
                'title' => $req->title,
                'category' => $req->category,
                'description' => $req->description,
                'budget' => $req->budget !== null ? (float) $req->budget : null,
                'budget_formatted' => $req->budget !== null ? number_format((float) $req->budget, 2).' TND' : 'Flexible',
                'location' => $req->location ?: ($req->address ?: $req->city),
                'city' => $req->city,
                'address' => $req->address,
                'urgency' => $req->urgency,
                'deadline' => ($req->deadline ?: $req->preferred_date)?->toISOString(),
                'preferred_date' => $req->preferred_date?->format('M d, Y'),
                'created_at_human' => $req->created_at?->diffForHumans(),
                'has_applied' => (bool) $myProposal,
                'my_proposal_status' => $myProposal?->status,
                'my_proposal_price' => $myProposal ? (float) $myProposal->price : null,
                'my_proposal_delivery_time' => $myProposal?->delivery_time,
                'my_proposal_delivery_days' => $myProposal?->delivery_days,
                'my_proposal_message' => $myProposal?->message,
            ];
        });

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $requests->currentPage(),
                'last_page' => $requests->lastPage(),
                'per_page' => $requests->perPage(),
                'total' => $requests->total(),
            ],
        ]);
    }
}

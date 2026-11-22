<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Provider\StoreProposalRequest;
use App\Http\Resources\ProposalResource;
use App\Models\Booking;
use App\Models\JobRequest;
use App\Models\Proposal;
use App\Notifications\ActivityNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProposalController extends Controller
{
    /**
     * Submit or update a proposal for a job request (Provider only).
     */
    public function store(StoreProposalRequest $request, int $jobRequestId): JsonResponse
    {
        $provider = $request->user();

        $jobRequest = JobRequest::find($jobRequestId);
        if (! $jobRequest) {
            return response()->json(['message' => 'Job request not found.'], 404);
        }

        if (! in_array($jobRequest->status, ['open', 'in_review'])) {
            return response()->json([
                'message' => 'This job request is no longer accepting new proposals.',
            ], 422);
        }

        // Provider cannot propose on their own request (if they registered as both or testing)
        if ($jobRequest->client_id === $provider->id) {
            return response()->json([
                'message' => 'You cannot submit a proposal on your own job request.',
            ], 422);
        }

        // Find existing proposal or create new
        $existing = Proposal::where('job_request_id', $jobRequestId)
            ->where('provider_id', $provider->id)
            ->first();

        if ($existing) {
            if ($existing->status !== 'pending') {
                return response()->json([
                    'message' => "Your proposal is already {$existing->status} and cannot be modified.",
                ], 422);
            }

            $existing->update([
                'price'                    => $request->input('price'),
                'delivery_time'            => $request->input('delivery_time'),
                'delivery_days'            => $request->input('delivery_days'),
                'estimated_duration_hours' => $request->input('estimated_duration_hours'),
                'message'                  => $request->input('message'),
            ]);

            $proposal = $existing;
            $statusMessage = 'Your proposal has been updated successfully.';
        } else {
            $proposal = Proposal::create([
                'proposal_number'          => Proposal::generateProposalNumber(),
                'job_request_id'           => $jobRequestId,
                'provider_id'              => $provider->id,
                'price'                    => $request->input('price'),
                'delivery_time'            => $request->input('delivery_time'),
                'delivery_days'            => $request->input('delivery_days'),
                'estimated_duration_hours' => $request->input('estimated_duration_hours'),
                'message'                  => $request->input('message'),
                'status'                   => 'pending',
            ]);

            // Mark job request as in_review if it was open
            if ($jobRequest->status === 'open') {
                $jobRequest->update(['status' => 'in_review']);
            }

            $jobRequest->client->notify(new ActivityNotification(
                'new_proposal',
                'New proposal received',
                "{$provider->name} sent a proposal for {$jobRequest->title}.",
                '/dashboard',
            ));

            $statusMessage = 'Your proposal has been submitted successfully to the client.';
        }

        $proposal->load([
            'provider.providerProfile',
            'provider.providerReviews',
            'provider.providerBookings',
        ]);

        return response()->json([
            'message' => $statusMessage,
            'data'    => new ProposalResource($proposal),
        ], 201);
    }

    /**
     * Client accepts a proposal.
     * Transitions proposal to 'accepted', rejects competing proposals, marks job request 'awarded',
     * and automatically generates a confirmed Booking record.
     */
    public function accept(Request $request, int $id): JsonResponse
    {
        $client = $request->user();

        $proposal = Proposal::with(['jobRequest', 'provider'])->find($id);
        if (! $proposal) {
            return response()->json(['message' => 'Proposal not found.'], 404);
        }

        $jobRequest = $proposal->jobRequest;

        // Authorization: only the client who created the job request can accept
        if ($jobRequest->client_id !== $client->id) {
            return response()->json([
                'message' => 'Unauthorized. Only the job owner can accept proposals.',
            ], 403);
        }

        if ($proposal->status !== 'pending') {
            return response()->json([
                'message' => "This proposal is already {$proposal->status}.",
            ], 422);
        }

        if ($jobRequest->status === 'awarded') {
            return response()->json([
                'message' => 'This job request has already been awarded.',
            ], 422);
        }

        [$booking, $rejectedProposals] = DB::transaction(function () use ($proposal, $jobRequest, $client) {
            // 1. Mark this proposal accepted
            $proposal->update([
                'status'      => 'accepted',
                'accepted_at' => now(),
            ]);

            // 2. Reject all other pending proposals for this job request
            $rejectedProposals = Proposal::where('job_request_id', $jobRequest->id)
                ->where('id', '!=', $proposal->id)
                ->where('status', 'pending')
                ->get();

            foreach ($rejectedProposals as $rejectedProposal) {
                $rejectedProposal->update([
                    'status'           => 'rejected',
                    'rejection_reason' => 'Client accepted another proposal for this request.',
                ]);
            }

            // 3. Create confirmed booking
            $scheduledDate = $jobRequest->preferred_date ?? now()->addDays(max(1, (int) ($proposal->delivery_days ?? 1)));

            $newBooking = Booking::create([
                'booking_number' => 'BK-' . strtoupper(Str::random(8)),
                'client_id'      => $client->id,
                'provider_id'    => $proposal->provider_id,
                'service_id'     => null,
                'service_title'  => $jobRequest->title,
                'category'       => $jobRequest->category,
                'status'         => 'confirmed',
                'total_price'    => $proposal->price,
                'scheduled_at'   => $scheduledDate,
                'address'        => $jobRequest->address,
                'city'           => $jobRequest->city,
                'client_notes'   => "Generated from Job Request #{$jobRequest->request_number}: {$jobRequest->description}",
                'provider_notes' => "Accepted Proposal #{$proposal->proposal_number} - Delivery timeline: {$proposal->delivery_time}",
            ]);

            // 4. Update job request to awarded
            $jobRequest->update([
                'status'               => 'awarded',
                'accepted_proposal_id' => $proposal->id,
                'booking_id'           => $newBooking->id,
            ]);

            return [$newBooking, $rejectedProposals];
        });

        $proposal->provider->notify(new ActivityNotification(
            'proposal_accepted',
            'Your proposal was accepted',
            "Your proposal for {$jobRequest->title} was accepted by the client.",
            '/provider/bookings',
        ));

        foreach ($rejectedProposals as $rejectedProposal) {
            $rejectedProposal->provider->notify(new ActivityNotification(
                'proposal_rejected',
                'Proposal status updated',
                "Another proposal was selected for {$jobRequest->title}.",
                '/dashboard',
            ));
        }

        $proposal->load([
            'provider.providerProfile',
            'provider.providerReviews',
            'provider.providerBookings',
        ]);

        return response()->json([
            'message' => 'Proposal accepted successfully! A confirmed booking has been created.',
            'data'    => [
                'proposal' => new ProposalResource($proposal),
                'booking'  => [
                    'id'             => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'status'         => $booking->status,
                    'total_price'    => (float) $booking->total_price,
                    'scheduled_at'   => $booking->scheduled_at?->toISOString(),
                ],
            ],
        ]);
    }

    /**
     * Client rejects a proposal.
     */
    public function reject(Request $request, int $id): JsonResponse
    {
        $client = $request->user();

        $proposal = Proposal::with('jobRequest')->find($id);
        if (! $proposal) {
            return response()->json(['message' => 'Proposal not found.'], 404);
        }

        $jobRequest = $proposal->jobRequest;

        if ($jobRequest->client_id !== $client->id) {
            return response()->json([
                'message' => 'Unauthorized. Only the job owner can decline proposals.',
            ], 403);
        }

        if ($proposal->status !== 'pending') {
            return response()->json([
                'message' => "This proposal is already {$proposal->status}.",
            ], 422);
        }

        $reason = $request->input('reason', 'Declined by client.');

        $proposal->update([
            'status'           => 'rejected',
            'rejection_reason' => $reason,
        ]);

        $proposal->provider->notify(new ActivityNotification(
            'proposal_rejected',
            'Your proposal was declined',
            "Your proposal for {$jobRequest->title} was declined by the client.",
            '/dashboard',
        ));

        return response()->json([
            'message' => 'Proposal has been declined.',
            'data'    => [
                'id'     => $proposal->id,
                'status' => 'rejected',
            ],
        ]);
    }

    /**
     * List all proposals submitted by the authenticated provider.
     */
    public function providerIndex(Request $request): JsonResponse
    {
        $provider = $request->user();

        $query = Proposal::where('provider_id', $provider->id)
            ->with(['jobRequest.client'])
            ->latest();

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $perPage = min(max((int) $request->input('per_page', 12), 1), 50);
        $proposals = $query->paginate($perPage);

        $items = collect($proposals->items())->map(function ($prop) {
            $job = $prop->jobRequest;
            return [
                'id'                       => $prop->id,
                'proposal_number'          => $prop->proposal_number,
                'price'                    => (float) $prop->price,
                'price_formatted'          => number_format((float) $prop->price, 2) . ' TND',
                'delivery_time'            => $prop->delivery_time,
                'delivery_days'            => $prop->delivery_days,
                'estimated_duration_hours' => $prop->estimated_duration_hours,
                'message'                  => $prop->message,
                'status'                   => $prop->status,
                'accepted_at'              => $prop->accepted_at?->toISOString(),
                'rejection_reason'         => $prop->rejection_reason,
                'created_at_human'         => $prop->created_at?->diffForHumans(),
                'job_request'              => [
                    'id'             => $job?->id,
                    'request_number' => $job?->request_number,
                    'title'          => $job?->title,
                    'category'       => $job?->category,
                    'city'           => $job?->city,
                    'status'         => $job?->status,
                    'client_name'    => $job?->client?->name,
                ],
            ];
        });

        return response()->json([
            'data' => $items,
            'meta' => [
                'current_page' => $proposals->currentPage(),
                'last_page'    => $proposals->lastPage(),
                'per_page'     => $proposals->perPage(),
                'total'        => $proposals->total(),
            ],
        ]);
    }
}

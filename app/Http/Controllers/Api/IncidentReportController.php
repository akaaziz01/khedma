<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\IncidentReport;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class IncidentReportController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        if ($request->input('type') === 'client' && ! $request->user()->isProvider()) {
            return response()->json(['message' => 'Only providers can report a client.'], 403);
        }

        if ($request->input('type') === 'provider' && ! $request->user()->isClient()) {
            return response()->json(['message' => 'Only clients can report a provider.'], 403);
        }

        $validated = $request->validate([
            'type' => ['required', Rule::in(['client', 'provider', 'issue'])],
            'booking_id' => [
                'required_if:type,client,provider',
                'prohibited_unless:type,client,provider',
                'integer',
                'exists:bookings,id',
            ],
            'subject' => ['required', 'string', 'min:3', 'max:160'],
            'details' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $booking = null;
        if ($validated['type'] === 'client') {
            $booking = Booking::query()
                ->where('provider_id', $request->user()->id)
                ->findOrFail($validated['booking_id']);
        }
        if ($validated['type'] === 'provider') {
            $booking = Booking::query()
                ->where('client_id', $request->user()->id)
                ->findOrFail($validated['booking_id']);
        }

        $report = IncidentReport::create([
            'reporter_id' => $request->user()->id,
            'reported_client_id' => $validated['type'] === 'client' ? $booking?->client_id : null,
            'reported_provider_id' => $validated['type'] === 'provider' ? $booking?->provider_id : null,
            'booking_id' => $booking?->id,
            'type' => $validated['type'],
            'subject' => $validated['subject'],
            'details' => $validated['details'],
            'status' => 'open',
        ]);

        return response()->json([
            'message' => 'Your report has been submitted for review.',
            'report' => $report,
        ], 201);
    }

    public function index(): JsonResponse
    {
        $reports = IncidentReport::query()
            ->with([
                'reporter:id,name,email',
                'reportedClient:id,name,email',
                'reportedProvider:id,name,email',
                'booking:id,booking_number',
            ])
            ->latest()
            ->paginate(50);

        return response()->json([
            'data' => $reports->items(),
            'pagination' => [
                'total' => $reports->total(),
                'per_page' => $reports->perPage(),
                'current_page' => $reports->currentPage(),
                'last_page' => $reports->lastPage(),
            ],
        ]);
    }

    public function update(Request $request, IncidentReport $report): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['required', Rule::in(['open', 'in_review', 'resolved', 'dismissed'])],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $report->update($validated);

        return response()->json([
            'message' => 'Report updated successfully.',
            'report' => $report->fresh(),
        ]);
    }
}

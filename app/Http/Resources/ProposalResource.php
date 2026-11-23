<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProposalResource extends JsonResource
{
    /**
     * Optional comparison flags passed via withComparisonFlags().
     */
    protected array $comparisonFlags = [];

    public function setComparisonFlags(array $flags): self
    {
        $this->comparisonFlags = $flags;
        return $this;
    }

    public function toArray(Request $request): array
    {
        $provider = $this->provider;
        $profile = $provider?->providerProfile;

        // Compute rating from reviews
        $reviewsCount = $provider
            ? $provider->providerReviews()->where('status', 'approved')->count()
            : 0;
        $avgRating = $reviewsCount > 0
            ? round((float) $provider->providerReviews->avg('rating'), 1)
            : 0.0;

        $completedBookingsCount = $provider?->providerBookings
            ? $provider->providerBookings->where('status', 'completed')->count()
            : 0;

        return [
            'id'                       => $this->id,
            'proposal_number'          => $this->proposal_number,
            'job_request_id'           => $this->job_request_id,
            'provider_id'              => $this->provider_id,
            'price'                    => (float) $this->price,
            'price_formatted'          => number_format((float) $this->price, 2) . ' TND',
            'delivery_time'            => $this->delivery_time,
            'delivery_days'            => $this->delivery_days ? (int) $this->delivery_days : null,
            'estimated_duration_hours' => $this->estimated_duration_hours !== null ? (float) $this->estimated_duration_hours : null,
            'message'                  => $this->message,
            'status'                   => $this->status,
            'accepted_at'              => $this->accepted_at?->toISOString(),
            'rejection_reason'         => $this->rejection_reason,
            'created_at'               => $this->created_at?->toISOString(),
            'created_at_human'         => $this->created_at?->diffForHumans(),

            // Provider profile summary
            'provider' => [
                'id'                       => $provider?->id,
                'name'                     => $provider?->name,
                'business_name'            => $profile?->business_name ?: $provider?->name,
                'category'                 => $profile?->category,
                'city'                     => $profile?->city,
                'avatar'                   => $provider?->avatar,
                'is_verified'              => (bool) ($provider?->is_verified ?? false),
                'is_available'             => (bool) ($profile?->is_available ?? true),
                'rating'                   => $avgRating,
                'reviews_count'            => $reviewsCount,
                'completed_bookings_count' => $completedBookingsCount,
            ],

            // Comparison tags (populated when comparing)
            'is_lowest_price'    => $this->comparisonFlags['is_lowest_price'] ?? false,
            'is_fastest_delivery'=> $this->comparisonFlags['is_fastest_delivery'] ?? false,
            'is_top_rated'       => $this->comparisonFlags['is_top_rated'] ?? false,
        ];
    }
}

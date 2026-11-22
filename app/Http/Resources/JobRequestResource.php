<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class JobRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $client = $this->client;

        return [
            'id'                   => $this->id,
            'request_number'       => $this->request_number,
            'title'                => $this->title,
            'category'             => $this->category,
            'description'          => $this->description,
            'budget'               => $this->budget !== null ? (float) $this->budget : null,
            'budget_formatted'     => $this->budget !== null ? number_format((float) $this->budget, 2) . ' TND' : null,
            'city'                 => $this->city,
            'address'              => $this->address,
            'urgency'              => $this->urgency,
            'preferred_date'       => $this->preferred_date?->toISOString(),
            'preferred_date_human' => $this->preferred_date?->format('M d, Y H:i'),
            'status'               => $this->status,
            'accepted_proposal_id' => $this->accepted_proposal_id,
            'booking_id'           => $this->booking_id,
            'created_at'           => $this->created_at?->toISOString(),
            'created_at_human'     => $this->created_at?->diffForHumans(),

            // Client summary
            'client' => [
                'id'    => $client?->id,
                'name'  => $client?->name,
                'email' => $client?->email,
            ],

            'proposals_count'   => $this->relationLoaded('proposals') ? $this->proposals->count() : $this->proposals()->count(),
            'proposals'         => ProposalResource::collection($this->whenLoaded('proposals')),
            'accepted_proposal' => $this->acceptedProposal ? new ProposalResource($this->acceptedProposal) : null,
        ];
    }
}

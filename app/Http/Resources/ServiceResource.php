<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'title'            => $this->title,
            'description'      => $this->description,
            'category'         => $this->category,                          // legacy string
            'category_id'      => $this->category_id,
            'category_detail'  => new ServiceCategoryResource($this->whenLoaded('serviceCategory')),
            'price'            => (float) $this->price,
            'duration_hours'   => (float) $this->duration_hours,
            'is_active'        => $this->is_active,
            'status'           => $this->status,
            'rejection_reason' => $this->when($this->status === 'rejected', $this->rejection_reason),
            'moderated_at'     => $this->moderated_at?->toISOString(),
            'moderated_by'     => $this->moderated_by,
            'provider'         => $this->whenLoaded('provider', fn () => [
                'id'          => $this->provider->id,
                'name'        => $this->provider->name,
                'email'       => $this->provider->email,
                'phone'       => $this->provider->phone,
                'is_verified' => (bool) $this->provider->is_verified,
                'profile'     => $this->provider->relationLoaded('providerProfile') && $this->provider->providerProfile ? [
                    'business_name' => $this->provider->providerProfile->business_name,
                    'city'          => $this->provider->providerProfile->city,
                    'category'      => $this->provider->providerProfile->category,
                    'skills'        => $this->provider->providerProfile->skills,
                    'hourly_rate'   => (float) $this->provider->providerProfile->hourly_rate,
                    'is_available'  => (bool) $this->provider->providerProfile->is_available,
                ] : null,
            ]),
            'created_at'       => $this->created_at?->toISOString(),
            'updated_at'       => $this->updated_at?->toISOString(),
        ];
    }
}


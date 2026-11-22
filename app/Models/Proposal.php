<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Proposal extends Model
{
    use HasFactory;

    protected $table = 'proposals';

    protected $fillable = [
        'proposal_number',
        'job_request_id',
        'provider_id',
        'price',
        'delivery_time',
        'delivery_days',
        'estimated_duration_hours',
        'message',
        'status',
        'accepted_at',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'price'                    => 'decimal:2',
            'delivery_days'            => 'integer',
            'estimated_duration_hours' => 'decimal:2',
            'accepted_at'              => 'datetime',
        ];
    }

    public static function generateProposalNumber(): string
    {
        do {
            $number = 'PRP-' . strtoupper(Str::random(8));
        } while (static::where('proposal_number', $number)->exists());

        return $number;
    }

    public function jobRequest(): BelongsTo
    {
        return $this->belongsTo(JobRequest::class, 'job_request_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    public function isAccepted(): bool
    {
        return $this->status === 'accepted';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }
}

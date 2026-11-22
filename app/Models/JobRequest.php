<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class JobRequest extends Model
{
    use HasFactory;

    protected $table = 'job_requests';

    protected $fillable = [
        'request_number',
        'client_id',
        'title',
        'category',
        'description',
        'budget',
        'location',
        'city',
        'address',
        'urgency',
        'deadline',
        'preferred_date',
        'status',
        'accepted_proposal_id',
        'booking_id',
    ];

    protected function casts(): array
    {
        return [
            'budget'         => 'decimal:2',
            'deadline'       => 'datetime',
            'preferred_date' => 'datetime',
        ];
    }

    public static function generateRequestNumber(): string
    {
        do {
            $number = 'REQ-' . strtoupper(Str::random(8));
        } while (static::where('request_number', $number)->exists());

        return $number;
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function proposals(): HasMany
    {
        return $this->hasMany(Proposal::class, 'job_request_id');
    }

    public function acceptedProposal(): BelongsTo
    {
        return $this->belongsTo(Proposal::class, 'accepted_proposal_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class, 'booking_id');
    }

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isAwarded(): bool
    {
        return $this->status === 'awarded';
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'booking_number',
        'client_id',
        'provider_id',
        'service_id',
        'service_title',
        'category',
        'status',
        'total_price',
        'scheduled_at',
        'address',
        'city',
        'client_notes',
        'provider_notes',
        'declined_reason',
        'completed_at',
        'accepted_at',
        'declined_at',
    ];

    protected function casts(): array
    {
        return [
            'total_price'  => 'decimal:2',
            'scheduled_at' => 'datetime',
            'completed_at' => 'datetime',
            'accepted_at'  => 'datetime',
            'declined_at'  => 'datetime',
        ];
    }

    // ── Relations ─────────────────────────────────────────────────────────────

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function provider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'provider_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function review(): HasOne
    {
        return $this->hasOne(Review::class);
    }

    // ── Status Helpers ────────────────────────────────────────────────────────

    public function isPending(): bool    { return $this->status === 'pending'; }
    public function isAccepted(): bool   { return $this->status === 'accepted'; }
    public function isInProgress(): bool { return $this->status === 'in_progress'; }
    public function isCompleted(): bool  { return $this->status === 'completed'; }
    public function isDeclined(): bool   { return $this->status === 'declined'; }
    public function isCancelled(): bool  { return $this->status === 'cancelled'; }
    public function isImmutable(): bool  { return in_array($this->status, ['completed', 'declined', 'cancelled']); }

    /**
     * Valid status transitions from each state.
     */
    public static function allowedTransitions(): array
    {
        return [
            'pending'     => ['accepted', 'declined'],
            'accepted'    => ['in_progress', 'cancelled'],
            'in_progress' => ['completed'],
            'completed'   => [],
            'declined'    => [],
            'cancelled'   => [],
        ];
    }

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, static::allowedTransitions()[$this->status] ?? []);
    }
}

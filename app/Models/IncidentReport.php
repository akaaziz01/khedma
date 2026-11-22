<?php

namespace App\Models;

use Database\Factories\IncidentReportFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentReport extends Model
{
    /** @use HasFactory<IncidentReportFactory> */
    use HasFactory;

    protected $fillable = [
        'reporter_id',
        'reported_client_id',
        'booking_id',
        'type',
        'subject',
        'details',
        'status',
        'admin_notes',
    ];

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function reportedClient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reported_client_id');
    }

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }
}

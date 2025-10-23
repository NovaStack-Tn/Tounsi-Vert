<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'participation_id',
        'organization_id',
        'event_id',
        'amount',
        'status',
        'payment_ref',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function participation()
    {
        return $this->belongsTo(Participation::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    // Helper method to get user directly through participation
    public function user()
    {
        return $this->hasOneThrough(User::class, Participation::class, 'id', 'id', 'participation_id', 'user_id');
    }

    // Scope for filtering by user
    public function scopeByUser($query, $userId)
    {
        return $query->whereHas('participation', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        });
    }

    // Scope for filtering by organization
    public function scopeByOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    // Scope for filtering by status
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    // Scope for date range filtering
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }
}

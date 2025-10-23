<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'organization_id',
        'event_category_id',
        'type',
        'title',
        'description',
        'start_at',
        'end_at',
        'max_participants',
        'meeting_url',
        'address',
        'region',
        'city',
        'zipcode',
        'poster_path',
        'is_published',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_published' => 'boolean',
        'max_participants' => 'integer',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'event_category_id');
    }

    public function participations()
    {
        return $this->hasMany(Participation::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    public function attendees()
    {
        return $this->hasMany(Participation::class)->where('type', 'attend');
    }

    public function followers()
    {
        return $this->hasMany(Participation::class)->where('type', 'follow');
    }

    public function averageRating()
    {
        return $this->reviews()->avg('rate');
    }

    public function isFull()
    {
        if (!$this->max_participants) {
            return false;
        }
        return $this->attendees()->count() >= $this->max_participants;
    }

    // ============================================
    // Query Scopes for Advanced Filtering
    // ============================================

    /**
     * Apply multiple filters to events query
     */
    public function scopeFilter($query, $filters)
    {
        return $query
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where('title', 'like', '%'.$search.'%')
                      ->orWhere('description', 'like', '%'.$search.'%');
            })
            ->when($filters['category_id'] ?? null, fn($q, $v) => $q->where('event_category_id', $v))
            ->when($filters['type'] ?? null, fn($q, $v) => $q->where('type', $v))
            ->when($filters['region'] ?? null, fn($q, $v) => $q->where('region', $v))
            ->when($filters['city'] ?? null, fn($q, $v) => $q->where('city', $v))
            ->when($filters['start_date'] ?? null, fn($q, $v) => $q->where('start_at', '>=', $v))
            ->when($filters['end_date'] ?? null, fn($q, $v) => $q->where('end_at', '<=', $v))
            ->when($filters['status'] ?? null, function ($query, $status) {
                if ($status === 'upcoming') {
                    $query->where('start_at', '>', now());
                } elseif ($status === 'ongoing') {
                    $query->where('start_at', '<=', now())
                          ->where('end_at', '>=', now());
                } elseif ($status === 'past') {
                    $query->where('end_at', '<', now());
                }
            })
            ->when($filters['published'] ?? null, fn($q, $v) => $q->where('is_published', $v === '1'));
    }

    /**
     * Search events by title or description
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('title', 'like', '%'.$term.'%')
              ->orWhere('description', 'like', '%'.$term.'%');
        });
    }

    /**
     * Sort events by specified field
     */
    public function scopeSort($query, $field, $direction = 'asc')
    {
        $allowedFields = ['title', 'start_at', 'end_at', 'created_at'];
        
        if (in_array($field, $allowedFields)) {
            return $query->orderBy($field, $direction);
        }
        
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get upcoming events
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_at', '>', now())->orderBy('start_at', 'asc');
    }

    /**
     * Get past events
     */
    public function scopePast($query)
    {
        return $query->where('end_at', '<', now())->orderBy('start_at', 'desc');
    }

    /**
     * Get ongoing events
     */
    public function scopeOngoing($query)
    {
        return $query->where('start_at', '<=', now())
                    ->where('end_at', '>=', now());
    }

    /**
     * Get published events only
     */
    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    /**
     * Filter by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('event_category_id', $categoryId);
    }

    /**
     * Filter by region
     */
    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Get events with available capacity
     */
    public function scopeWithAvailableSpots($query)
    {
        return $query->whereNotNull('max_participants')
                    ->whereRaw('max_participants > (SELECT COUNT(*) FROM participations WHERE participations.event_id = events.id AND participations.type = "attend")');
    }
}

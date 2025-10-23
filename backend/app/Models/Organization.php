<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organization extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'owner_id',
        'org_category_id',
        'name',
        'description',
        'address',
        'region',
        'city',
        'zipcode',
        'phone_number',
        'logo_path',
        'is_verified',
        'is_blocked',
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'is_blocked' => 'boolean',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function category()
    {
        return $this->belongsTo(OrgCategory::class, 'org_category_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }

    public function socialLinks()
    {
        return $this->hasMany(OrganizationSocialLink::class);
    }

    public function followers()
    {
        return $this->belongsToMany(User::class, 'organization_followers')->withTimestamps();
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function reports()
    {
        return $this->hasMany(Report::class);
    }

    // ============================================
    // Query Scopes for Advanced Filtering
    // ============================================

    /**
     * Apply multiple filters to organizations query
     */
    public function scopeFilter($query, $filters)
    {
        return $query
            ->when($filters['search'] ?? null, function ($query, $search) {
                $query->where('name', 'like', '%'.$search.'%')
                      ->orWhere('description', 'like', '%'.$search.'%');
            })
            ->when($filters['category_id'] ?? null, fn($q, $v) => $q->where('org_category_id', $v))
            ->when($filters['region'] ?? null, fn($q, $v) => $q->where('region', $v))
            ->when($filters['city'] ?? null, fn($q, $v) => $q->where('city', $v))
            ->when($filters['verified'] ?? null, function ($query, $verified) {
                if ($verified === 'verified') {
                    $query->where('is_verified', true)->where('is_blocked', false);
                } elseif ($verified === 'pending') {
                    $query->where('is_verified', false)->where('is_blocked', false);
                } elseif ($verified === 'blocked') {
                    $query->where('is_blocked', true);
                }
            });
    }

    /**
     * Search organizations by name or description
     */
    public function scopeSearch($query, $term)
    {
        return $query->where(function($q) use ($term) {
            $q->where('name', 'like', '%'.$term.'%')
              ->orWhere('description', 'like', '%'.$term.'%');
        });
    }

    /**
     * Sort organizations by specified field
     */
    public function scopeSort($query, $field, $direction = 'asc')
    {
        $allowedFields = ['name', 'created_at', 'region', 'city'];
        
        if (in_array($field, $allowedFields)) {
            return $query->orderBy($field, $direction);
        }
        
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get verified organizations only
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true)->where('is_blocked', false);
    }

    /**
     * Get pending (not verified) organizations
     */
    public function scopePending($query)
    {
        return $query->where('is_verified', false)->where('is_blocked', false);
    }

    /**
     * Get blocked organizations
     */
    public function scopeBlocked($query)
    {
        return $query->where('is_blocked', true);
    }

    /**
     * Filter by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('org_category_id', $categoryId);
    }

    /**
     * Filter by region
     */
    public function scopeByRegion($query, $region)
    {
        return $query->where('region', $region);
    }

    /**
     * Load organization with donation insights
     */
    public function scopeWithInsights($query)
    {
        return $query->withCount('donations')
                    ->withSum('donations', 'amount')
                    ->with('donations');
    }

    // ============================================
    // Helper Methods
    // ============================================

    /**
     * Calculate profile completeness percentage
     */
    public function getProfileCompletenessAttribute()
    {
        $fields = [
            'name' => 10,
            'description' => 15,
            'address' => 10,
            'region' => 5,
            'city' => 5,
            'zipcode' => 5,
            'phone_number' => 10,
            'logo_path' => 15,
            'org_category_id' => 10,
        ];

        $score = 0;
        foreach ($fields as $field => $points) {
            if (!empty($this->$field)) {
                $score += $points;
            }
        }

        // Additional points for related data
        if ($this->events()->count() > 0) $score += 10;
        if ($this->socialLinks()->count() > 0) $score += 5;

        return min($score, 100);
    }

    /**
     * Get donation insights for last 30 days
     */
    public function getDonationInsights()
    {
        $thirtyDaysAgo = now()->subDays(30);
        $sixtyDaysAgo = now()->subDays(60);

        $current = $this->donations()
            ->where('status', 'succeeded')
            ->where('created_at', '>=', $thirtyDaysAgo)
            ->sum('amount');

        $previous = $this->donations()
            ->where('status', 'succeeded')
            ->whereBetween('created_at', [$sixtyDaysAgo, $thirtyDaysAgo])
            ->sum('amount');

        $trend = $previous > 0 ? (($current - $previous) / $previous) * 100 : 0;

        return [
            'total' => $current ?? 0,
            'previous' => $previous ?? 0,
            'trend' => round($trend, 1),
            'donors' => $this->donations()
                ->where('status', 'succeeded')
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->distinct('participation_id')
                ->count('participation_id'),
            'avg_donation' => $this->donations()
                ->where('status', 'succeeded')
                ->where('created_at', '>=', $thirtyDaysAgo)
                ->avg('amount') ?? 0,
        ];
    }
}

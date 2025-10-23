<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'organization_id',
        'event_id',
        'reason',
        'details',
        'status',
        'priority',
        'category',
        'resolved_at',
        'resolved_by',
        'ai_risk_score',
        'ai_suggested_category',
        'ai_confidence',
        'ai_auto_flagged',
        'ai_analysis',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'ai_auto_flagged' => 'boolean',
        'ai_analysis' => 'array',
    ];

    /**
     * Get the user who submitted the report
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the event being reported
     */
    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /**
     * Get the organization being reported
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the admin who resolved the report
     */
    public function resolver()
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    /**
     * Get all actions taken on this report
     */
    public function actions()
    {
        return $this->hasMany(ReportAction::class)->orderBy('action_taken_at', 'desc');
    }

    /**
     * Get the latest action on this report
     */
    public function latestAction()
    {
        return $this->hasOne(ReportAction::class)->latestOfMany('action_taken_at');
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeAttribute()
    {
        $badges = [
            'open' => 'danger',
            'in_review' => 'warning',
            'resolved' => 'success',
            'dismissed' => 'secondary',
        ];

        return $badges[$this->status] ?? 'secondary';
    }

    /**
     * Get priority badge color
     */
    public function getPriorityBadgeAttribute()
    {
        $badges = [
            'low' => 'info',
            'medium' => 'warning',
            'high' => 'danger',
            'critical' => 'dark',
        ];

        return $badges[$this->priority] ?? 'secondary';
    }

    /**
     * Get category label
     */
    public function getCategoryLabelAttribute()
    {
        $labels = [
            'spam' => 'Spam',
            'inappropriate' => 'Inappropriate Content',
            'fraud' => 'Fraud/Scam',
            'harassment' => 'Harassment',
            'violence' => 'Violence',
            'misinformation' => 'Misinformation',
            'copyright' => 'Copyright Violation',
            'other' => 'Other',
        ];

        return $labels[$this->category] ?? $this->category;
    }

    /**
     * Scope for open reports
     */
    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    /**
     * Scope for reports by organization
     */
    public function scopeForOrganization($query, $organizationId)
    {
        return $query->where('organization_id', $organizationId);
    }

    /**
     * Scope for high risk reports
     */
    public function scopeHighRisk($query)
    {
        return $query->where('ai_risk_score', '>=', 70);
    }

    /**
     * Scope for auto-flagged reports
     */
    public function scopeAutoFlagged($query)
    {
        return $query->where('ai_auto_flagged', true);
    }

    /**
     * Get AI risk level badge
     */
    public function getAiRiskLevelAttribute()
    {
        if ($this->ai_risk_score >= 80) return 'critical';
        if ($this->ai_risk_score >= 60) return 'high';
        if ($this->ai_risk_score >= 40) return 'medium';
        return 'low';
    }

    /**
     * Get AI risk badge color
     */
    public function getAiRiskBadgeAttribute()
    {
        $badges = [
            'critical' => 'danger',
            'high' => 'warning',
            'medium' => 'info',
            'low' => 'success',
        ];

        return $badges[$this->aiRiskLevel] ?? 'secondary';
    }
}

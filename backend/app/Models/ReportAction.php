<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportAction extends Model
{
    use HasFactory;

    protected $fillable = [
        'report_id',
        'admin_id',
        'action_type',
        'action_note',
        'internal_note',
        'action_taken_at',
    ];

    protected $casts = [
        'action_taken_at' => 'datetime',
    ];

    /**
     * Get the report that owns this action
     */
    public function report()
    {
        return $this->belongsTo(Report::class);
    }

    /**
     * Get the admin who performed this action
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Get action type label
     */
    public function getActionTypeLabelAttribute()
    {
        $labels = [
            'reviewed' => 'Reviewed',
            'investigating' => 'Under Investigation',
            'resolved' => 'Resolved',
            'dismissed' => 'Dismissed',
            'warning_sent' => 'Warning Sent',
            'content_removed' => 'Content Removed',
            'account_suspended' => 'Account Suspended',
        ];

        return $labels[$this->action_type] ?? $this->action_type;
    }

    /**
     * Get action type badge color
     */
    public function getActionTypeBadgeAttribute()
    {
        $badges = [
            'reviewed' => 'info',
            'investigating' => 'warning',
            'resolved' => 'success',
            'dismissed' => 'secondary',
            'warning_sent' => 'warning',
            'content_removed' => 'danger',
            'account_suspended' => 'danger',
        ];

        return $badges[$this->action_type] ?? 'secondary';
    }
}

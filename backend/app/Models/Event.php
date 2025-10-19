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
}

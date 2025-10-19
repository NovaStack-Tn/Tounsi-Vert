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
}

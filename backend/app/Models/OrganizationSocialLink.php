<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrganizationSocialLink extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id',
        'title',
        'url',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}

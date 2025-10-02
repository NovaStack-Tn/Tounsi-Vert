<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrgCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function organizations()
    {
        return $this->hasMany(Organization::class, 'org_category_id');
    }
}

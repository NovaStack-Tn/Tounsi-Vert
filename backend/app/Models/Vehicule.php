<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicule extends Model
{
    protected $fillable = [
        'owner_id',
        'type',
        'description',
        'capacity',
        'availability_start',
        'availability_end',
        'location',
        'status',
        'requires_fuel_compensation',
        'verified',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}

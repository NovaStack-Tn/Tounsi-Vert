<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'requester_id',
        'owner_id',
        'pickup_location',
        'dropoff_location',
        'scheduled_time',
        'status',
        'vehicule_id',
        'notes',
    ];

    public function vehicule()
    {
        return $this->belongsTo(Vehicule::class);
    }
}

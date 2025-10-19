<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Vehicule;

class BookingController extends Controller
{
    public function create(Request $request)
    {
        $vehiculeId = $request->query('vehicule');
        $vehicule = Vehicule::findOrFail($vehiculeId);
        return view('bookings.create', compact('vehicule'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'pickup_location'   => 'required|string|max:255',
            'dropoff_location'  => 'required|string|max:255',
            'scheduled_time'    => 'nullable|date',
            'vehicule_id'       => 'required|exists:vehicules,id',
            'notes'             => 'nullable|string|max:1000',
        ]);

        $vehicule = Vehicule::findOrFail($data['vehicule_id']);

        $data['requester_id'] = auth()->id() ?? 1; // fallback for testing
        $data['owner_id'] = $vehicule->owner_id ?? 1;
        $data['status'] = 'pending';

        Booking::create($data);

        return redirect()->route('vehicules.index')->with('success', 'Booking created successfully!');
    }

    public function index()
    {
        $bookings = Booking::with('vehicule')->latest()->get();
        return view('bookings.index', compact('bookings'));
    }
}

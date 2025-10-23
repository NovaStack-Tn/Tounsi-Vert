<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Vehicule;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

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
        // Validate form
        $data = $request->validate([
            'pickup_location'   => 'required|string|max:255',
            'dropoff_location'  => 'required|string|max:255',
            'scheduled_time'    => 'nullable|date',
            'vehicule_id'       => 'required|exists:vehicules,id',
            'notes'             => 'nullable|string|max:1000',
        ]);

        // Find the vehicle
        $vehicule = Vehicule::findOrFail($data['vehicule_id']);

        // Prepare booking data
        $data['requester_id'] = auth()->id() ?? 1; // fallback for testing
        $data['owner_id'] = $vehicule->owner_id ?? 1;
        $data['status'] = 'pending';

        // Create booking
        $booking = Booking::create($data);

// Send email to vehicle owner
$owner = User::find($data['owner_id']);
if ($owner && $owner->email) {
    Mail::send('bookings.booking_confirmation', ['vehicule' => $vehicule, 'booking' => $booking, 'owner' => $owner], function ($message) use ($owner) {
        $message->to($owner->email)
                ->subject('New Vehicle Booking');
    });
}
 


        return redirect()->route('vehicules.index')->with('success', 'Booking created successfully! Email sent to owner.');
    }

    public function index()
    {
        $bookings = Booking::with('vehicule')->latest()->get();
        return view('bookings.index', compact('bookings'));
    }

public function quickForm()
{
    return view('bookings.quick');
}

public function quickMatch(Request $request)
{
    $prompt = strtolower($request->input('prompt'));

    $vehicules = \App\Models\Vehicule::with('owner')->get();
    $bestMatch = null;
    $bestScore = -1;

    foreach ($vehicules as $vehicule) {
        $text = strtolower($vehicule->type . ' ' . ($vehicule->description ?? ''));
        similar_text($prompt, $text, $score);
        if ($score > $bestScore) {
            $bestScore = $score;
            $bestMatch = $vehicule;
        }
    }

    if (!$bestMatch && $vehicules->count() > 0) {
        $bestMatch = $vehicules->first();
    }

    // ✅ Send email only with vehicule and owner
    if ($bestMatch && $bestMatch->owner && $bestMatch->owner->email) {
        \Mail::raw(
            "Your vehicle \"{$bestMatch->type}\" was matched with a user request: \"{$prompt}\".",
            function ($message) use ($bestMatch) {
                $message->to($bestMatch->owner->email)
                        ->subject('Quick Match Notification');
            }
        );
    }

    // ✅ Display confirmation message instead of full card
    return view('bookings.quick', [
        'match' => $bestMatch,
        'emailSent' => $bestMatch && $bestMatch->owner && $bestMatch->owner->email,
    ]);
}






 

            





}

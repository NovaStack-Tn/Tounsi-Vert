<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Donation;
use App\Models\Event;
use App\Models\Participation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        // Create participation
        $participation = Participation::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
            'type' => 'donation',
        ]);

        // Create donation
        $donation = Donation::create([
            'participation_id' => $participation->id,
            'organization_id' => $event->organization_id,
            'event_id' => $event->id,
            'amount' => $request->amount,
            'status' => 'pending',
        ]);

        // In a real app, integrate payment gateway here
        // For now, mark as succeeded
        $donation->update([
            'status' => 'succeeded',
            'paid_at' => now(),
        ]);

        // Update user score
        Auth::user()->increment('score', floor($request->amount));

        return redirect()->back()->with('success', 'Don effectué avec succès!');
    }
}

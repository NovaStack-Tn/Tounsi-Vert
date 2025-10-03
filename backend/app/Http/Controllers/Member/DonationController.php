<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Donation;
use App\Models\Participation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class  DonationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function create(Event $event)
    {
        return view('member.donations.create', compact('event'));
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
        ]);

        try {
            DB::transaction(function () use ($request, $event) {
                // Create participation
                $participation = Participation::create([
                    'user_id' => auth()->id(),
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

                // In real app, integrate with payment gateway here
                // For demo, mark as succeeded immediately
                $donation->update([
                    'status' => 'succeeded',
                    'paid_at' => now(),
                    'payment_ref' => 'DEMO-' . uniqid(),
                ]);

                // Update user score
                auth()->user()->increment('score', floor($request->amount));
            });

            return redirect()->route('events.show', $event)->with('success', 'Thank you for your donation!');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while processing your donation.');
        }
    }
}

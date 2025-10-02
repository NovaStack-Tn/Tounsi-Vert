<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Review;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'rate' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        // Check if user participated
        $participated = $event->participations()
            ->where('user_id', auth()->id())
            ->whereIn('type', ['attend', 'donation', 'follow'])
            ->exists();

        if (!$participated) {
            return back()->with('error', 'You must participate in the event before reviewing.');
        }

        try {
            Review::create([
                'user_id' => auth()->id(),
                'event_id' => $event->id,
                'rate' => $request->rate,
                'comment' => $request->comment,
            ]);

            return back()->with('success', 'Review submitted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'You have already reviewed this event.');
        }
    }
}

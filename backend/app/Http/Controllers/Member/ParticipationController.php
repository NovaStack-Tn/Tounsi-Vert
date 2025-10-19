<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ParticipationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function attend(Event $event)
    {
        if ($event->isFull()) {
            return back()->with('error', 'This event is full.');
        }

        try {
            DB::transaction(function () use ($event) {
                Participation::create([
                    'user_id' => auth()->id(),
                    'event_id' => $event->id,
                    'type' => 'attend',
                ]);

                // Update user score
                auth()->user()->increment('score', 10);
            });

            return back()->with('success', 'You have successfully joined this event!');
        } catch (\Exception $e) {
            return back()->with('error', 'You have already joined this event.');
        }
    }

    public function follow(Event $event)
    {
        try {
            DB::transaction(function () use ($event) {
                Participation::create([
                    'user_id' => auth()->id(),
                    'event_id' => $event->id,
                    'type' => 'follow',
                ]);

                auth()->user()->increment('score', 1);
            });

            return back()->with('success', 'You are now following this event!');
        } catch (\Exception $e) {
            return back()->with('error', 'You are already following this event.');
        }
    }

    public function unjoin(Event $event)
    {
        $participation = Participation::where('user_id', auth()->id())
            ->where('event_id', $event->id)
            ->where('type', 'attend')
            ->first();

        if (!$participation) {
            return back()->with('error', 'You have not joined this event.');
        }

        try {
            DB::transaction(function () use ($participation) {
                $participation->delete();
                
                // Decrease user score
                auth()->user()->decrement('score', 10);
            });

            return back()->with('success', 'You have successfully left this event.');
        } catch (\Exception $e) {
            return back()->with('error', 'Unable to leave this event.');
        }
    }

    public function share(Event $event, Request $request)
    {
        $request->validate([
            'platform' => 'nullable|string',
        ]);

        try {
            DB::transaction(function () use ($event, $request) {
                Participation::create([
                    'user_id' => auth()->id(),
                    'event_id' => $event->id,
                    'type' => 'share',
                    'meta' => ['platform' => $request->platform ?? 'other'],
                ]);

                auth()->user()->increment('score', 2);
            });

            return back()->with('success', 'Thank you for sharing!');
        } catch (\Exception $e) {
            return back()->with('error', 'You have already shared this event.');
        }
    }
}

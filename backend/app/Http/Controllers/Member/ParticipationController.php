<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Participation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ParticipationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'type' => 'required|in:attend,follow,share',
        ]);

        $participation = Participation::create([
            'user_id' => Auth::id(),
            'event_id' => $event->id,
            'type' => $request->type,
            'meta' => $request->meta ?? null,
        ]);

        // Update user score
        $this->updateUserScore($request->type);

        return redirect()->back()->with('success', 'Participation enregistrée avec succès!');
    }

    private function updateUserScore($type)
    {
        $user = Auth::user();
        $points = [
            'attend' => 10,
            'follow' => 1,
            'share' => 2,
        ];

        $user->increment('score', $points[$type] ?? 0);
    }
}

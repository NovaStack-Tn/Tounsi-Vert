<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();
        
        // Get all participations with relationships
        $participations = $user->participations()
            ->with(['event.organization', 'event.category', 'donation'])
            ->latest()
            ->paginate(10);

        // Count total events attended
        $eventsAttended = $user->participations()
            ->where('type', 'attend')
            ->count();

        // Get donations with amount
        $donations = $user->participations()
            ->where('type', 'donation')
            ->with(['donation', 'event.organization'])
            ->whereHas('donation', function ($query) {
                $query->where('status', 'succeeded');
            })
            ->get();

        $totalDonationsCount = $donations->count();
        $totalDonationsAmount = $donations->sum(function ($participation) {
            return $participation->donation->amount ?? 0;
        });

        // Score breakdown calculation
        $scoreBreakdown = [
            'attend' => [
                'count' => $user->participations()->where('type', 'attend')->count(),
                'points_each' => 10,
                'total' => $user->participations()->where('type', 'attend')->count() * 10
            ],
            'follow' => [
                'count' => $user->participations()->where('type', 'follow')->count(),
                'points_each' => 1,
                'total' => $user->participations()->where('type', 'follow')->count() * 1
            ],
            'share' => [
                'count' => $user->participations()->where('type', 'share')->count(),
                'points_each' => 2,
                'total' => $user->participations()->where('type', 'share')->count() * 2
            ],
        ];

        return view('member.dashboard', compact(
            'user', 
            'participations', 
            'eventsAttended', 
            'donations',
            'totalDonationsCount',
            'totalDonationsAmount',
            'scoreBreakdown'
        ));
    }
}

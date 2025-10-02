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
        $participations = $user->participations()
            ->with(['event.organization', 'donation'])
            ->latest()
            ->paginate(10);

        $totalDonations = $user->participations()
            ->where('type', 'donation')
            ->whereHas('donation', function ($query) {
                $query->where('status', 'succeeded');
            })
            ->count();

        $eventsAttended = $user->participations()
            ->where('type', 'attend')
            ->count();

        return view('member.dashboard', compact('user', 'participations', 'totalDonations', 'eventsAttended'));
    }
}

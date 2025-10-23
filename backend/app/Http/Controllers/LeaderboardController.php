<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Organization;
use App\Models\Donation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeaderboardController extends Controller
{
    public function index()
    {
        // Top 10 Donators (by total donation amount)
        $topDonators = User::select(
                'users.id',
                'users.first_name',
                'users.last_name',
                'users.email',
                'users.city',
                'users.region',
                'users.score',
                DB::raw('SUM(donations.amount) as total_donated')
            )
            ->join('participations', 'users.id', '=', 'participations.user_id')
            ->join('donations', 'participations.id', '=', 'donations.participation_id')
            ->where('donations.status', 'succeeded')
            ->groupBy('users.id', 'users.first_name', 'users.last_name', 'users.email', 'users.city', 'users.region', 'users.score')
            ->orderByDesc('total_donated')
            ->limit(10)
            ->get();

        // Top 10 Users by Score
        $topUsers = User::where('role', '!=', 'admin')
            ->orderByDesc('score')
            ->limit(10)
            ->get();

        // Top 10 Organizations by Followers
        $topOrganizations = Organization::withCount('followers')
            ->having('followers_count', '>', 0)
            ->orderByDesc('followers_count')
            ->limit(10)
            ->get();

        return view('leaderboard.index', compact('topDonators', 'topUsers', 'topOrganizations'));
    }
}

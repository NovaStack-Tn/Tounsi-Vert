<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organization;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $upcomingEvents = Event::with(['organization', 'category'])
            ->where('is_published', true)
            ->where('start_at', '>', now())
            ->orderBy('start_at')
            ->take(6)
            ->get();

        $featuredOrganizations = Organization::where('is_verified', true)
            ->withCount('followers')
            ->orderBy('followers_count', 'desc')
            ->take(6)
            ->get();

        // Statistics
        $stats = [
            'total_events' => Event::where('is_published', true)->count(),
            'total_organizations' => Organization::where('is_verified', true)->count(),
            'total_attendees' => \DB::table('participations')->where('type', 'attend')->distinct('user_id')->count(),
            'total_donations' => \DB::table('donations')->where('status', 'succeeded')->sum('amount'),
        ];

        return view('home', compact('upcomingEvents', 'featuredOrganizations', 'stats'));
    }

    public function about()
    {
        return view('about');
    }
}

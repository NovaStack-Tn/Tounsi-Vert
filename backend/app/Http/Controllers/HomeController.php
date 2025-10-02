<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Organization;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $upcomingEvents = Event::where('is_published', true)
            ->where('start_at', '>', now())
            ->with(['organization', 'category'])
            ->orderBy('start_at', 'asc')
            ->take(6)
            ->get();

        $featuredOrganizations = Organization::where('is_verified', true)
            ->with('category')
            ->take(6)
            ->get();

        return view('home', compact('upcomingEvents', 'featuredOrganizations'));
    }
}

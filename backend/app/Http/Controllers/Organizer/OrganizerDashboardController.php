<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrganizerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $organizations = $user->organizationsOwned()->with('category', 'events')->get();
        
        $stats = [
            'total_organizations' => $organizations->count(),
            'verified_organizations' => $organizations->where('is_verified', true)->count(),
            'total_events' => $organizations->sum(function ($org) {
                return $org->events->count();
            }),
            'published_events' => $organizations->sum(function ($org) {
                return $org->events->where('is_published', true)->count();
            }),
            'total_attendees' => $organizations->sum(function ($org) {
                return $org->events->sum(function ($event) {
                    return $event->participations->where('type', 'attend')->count();
                });
            }),
            'total_donations' => $organizations->sum(function ($org) {
                return $org->events->sum(function ($event) {
                    return $event->donations->where('status', 'succeeded')->sum('amount');
                });
            }),
        ];
        
        $recentEvents = $organizations->flatMap(function ($org) {
            return $org->events;
        })->sortByDesc('created_at')->take(5);
        
        return view('organizer.dashboard', compact('stats', 'organizations', 'recentEvents'));
    }
}

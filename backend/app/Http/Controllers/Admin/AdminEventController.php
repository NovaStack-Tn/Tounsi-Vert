<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminEventController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $organizationFilter = $request->get('organization_id', 'all');
        
        $query = Event::with(['organization', 'category'])
            ->withCount(['participations as attendees_count' => function ($q) {
                $q->where('type', 'attend');
            }])
            ->withCount('reviews')
            ->withAvg('reviews', 'rate')
            ->withSum('donations', 'amount');
        
        if ($organizationFilter !== 'all') {
            $query->where('organization_id', $organizationFilter);
        }
        
        $events = $query->latest()->paginate(20)->appends(['organization_id' => $organizationFilter]);
        
        $organizations = Organization::orderBy('name')->get();
        
        // Statistics
        $stats = [
            'total_events' => Event::count(),
            'published_events' => Event::where('is_published', true)->count(),
            'draft_events' => Event::where('is_published', false)->count(),
            'total_attendees' => DB::table('participations')->where('type', 'attend')->count(),
            'total_donations' => DB::table('donations')->where('status', 'succeeded')->sum('amount'),
        ];

        return view('admin.events.index', compact('events', 'organizations', 'organizationFilter', 'stats'));
    }

    public function show(Event $event)
    {
        $event->load([
            'organization',
            'category',
            'reviews.user',
            'participations' => function ($q) {
                $q->where('type', 'attend')->with('user');
            },
            'donations' => function ($q) {
                $q->where('status', 'succeeded')->with('participation.user');
            }
        ]);

        $stats = [
            'total_attendees' => $event->participations->where('type', 'attend')->count(),
            'total_reviews' => $event->reviews->count(),
            'average_rating' => $event->reviews->avg('rate'),
            'total_donations' => $event->donations->where('status', 'succeeded')->sum('amount'),
            'total_donors' => $event->donations->where('status', 'succeeded')->unique('participation_id')->count(),
        ];

        return view('admin.events.show', compact('event', 'stats'));
    }

    public function destroy(Event $event)
    {
        $eventTitle = $event->title;
        $event->delete();
        
        return redirect()->route('admin.events.index')
            ->with('success', "Event '{$eventTitle}' has been deleted successfully!");
    }

    public function leaderboard()
    {
        // Top events by average rating
        $topRatedEvents = Event::withAvg('reviews', 'rate')
            ->withCount('reviews')
            ->having('reviews_count', '>', 0)
            ->orderByDesc('reviews_avg_rate')
            ->limit(10)
            ->get();

        // Top events by total attendees
        $topAttendedEvents = Event::withCount(['participations as attendees_count' => function ($q) {
                $q->where('type', 'attend');
            }])
            ->having('attendees_count', '>', 0)
            ->orderByDesc('attendees_count')
            ->limit(10)
            ->get();

        // Top events by donations
        $topDonatedEvents = Event::withSum('donations', 'amount')
            ->having('donations_sum_amount', '>', 0)
            ->orderByDesc('donations_sum_amount')
            ->limit(10)
            ->get();

        // Top organizations by events
        $topOrganizations = Organization::withCount('events')
            ->having('events_count', '>', 0)
            ->orderByDesc('events_count')
            ->limit(10)
            ->get();

        return view('admin.events.leaderboard', compact(
            'topRatedEvents',
            'topAttendedEvents',
            'topDonatedEvents',
            'topOrganizations'
        ));
    }
}

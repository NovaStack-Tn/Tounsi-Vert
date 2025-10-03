<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class OrganizerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Get user's single organization
        $organization = $user->organizationsOwned()
            ->with(['category', 'events.category', 'events.reviews', 'events.participations', 'events.donations', 'followers'])
            ->first();
        
        // If no organization, redirect to create
        if (!$organization) {
            return redirect()->route('organizer.organizations.create')
                ->with('info', 'Please create your organization first.');
        }
        
        // Calculate comprehensive stats
        $stats = [
            'total_events' => $organization->events->count(),
            'published_events' => $organization->events->where('is_published', true)->count(),
            'draft_events' => $organization->events->where('is_published', false)->count(),
            'upcoming_events' => $organization->events->where('is_published', true)->where('start_at', '>', now())->count(),
            'past_events' => $organization->events->where('start_at', '<', now())->count(),
            'total_followers' => $organization->followers->count(),
            'total_attendees' => $organization->events->sum(function ($event) {
                return $event->participations->where('type', 'attend')->count();
            }),
            'total_donations_amount' => $organization->events->sum(function ($event) {
                return $event->donations->where('status', 'succeeded')->sum('amount');
            }),
            'total_donations_count' => $organization->events->sum(function ($event) {
                return $event->donations->where('status', 'succeeded')->count();
            }),
            'total_reviews' => $organization->events->sum(function ($event) {
                return $event->reviews->count();
            }),
            'average_rating' => $organization->events->flatMap(function ($event) {
                return $event->reviews;
            })->avg('rate') ?? 0,
        ];
        
        // Recent events
        $recentEvents = $organization->events()
            ->with(['category', 'participations'])
            ->latest()
            ->take(5)
            ->get();
        
        // Monthly events data for chart (last 6 months)
        $monthlyEventsData = [];
        $monthlyDonationsData = [];
        $monthlyAttendeesData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $month = $date->format('M');
            
            // Events per month
            $monthlyEventsData[] = [
                'month' => $month,
                'count' => $organization->events()
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count()
            ];
            
            // Donations per month
            $monthlyDonations = 0;
            foreach ($organization->events as $event) {
                $monthlyDonations += $event->donations()
                    ->where('status', 'succeeded')
                    ->whereYear('paid_at', $date->year)
                    ->whereMonth('paid_at', $date->month)
                    ->sum('amount');
            }
            $monthlyDonationsData[] = [
                'month' => $month,
                'amount' => $monthlyDonations
            ];
            
            // Attendees per month
            $monthlyAttendees = 0;
            foreach ($organization->events as $event) {
                $monthlyAttendees += $event->participations()
                    ->where('type', 'attend')
                    ->whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count();
            }
            $monthlyAttendeesData[] = [
                'month' => $month,
                'count' => $monthlyAttendees
            ];
        }
        
        // Event types distribution for pie chart
        $eventTypeData = [
            'online' => $organization->events->where('type', 'online')->count(),
            'onsite' => $organization->events->where('type', 'onsite')->count(),
            'hybrid' => $organization->events->where('type', 'hybrid')->count(),
        ];
        
        // Event categories distribution
        $categoryData = $organization->events->groupBy('event_category_id')->map(function ($events) {
            return [
                'name' => $events->first()->category->name ?? 'Unknown',
                'count' => $events->count()
            ];
        })->values()->toArray();
        
        // Recent reviews
        $recentReviews = $organization->events()
            ->with(['reviews.user', 'reviews.event'])
            ->get()
            ->flatMap(function ($event) {
                return $event->reviews;
            })
            ->sortByDesc('created_at')
            ->take(5);
        
        return view('organizer.dashboard', compact(
            'organization', 
            'stats', 
            'recentEvents', 
            'monthlyEventsData',
            'monthlyDonationsData',
            'monthlyAttendeesData',
            'eventTypeData',
            'categoryData',
            'recentReviews'
        ));
    }
    
    public function community()
    {
        $user = auth()->user();
        
        // Get user's organization
        $organization = $user->organizationsOwned()->first();
        
        if (!$organization) {
            return redirect()->route('organizer.dashboard')
                ->with('error', 'Organization not found.');
        }
        
        // Get all followers with pagination
        $followers = $organization->followers()
            ->withCount('participations')
            ->withCount(['participations as events_attended_count' => function ($query) use ($organization) {
                $query->where('type', 'attend')
                    ->whereHas('event', function ($q) use ($organization) {
                        $q->where('organization_id', $organization->id);
                    });
            }])
            ->latest('organization_followers.created_at')
            ->paginate(20);
        
        // Stats
        $stats = [
            'total_followers' => $organization->followers->count(),
            'new_this_month' => $organization->followers()
                ->whereYear('organization_followers.created_at', now()->year)
                ->whereMonth('organization_followers.created_at', now()->month)
                ->count(),
        ];
        
        return view('organizer.community', compact('organization', 'followers', 'stats'));
    }
    
    public function donations()
    {
        $user = auth()->user();
        
        // Get user's organization
        $organization = $user->organizationsOwned()->first();
        
        if (!$organization) {
            return redirect()->route('organizer.dashboard')
                ->with('error', 'Organization not found.');
        }
        
        // Get all donations from organization's events
        $donations = \App\Models\Donation::whereHas('event', function ($query) use ($organization) {
                $query->where('organization_id', $organization->id);
            })
            ->with(['participation.user', 'event'])
            ->where('status', 'succeeded')
            ->latest('paid_at')
            ->paginate(20);
        
        // Stats
        $stats = [
            'total_amount' => \App\Models\Donation::whereHas('event', function ($query) use ($organization) {
                    $query->where('organization_id', $organization->id);
                })
                ->where('status', 'succeeded')
                ->sum('amount'),
            'total_count' => \App\Models\Donation::whereHas('event', function ($query) use ($organization) {
                    $query->where('organization_id', $organization->id);
                })
                ->where('status', 'succeeded')
                ->count(),
            'this_month' => \App\Models\Donation::whereHas('event', function ($query) use ($organization) {
                    $query->where('organization_id', $organization->id);
                })
                ->where('status', 'succeeded')
                ->whereYear('paid_at', now()->year)
                ->whereMonth('paid_at', now()->month)
                ->sum('amount'),
        ];
        
        return view('organizer.donations', compact('organization', 'donations', 'stats'));
    }
}

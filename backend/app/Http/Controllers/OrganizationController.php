<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrgCategory;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $query = Organization::with('category')
            ->where('is_verified', true);

        if ($request->filled('category')) {
            $query->where('org_category_id', $request->category);
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        $organizations = $query->withCount('followers')->paginate(12);
        $categories = OrgCategory::all();

        return view('organizations.index', compact('organizations', 'categories'));
    }

    public function show(Organization $organization)
    {
        $organization->load(['category', 'socialLinks', 'followers']);

        // Get published events with pagination
        $events = $organization->events()
            ->where('is_published', true)
            ->with(['category', 'reviews'])
            ->withCount('participations')
            ->withAvg('reviews', 'rate')
            ->orderBy('start_at')
            ->paginate(6);

        $followersCount = $organization->followers()->count();
        
        // Check if current user is following
        $isFollowing = false;
        if (auth()->check()) {
            $isFollowing = $organization->followers()->where('user_id', auth()->id())->exists();
        }

        // Calculate total donations
        $totalDonations = $organization->events()
            ->with(['donations' => function ($query) {
                $query->where('status', 'succeeded');
            }])
            ->get()
            ->sum(function ($event) {
                return $event->donations->where('status', 'succeeded')->sum('amount');
            });

        // Calculate average rating
        $averageRating = $organization->events()
            ->with('reviews')
            ->get()
            ->flatMap(function ($event) {
                return $event->reviews;
            })
            ->avg('rate') ?? 0;

        // Get reports with actions (JOIN between reports and report_actions)
        $reports = $organization->reports()
            ->with(['user', 'actions.admin', 'latestAction'])
            ->where('status', '!=', 'dismissed')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Count reports by status
        $reportsCount = [
            'total' => $organization->reports()->count(),
            'open' => $organization->reports()->where('status', 'open')->count(),
            'in_review' => $organization->reports()->where('status', 'in_review')->count(),
            'resolved' => $organization->reports()->where('status', 'resolved')->count(),
        ];

        return view('organizations.show', compact(
            'organization', 
            'followersCount', 
            'isFollowing', 
            'totalDonations', 
            'averageRating', 
            'events',
            'reports',
            'reportsCount'
        ));
    }

    public function follow(Organization $organization)
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to follow organizations.');
        }

        $user = auth()->user();

        // Check if already following
        if ($organization->followers()->where('user_id', $user->id)->exists()) {
            return back()->with('info', 'You are already following this organization.');
        }

        $organization->followers()->attach($user->id);

        return back()->with('success', 'You are now following ' . $organization->name . '!');
    }

    public function unfollow(Organization $organization)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $user = auth()->user();
        $organization->followers()->detach($user->id);

        return back()->with('success', 'You have unfollowed ' . $organization->name . '.');
    }
}

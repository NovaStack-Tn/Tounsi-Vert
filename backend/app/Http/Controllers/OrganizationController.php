<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\OrgCategory;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
    public function index(Request $request)
    {
        $query = Organization::query()->where('is_verified', true);

        if ($request->filled('category')) {
            $query->where('org_category_id', $request->category);
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        $organizations = $query->with('category')
            ->paginate(12);

        $categories = OrgCategory::all();

        return view('organizations.index', compact('organizations', 'categories'));
    }

    public function show(Organization $organization)
    {
        $organization->load(['category', 'events', 'socialLinks']);
        
        $followersCount = $organization->followers()->count();
        $upcomingEvents = $organization->events()
            ->where('is_published', true)
            ->where('start_at', '>', now())
            ->orderBy('start_at', 'asc')
            ->get();

        return view('organizations.show', compact('organization', 'followersCount', 'upcomingEvents'));
    }
}

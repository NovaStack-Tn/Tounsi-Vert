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
        $organization->load(['category', 'socialLinks', 'events' => function ($query) {
            $query->where('is_published', true)->orderBy('start_at');
        }]);

        $followersCount = $organization->followers()->count();

        return view('organizations.show', compact('organization', 'followersCount'));
    }
}

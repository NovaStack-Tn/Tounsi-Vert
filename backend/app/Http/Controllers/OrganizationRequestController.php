<?php

namespace App\Http\Controllers;

use App\Models\OrganizationRequest;
use App\Models\OrgCategory;
use Illuminate\Http\Request;

class OrganizationRequestController extends Controller
{
    /**
     * Show the form to request becoming an organizer
     */
    public function create()
    {
        // Check if user already has a pending request
        $existingRequest = auth()->user()->organizationRequests()
            ->where('status', 'pending')
            ->first();

        if ($existingRequest) {
            return redirect()->route('dashboard')
                ->with('info', 'You already have a pending organization request.');
        }

        $categories = OrgCategory::all();
        return view('member.organization-request', compact('categories'));
    }

    /**
     * Store the organization request
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:org_categories,id',
            'organization_name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:120',
            'region' => 'required|string|max:120',
            'zipcode' => 'required|string|max:20',
            'phone_number' => 'nullable|string|max:20',
            'website' => 'nullable|url|max:255',
        ]);

        $validated['user_id'] = auth()->id();
        $validated['status'] = 'pending';

        OrganizationRequest::create($validated);

        return redirect()->route('dashboard')
            ->with('success', 'Organization request submitted successfully! We will review it soon.');
    }

    /**
     * Show user's organization requests
     */
    public function index()
    {
        $requests = auth()->user()->organizationRequests()
            ->with('category')
            ->latest()
            ->get();

        return view('member.organization-requests', compact('requests'));
    }
}

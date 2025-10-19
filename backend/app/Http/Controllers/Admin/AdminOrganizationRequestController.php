<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrganizationRequest;
use App\Models\Organization;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminOrganizationRequestController extends Controller
{
    /**
     * Display all organization requests
     */
    public function index()
    {
        $requests = OrganizationRequest::with(['user', 'category', 'reviewer'])
            ->latest()
            ->paginate(20);

        return view('admin.organization-requests.index', compact('requests'));
    }

    /**
     * Show details of a specific request
     */
    public function show(OrganizationRequest $request)
    {
        $request->load(['user', 'category', 'reviewer']);
        return view('admin.organization-requests.show', compact('request'));
    }

    /**
     * Approve organization request and create organization
     */
    public function approve(Request $request, OrganizationRequest $organizationRequest)
    {
        $validated = $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        try {
            // Create the organization
            $organization = Organization::create([
                'owner_id' => $organizationRequest->user_id,
                'org_category_id' => $organizationRequest->category_id,
                'name' => $organizationRequest->organization_name,
                'description' => $organizationRequest->description,
                'address' => $organizationRequest->address,
                'city' => $organizationRequest->city,
                'region' => $organizationRequest->region,
                'zipcode' => $organizationRequest->zipcode,
                'phone_number' => $organizationRequest->phone_number,
                'is_verified' => true, // Auto-verify when approved
            ]);

            // Change user role to organizer
            $user = User::find($organizationRequest->user_id);
            $user->role = 'organizer';
            $user->save();

            // Update request status
            $organizationRequest->update([
                'status' => 'approved',
                'admin_notes' => $validated['admin_notes'] ?? null,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);

            DB::commit();

            return redirect()->route('admin.organization-requests.index')
                ->with('success', 'Organization request approved! Organization created and user promoted to organizer.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to approve request: ' . $e->getMessage());
        }
    }

    /**
     * Reject organization request
     */
    public function reject(Request $request, OrganizationRequest $organizationRequest)
    {
        $validated = $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $organizationRequest->update([
            'status' => 'rejected',
            'admin_notes' => $validated['admin_notes'],
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
        ]);

        return redirect()->route('admin.organization-requests.index')
            ->with('success', 'Organization request rejected.');
    }
}

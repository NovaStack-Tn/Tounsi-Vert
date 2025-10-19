<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class AdminOrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $organizations = Organization::with(['owner', 'category'])
            ->withCount('events')
            ->latest()
            ->paginate(20);

        return view('admin.organizations.index', compact('organizations'));
    }

    public function verify(Organization $organization)
    {
        $organization->update(['is_verified' => true]);

        return back()->with('success', 'Organization verified successfully!');
    }

    public function unverify(Organization $organization)
    {
        $organization->update(['is_verified' => false]);

        return back()->with('success', 'Organization unverified.');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();

        return back()->with('success', 'Organization deleted successfully!');
    }
}

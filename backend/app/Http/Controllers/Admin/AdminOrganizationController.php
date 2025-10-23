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

    public function index(Request $request)
    {
        $query = Organization::with(['owner', 'category'])
            ->withCount('events')
            ->withCount('followers')
            ->withSum('donations', 'amount');

        // Apply advanced filters
        $query->filter($request->only(['search', 'category_id', 'region', 'city', 'verified']));

        // Apply sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->sort($sortField, $sortDirection);

        $organizations = $query->paginate(20)->appends($request->query());

        $categories = \App\Models\OrgCategory::orderBy('name')->get();

        return view('admin.organizations.index', compact('organizations', 'categories'));
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

    /**
     * Export organizations to CSV or PDF
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $organizations = Organization::with(['category', 'owner'])
            ->withCount('events')
            ->withCount('followers')
            ->withSum('donations', 'amount')
            ->filter($request->only(['search', 'category_id', 'region', 'city', 'verified']))
            ->get();

        if ($format === 'csv') {
            return $this->exportCsv($organizations);
        } elseif ($format === 'pdf') {
            return $this->exportPdf($organizations);
        }

        return back()->with('error', 'Invalid export format');
    }

    /**
     * Export organizations to CSV
     */
    private function exportCsv($organizations)
    {
        $csv = "ID,Name,Category,Owner,Region,City,Events,Followers,Total Donations,Verified,Status\n";
        
        foreach ($organizations as $org) {
            $csv .= sprintf(
                "%d,\"%s\",\"%s\",\"%s\",%s,%s,%d,%d,%.2f,%s,%s\n",
                $org->id,
                str_replace('"', '""', $org->name),
                $org->category->name ?? 'N/A',
                $org->owner->full_name ?? 'N/A',
                $org->region,
                $org->city,
                $org->events_count ?? 0,
                $org->followers_count ?? 0,
                $org->donations_sum_amount ?? 0,
                $org->is_verified ? 'Yes' : 'No',
                $org->is_blocked ? 'Blocked' : 'Active'
            );
        }
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="organizations-export-' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Export organizations to PDF
     */
    private function exportPdf($organizations)
    {
        // Check if DomPDF is available
        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return back()->with('error', 'PDF export requires: composer require barryvdh/laravel-dompdf');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.organizations.pdf', compact('organizations'));
        
        return $pdf->download('organizations-export-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Bulk verify organizations
     */
    public function bulkVerify(Request $request)
    {
        $request->validate([
            'organization_ids' => 'required|array',
            'organization_ids.*' => 'exists:organizations,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $count = Organization::whereIn('id', $request->organization_ids)
            ->update(['is_verified' => true]);

        return back()->with('success', "{$count} organization(s) verified successfully!");
    }

    /**
     * Bulk unverify organizations
     */
    public function bulkUnverify(Request $request)
    {
        $request->validate([
            'organization_ids' => 'required|array',
            'organization_ids.*' => 'exists:organizations,id',
            'reason' => 'nullable|string|max:500',
        ]);

        $count = Organization::whereIn('id', $request->organization_ids)
            ->update(['is_verified' => false]);

        return back()->with('success', "{$count} organization(s) unverified successfully!");
    }

    /**
     * Bulk reject (block) organizations
     */
    public function bulkReject(Request $request)
    {
        $request->validate([
            'organization_ids' => 'required|array',
            'organization_ids.*' => 'exists:organizations,id',
            'reason' => 'required|string|max:500',
        ]);

        $count = Organization::whereIn('id', $request->organization_ids)
            ->update([
                'is_blocked' => true,
                'is_verified' => false,
            ]);

        return back()->with('success', "{$count} organization(s) rejected successfully!");
    }

    /**
     * Get insights for a specific organization
     */
    public function insights(Organization $organization)
    {
        $insights = $organization->getDonationInsights();
        $completeness = $organization->profile_completeness;

        return view('admin.organizations.insights', compact('organization', 'insights', 'completeness'));
    }
}

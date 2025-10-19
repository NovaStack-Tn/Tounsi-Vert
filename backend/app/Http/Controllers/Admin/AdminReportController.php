<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = Report::with(['user', 'event', 'organization'])
            ->latest();
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $reports = $query->paginate(20)->appends(['status' => $status]);
        
        // Statistics
        $stats = [
            'total' => Report::count(),
            'open' => Report::where('status', 'open')->count(),
            'in_review' => Report::where('status', 'in_review')->count(),
            'resolved' => Report::where('status', 'resolved')->count(),
            'dismissed' => Report::where('status', 'dismissed')->count(),
        ];

        return view('admin.reports.index', compact('reports', 'status', 'stats'));
    }

    public function resolve(Report $report)
    {
        $report->update(['status' => 'resolved']);
        return back()->with('success', 'Report marked as resolved!');
    }
    
    public function dismiss(Report $report)
    {
        $report->update(['status' => 'dismissed']);
        return back()->with('success', 'Report dismissed!');
    }
    
    public function suspendOrganization(Report $report)
    {
        if ($report->organization_id) {
            $report->organization->update(['is_blocked' => true]);
            $report->update(['status' => 'resolved']);
            
            return back()->with('success', 'Organization has been suspended and report marked as resolved!');
        }
        
        return back()->with('error', 'This report is not for an organization.');
    }

    public function updateStatus(Report $report, Request $request)
    {
        $request->validate([
            'status' => 'required|in:open,in_review,resolved,dismissed',
        ]);

        $report->update(['status' => $request->status]);

        return back()->with('success', 'Report status updated successfully!');
    }
}

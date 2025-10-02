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

    public function index()
    {
        $reports = Report::with(['user', 'event', 'organization'])
            ->latest()
            ->paginate(20);

        return view('admin.reports.index', compact('reports'));
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

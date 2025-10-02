<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class AdminReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $reports = Report::with(['user', 'event', 'organization'])
            ->paginate(15);

        return view('admin.reports.index', compact('reports'));
    }

    public function updateStatus(Request $request, Report $report)
    {
        $request->validate([
            'status' => 'required|in:open,in_review,resolved,dismissed',
        ]);

        $report->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Statut du signalement mis à jour!');
    }
}

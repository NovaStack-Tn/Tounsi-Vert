<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Models\ReportAction;
use App\Services\ReportAnalysisService;
use App\Services\ReportExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class AdminReportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $analysisService = new ReportAnalysisService();
        
        // Get filter parameters
        $status = $request->get('status', 'all');
        $priority = $request->get('priority', 'all');
        $category = $request->get('category', 'all');
        
        // Use advanced search if search parameter exists
        if ($request->has('search') || $request->has('date_from') || $request->has('date_to')) {
            $filters = [
                'search' => $request->get('search'),
                'status' => $status,
                'priority' => $priority,
                'category' => $category,
                'date_from' => $request->get('date_from'),
                'date_to' => $request->get('date_to'),
                'sort_by' => $request->get('sort_by', 'created_at'),
                'sort_order' => $request->get('sort_order', 'desc'),
            ];
            
            $query = $analysisService->searchReports($filters);
        } else {
            $query = Report::with(['user', 'event', 'organization', 'latestAction'])
                ->latest();
            
            if ($status !== 'all') {
                $query->where('status', $status);
            }

            if ($priority !== 'all') {
                $query->where('priority', $priority);
            }

            if ($category !== 'all') {
                $query->where('category', $category);
            }
        }
        
        $reports = $query->paginate(20)->appends($request->all());
        
        // Statistics
        $stats = [
            'total' => Report::count(),
            'open' => Report::where('status', 'open')->count(),
            'in_review' => Report::where('status', 'in_review')->count(),
            'resolved' => Report::where('status', 'resolved')->count(),
            'dismissed' => Report::where('status', 'dismissed')->count(),
            'high_priority' => Report::where('priority', 'high')->orWhere('priority', 'critical')->count(),
            'auto_flagged' => Report::where('ai_auto_flagged', true)->count(),
            'high_risk' => Report::where('ai_risk_score', '>=', 70)->count(),
        ];

        return view('admin.reports.index', compact('reports', 'stats', 'status', 'priority', 'category'));
    }
    
    public function analytics()
    {
        $analysisService = new ReportAnalysisService();
        $analytics = $analysisService->getAdvancedStatistics();
        
        return view('admin.reports.analytics', compact('analytics'));
    }
    
    public function search(Request $request)
    {
        $analysisService = new ReportAnalysisService();
        
        $filters = $request->only([
            'search', 'status', 'priority', 'category',
            'date_from', 'date_to', 'organization_id', 'user_id',
            'sort_by', 'sort_order'
        ]);
        
        $reports = $analysisService->searchReports($filters)->paginate(20);
        
        return view('admin.reports.search', compact('reports', 'filters'));
    }

    public function show(Report $report)
    {
        $report->load(['user', 'event', 'organization', 'actions.admin', 'resolver']);
        
        return view('admin.reports.show', compact('report'));
    }

    public function addAction(Report $report, Request $request)
    {
        $request->validate([
            'action_type' => 'required|in:reviewed,investigating,resolved,dismissed,warning_sent,content_removed,account_suspended',
            'action_note' => 'nullable|string|max:1000',
            'internal_note' => 'nullable|string|max:1000',
        ]);

        ReportAction::create([
            'report_id' => $report->id,
            'admin_id' => auth()->id(),
            'action_type' => $request->action_type,
            'action_note' => $request->action_note,
            'internal_note' => $request->internal_note,
            'action_taken_at' => now(),
        ]);

        // Update report status based on action
        if (in_array($request->action_type, ['resolved', 'dismissed'])) {
            $report->update([
                'status' => $request->action_type,
                'resolved_by' => auth()->id(),
                'resolved_at' => now(),
            ]);
        } elseif ($request->action_type === 'investigating') {
            $report->update(['status' => 'in_review']);
        }

        return back()->with('success', 'Action added successfully!');
    }

    public function updateStatus(Report $report, Request $request)
    {
        $request->validate([
            'status' => 'required|in:open,in_review,resolved,dismissed',
            'priority' => 'nullable|in:low,medium,high,critical',
        ]);

        $data = ['status' => $request->status];

        if ($request->filled('priority')) {
            $data['priority'] = $request->priority;
        }

        if (in_array($request->status, ['resolved', 'dismissed'])) {
            $data['resolved_by'] = auth()->id();
            $data['resolved_at'] = now();
        }

        $report->update($data);

        return back()->with('success', 'Report updated successfully!');
    }

    public function suspendOrganization(Report $report)
    {
        if ($report->organization_id) {
            $report->organization->update(['is_blocked' => true]);
            
            // Add action
            ReportAction::create([
                'report_id' => $report->id,
                'admin_id' => auth()->id(),
                'action_type' => 'account_suspended',
                'action_note' => 'Organization has been suspended due to policy violations.',
                'internal_note' => 'Suspended via report #' . $report->id,
                'action_taken_at' => now(),
            ]);

            $report->update([
                'status' => 'resolved',
                'resolved_by' => auth()->id(),
                'resolved_at' => now(),
            ]);
            
            return back()->with('success', 'Organization has been suspended and report marked as resolved!');
        }
        
        return back()->with('error', 'This report is not for an organization.');
    }

    public function bulkAction(Request $request)
    {
        $request->validate([
            'report_ids' => 'required|array',
            'report_ids.*' => 'exists:reports,id',
            'action' => 'required|in:resolve,dismiss,mark_review',
        ]);

        $statusMap = [
            'resolve' => 'resolved',
            'dismiss' => 'dismissed',
            'mark_review' => 'in_review',
        ];

        $status = $statusMap[$request->action];

        Report::whereIn('id', $request->report_ids)->update([
            'status' => $status,
            'resolved_by' => in_array($status, ['resolved', 'dismissed']) ? auth()->id() : null,
            'resolved_at' => in_array($status, ['resolved', 'dismissed']) ? now() : null,
        ]);

        return back()->with('success', count($request->report_ids) . ' reports updated successfully!');
    }
    
    /**
     * Export reports to CSV
     */
    public function exportCSV(Request $request)
    {
        $analysisService = new ReportAnalysisService();
        $exportService = new ReportExportService();
        
        // Get filtered reports
        $filters = $request->only([
            'search', 'status', 'priority', 'category',
            'date_from', 'date_to', 'organization_id', 'user_id'
        ]);
        
        $reports = $analysisService->searchReports($filters)
            ->with(['user', 'organization', 'event', 'resolver'])
            ->get();
        
        $csv = $exportService->exportToCSV($reports);
        
        $filename = 'reports_export_' . now()->format('Y-m-d_His') . '.csv';
        
        return Response::make($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    /**
     * Export reports to Excel
     */
    public function exportExcel(Request $request)
    {
        $analysisService = new ReportAnalysisService();
        $exportService = new ReportExportService();
        
        $filters = $request->only([
            'search', 'status', 'priority', 'category',
            'date_from', 'date_to', 'organization_id', 'user_id'
        ]);
        
        $reports = $analysisService->searchReports($filters)
            ->with(['user', 'organization', 'event', 'resolver'])
            ->get();
        
        $excel = $exportService->exportToExcel($reports);
        
        $filename = 'reports_export_' . now()->format('Y-m-d_His') . '.xls';
        
        return Response::make($excel, 200, [
            'Content-Type' => 'application/vnd.ms-excel',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    /**
     * Export reports to PDF
     */
    public function exportPDF(Request $request)
    {
        $analysisService = new ReportAnalysisService();
        $exportService = new ReportExportService();
        
        $filters = $request->only([
            'search', 'status', 'priority', 'category',
            'date_from', 'date_to', 'organization_id', 'user_id'
        ]);
        
        $reports = $analysisService->searchReports($filters)
            ->with(['user', 'organization', 'event', 'resolver'])
            ->get();
        
        $stats = $exportService->generateStatisticsSummary($reports);
        $html = $exportService->generatePDFContent($reports, $stats);
        
        $filename = 'reports_export_' . now()->format('Y-m-d_His') . '.html';
        
        return Response::make($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
    
    /**
     * Export single report to PDF
     */
    public function exportSinglePDF(Report $report)
    {
        $report->load(['user', 'organization', 'event', 'actions.admin', 'resolver']);
        
        $exportService = new ReportExportService();
        $html = $exportService->generateSingleReportPDF($report);
        
        $filename = 'report_' . $report->id . '_' . now()->format('Y-m-d') . '.html';
        
        return Response::make($html, 200, [
            'Content-Type' => 'text/html',
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ]);
    }
    
    /**
     * Export reports to JSON
     */
    public function exportJSON(Request $request)
    {
        $analysisService = new ReportAnalysisService();
        $exportService = new ReportExportService();
        
        $filters = $request->only([
            'search', 'status', 'priority', 'category',
            'date_from', 'date_to', 'organization_id', 'user_id'
        ]);
        
        $reports = $analysisService->searchReports($filters)
            ->with(['user', 'organization', 'event', 'resolver', 'actions'])
            ->get();
        
        $json = $exportService->exportToJSON($reports);
        
        $filename = 'reports_export_' . now()->format('Y-m-d_His') . '.json';
        
        return Response::make($json, 200, [
            'Content-Type' => 'application/json',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
    
    /**
     * Advanced analytics dashboard
     */
    public function advancedAnalytics(Request $request)
    {
        $analysisService = new ReportAnalysisService();
        $exportService = new ReportExportService();
        
        $filters = $request->only([
            'date_from', 'date_to', 'status', 'priority', 'category'
        ]);
        
        $reports = $analysisService->searchReports($filters)
            ->with(['user', 'organization', 'event'])
            ->get();
        
        $analytics = $exportService->generateAnalyticsReport($reports);
        
        return view('admin.reports.advanced-analytics', compact('analytics', 'filters'));
    }
}

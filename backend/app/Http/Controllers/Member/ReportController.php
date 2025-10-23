<?php

namespace App\Http\Controllers\Member;

use App\Models\Event;
use App\Http\Controllers\Controller;
use App\Models\Report;
use App\Services\ReportAnalysisService;
use Illuminate\Http\Request;




class ReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        
        $query = Report::where('user_id', auth()->id())
            ->with(['event', 'organization'])
            ->latest();
        
        if ($status !== 'all') {
            $query->where('status', $status);
        }
        
        $reports = $query->paginate(10)->appends(['status' => $status]);
        
        return view('member.reports.index', compact('reports', 'status'));
    }
    
    public function create(Request $request)
    {
        $organizationId = $request->get('organization_id');
        $eventId = $request->get('event_id');
        
        return view('member.reports.create', compact('organizationId', 'eventId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'event_id' => 'nullable|exists:events,id',
            'organization_id' => 'nullable|exists:organizations,id',
            'reason' => 'required|string|max:200',
            'details' => 'nullable|string|max:2000',
            'category' => 'required|in:spam,inappropriate,fraud,harassment,violence,misinformation,copyright,other',
            'priority' => 'nullable|in:low,medium,high,critical',
        ]);

        // AI Analysis
        $analysisService = new ReportAnalysisService();
        $aiAnalysis = $analysisService->analyzeReportContent(
            $request->reason,
            $request->details ?? ''
        );

        // Use AI suggested priority if user didn't specify
        $priority = $request->priority ?? $aiAnalysis['priority'];

        Report::create([
            'user_id' => auth()->id(),
            'event_id' => $request->event_id,
            'organization_id' => $request->organization_id,
            'reason' => $request->reason,
            'details' => $request->details,
            'category' => $request->category,
            'priority' => $priority,
            'status' => 'open',
            'ai_risk_score' => $aiAnalysis['risk_score'],
            'ai_suggested_category' => $aiAnalysis['suggested_category'],
            'ai_confidence' => $aiAnalysis['confidence'],
            'ai_auto_flagged' => $aiAnalysis['auto_flag'],
            'ai_analysis' => $aiAnalysis,
        ]);

        $message = 'Thank you for your report! We will review it soon.';
        
        if ($aiAnalysis['requires_immediate_attention']) {
            $message = 'Your report has been flagged for immediate attention. Our team will review it as soon as possible.';
        }

        return redirect()->route('member.reports.index')
            ->with('success', $message);
    }
}

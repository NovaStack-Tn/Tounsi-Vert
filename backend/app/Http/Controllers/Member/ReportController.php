<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Report;
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
        ]);

        Report::create([
            'user_id' => auth()->id(),
            'event_id' => $request->event_id,
            'organization_id' => $request->organization_id,
            'reason' => $request->reason,
            'details' => $request->details,
            'status' => 'pending',
        ]);

        return redirect()->route('member.reports.index')
            ->with('success', 'Thank you for your report! We will contact you soon.');
    }
}

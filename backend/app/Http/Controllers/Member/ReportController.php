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
            'status' => 'open',
        ]);

        return back()->with('success', 'Report submitted successfully. We will review it soon.');
    }
}

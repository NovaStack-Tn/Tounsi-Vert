<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_organizations' => Organization::count(),
            'verified_organizations' => Organization::where('is_verified', true)->count(),
            'total_events' => Event::count(),
            'upcoming_events' => Event::where('start_at', '>', now())->count(),
            'pending_reports' => Report::where('status', 'open')->count(),
        ];

        $recentReports = Report::with(['user', 'event', 'organization'])
            ->where('status', 'open')
            ->latest()
            ->take(5)
            ->get();

        $pendingOrganizations = Organization::where('is_verified', false)
            ->with('owner', 'category')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentReports', 'pendingOrganizations'));
    }
}

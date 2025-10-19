<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Event;
use App\Models\Organization;
use App\Models\Report;
use App\Models\Donation;

class AdminDashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_organizations' => Organization::count(),
            'verified_organizations' => Organization::where('is_verified', true)->count(),
            'total_events' => Event::count(),
            'published_events' => Event::where('is_published', true)->count(),
            'open_reports' => Report::where('status', 'open')->count(),
            'total_donations' => Donation::where('status', 'succeeded')->sum('amount'),
        ];

        $recentReports = Report::with(['user', 'event', 'organization'])
            ->latest()
            ->take(10)
            ->get();

        $pendingOrganizations = Organization::where('is_verified', false)
            ->with('owner')
            ->latest()
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentReports', 'pendingOrganizations'));
    }
}

<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Donation;
use App\Models\Organization;
use App\Models\Participation;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\UpdateDonationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class  DonationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of donations with filtering
     */
    public function index(Request $request)
    {
        $query = Donation::with(['organization', 'event', 'participation.user']);

        // Apply filters
        if ($request->filled('organization_id')) {
            $query->byOrganization($request->organization_id);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->dateRange($request->start_date, $request->end_date);
        }

        // Filter by authenticated user's donations
        if ($request->has('my_donations')) {
            $query->byUser(auth()->id());
        }

        $donations = $query->latest()->paginate(15);

        // Calculate statistics
        $totalDonations = $query->sum('amount');
        $totalCount = $query->count();
        
        // Get donations grouped by organization
        $donationsByOrganization = Donation::with('organization')
            ->select('organization_id', DB::raw('SUM(amount) as total_amount'), DB::raw('COUNT(*) as donation_count'))
            ->when($request->has('my_donations'), function ($q) {
                $q->byUser(auth()->id());
            })
            ->groupBy('organization_id')
            ->get();

        $organizations = Organization::where('is_verified', true)->get();
        $statuses = ['pending', 'succeeded', 'failed', 'refunded'];

        return view('member.donations.index', compact(
            'donations',
            'organizations',
            'statuses',
            'totalDonations',
            'totalCount',
            'donationsByOrganization'
        ));
    }

    /**
     * Show donation statistics
     */
    public function statistics(Request $request)
    {
        $userId = auth()->id();

        // User's donations statistics
        $userStats = [
            'total_amount' => Donation::byUser($userId)->sum('amount'),
            'total_count' => Donation::byUser($userId)->count(),
            'succeeded' => Donation::byUser($userId)->byStatus('succeeded')->sum('amount'),
            'pending' => Donation::byUser($userId)->byStatus('pending')->sum('amount'),
        ];

        // Donations by organization
        $donationsByOrganization = Donation::with('organization')
            ->byUser($userId)
            ->select('organization_id', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
            ->groupBy('organization_id')
            ->get();

        // Monthly donations data for chart
        $monthlyDonations = Donation::byUser($userId)
            ->selectRaw('MONTH(created_at) as month, SUM(amount) as total')
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Recent donations
        $recentDonations = Donation::with(['organization', 'event'])
            ->byUser($userId)
            ->latest()
            ->limit(10)
            ->get();

        return view('member.donations.statistics', compact(
            'userStats',
            'donationsByOrganization',
            'monthlyDonations',
            'recentDonations'
        ));
    }

    /**
     * Show the form for creating a new donation
     */
    public function create(Request $request)
    {
        // If coming from event donation link
        $eventId = $request->query('event_id');
        $event = $eventId ? Event::with('organization')->find($eventId) : null;

        // Only load organizations and events if NOT donating to a specific event
        $organizations = $event ? collect() : Organization::where('is_verified', true)->get();
        $events = $event ? collect() : Event::where('is_published', true)->get();

        return view('member.donations.create', compact('organizations', 'events', 'event'));
    }

    /**
     * Store a newly created donation
     */
    public function store(StoreDonationRequest $request)
    {
        try {
            $eventId = null;
            
            DB::transaction(function () use ($request, &$eventId) {
                // Store event ID for redirect
                $eventId = $request->event_id;
                
                // Create participation
                $participation = Participation::create([
                    'user_id' => auth()->id(),
                    'event_id' => $request->event_id,
                    'type' => 'donation',
                ]);

                // Create donation
                $donation = Donation::create([
                    'participation_id' => $participation->id,
                    'organization_id' => $request->organization_id,
                    'event_id' => $request->event_id,
                    'amount' => $request->amount,
                    'status' => 'pending',
                ]);

                // Simulate payment processing (replace with real payment gateway)
                $donation->update([
                    'status' => 'succeeded',
                    'paid_at' => now(),
                    'payment_ref' => 'DON-' . strtoupper(uniqid()),
                ]);

                // Update user score
                auth()->user()->increment('score', floor($request->amount));
            });

            // Redirect back to event if donating to a specific event, otherwise to donations index
            if ($eventId) {
                return redirect()->route('events.show', $eventId)
                    ->with('success', 'Thank you for your donation! Your support makes a real difference.');
            }
            
            return redirect()->route('donations.index')
                ->with('success', 'Donation created successfully!');
                
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'An error occurred while processing your donation.');
        }
    }

    /**
     * Display the specified donation
     */
    public function show(Donation $donation)
    {
        $donation->load(['organization', 'event', 'participation.user']);

        // Ensure user can only view their own donations
        if ($donation->participation->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this donation.');
        }

        return view('member.donations.show', compact('donation'));
    }

    /**
     * Show the form for editing the specified donation
     */
    public function edit(Donation $donation)
    {
        $donation->load('participation');

        // Ensure user can only edit their own donations
        if ($donation->participation->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this donation.');
        }

        // Only allow editing pending donations
        if ($donation->status !== 'pending') {
            return redirect()->route('donations.show', $donation)
                ->with('error', 'Only pending donations can be edited.');
        }

        $organizations = Organization::where('is_verified', true)->get();
        $events = Event::where('is_published', true)->get();

        return view('member.donations.edit', compact('donation', 'organizations', 'events'));
    }

    /**
     * Update the specified donation
     */
    public function update(UpdateDonationRequest $request, Donation $donation)
    {
        $donation->load('participation');

        // Ensure user can only update their own donations
        if ($donation->participation->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this donation.');
        }

        // Only allow updating pending donations
        if ($donation->status !== 'pending') {
            return redirect()->route('donations.show', $donation)
                ->with('error', 'Only pending donations can be updated.');
        }

        try {
            $donation->update($request->validated());

            return redirect()->route('donations.show', $donation)
                ->with('success', 'Donation updated successfully!');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'An error occurred while updating the donation.');
        }
    }

    /**
     * Remove the specified donation
     */
    public function destroy(Donation $donation)
    {
        $donation->load('participation');

        // Ensure user can only delete their own donations
        if ($donation->participation->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this donation.');
        }

        // Only allow deleting pending or failed donations
        if (!in_array($donation->status, ['pending', 'failed'])) {
            return redirect()->route('donations.index')
                ->with('error', 'Only pending or failed donations can be deleted.');
        }

        try {
            DB::transaction(function () use ($donation) {
                $participation = $donation->participation;
                $donation->delete();
                
                // Also delete the participation if it was only for this donation
                if ($participation && $participation->type === 'donation') {
                    $participation->delete();
                }
            });

            return redirect()->route('donations.index')
                ->with('success', 'Donation deleted successfully!');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while deleting the donation.');
        }
    }

    /**
     * Export donations to PDF
     */
    public function exportPdf(Request $request)
    {
        // Check if DomPDF is available
        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return back()->with('error', 'PDF export is not available. Please install: composer require barryvdh/laravel-dompdf');
        }

        try {
            $query = Donation::with(['organization', 'event', 'participation.user']);

            // Apply same filters as index
            if ($request->filled('organization_id')) {
                $query->byOrganization($request->organization_id);
            }

            if ($request->filled('status')) {
                $query->byStatus($request->status);
            }

            if ($request->has('my_donations')) {
                $query->byUser(auth()->id());
            }

            $donations = $query->latest()->get();
            $totalAmount = $donations->sum('amount');

            $pdf = Pdf::loadView('member.donations.pdf', compact('donations', 'totalAmount'));
            
            return $pdf->download('donations-' . date('Y-m-d') . '.pdf');
        } catch (\Exception $e) {
            return back()->with('error', 'PDF export failed: ' . $e->getMessage());
        }
    }

    /**
     * Export donations to CSV
     */
    public function exportCsv(Request $request)
    {
        $query = Donation::with(['organization', 'event', 'participation.user']);

        // Apply same filters as index
        if ($request->filled('organization_id')) {
            $query->byOrganization($request->organization_id);
        }

        if ($request->filled('status')) {
            $query->byStatus($request->status);
        }

        if ($request->has('my_donations')) {
            $query->byUser(auth()->id());
        }

        $donations = $query->latest()->get();

        $filename = 'donations-' . date('Y-m-d') . '.csv';
        $handle = fopen('php://output', 'w');

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        // Add CSV headers
        fputcsv($handle, ['ID', 'User', 'Organization', 'Event', 'Amount', 'Status', 'Payment Ref', 'Date']);

        // Add data
        foreach ($donations as $donation) {
            fputcsv($handle, [
                $donation->id,
                $donation->participation->user->name ?? 'N/A',
                $donation->organization->name ?? 'N/A',
                $donation->event->title ?? 'N/A',
                $donation->amount,
                $donation->status,
                $donation->payment_ref ?? 'N/A',
                $donation->created_at->format('Y-m-d H:i:s'),
            ]);
        }

        fclose($handle);
        exit;
    }

    /**
     * Legacy method for event donations
     */
    public function createForEvent(Event $event)
    {
        // Load organization relationship
        $event->load('organization');
        
        // Empty collections since we're donating to a specific event
        $organizations = collect();
        $events = collect();
        
        return view('member.donations.create', compact('event', 'organizations', 'events'));
    }

    /**
     * Legacy method for storing event donations
     */
    public function storeForEvent(Request $request, Event $event)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1|max:10000',
        ]);

        try {
            DB::transaction(function () use ($request, $event) {
                // Create participation
                $participation = Participation::create([
                    'user_id' => auth()->id(),
                    'event_id' => $event->id,
                    'type' => 'donation',
                ]);

                // Create donation
                $donation = Donation::create([
                    'participation_id' => $participation->id,
                    'organization_id' => $event->organization_id,
                    'event_id' => $event->id,
                    'amount' => $request->amount,
                    'status' => 'pending',
                ]);

                // Mark as succeeded
                $donation->update([
                    'status' => 'succeeded',
                    'paid_at' => now(),
                    'payment_ref' => 'DON-' . strtoupper(uniqid()),
                ]);

                // Update user score
                auth()->user()->increment('score', floor($request->amount));
            });

            return redirect()->route('events.show', $event)->with('success', 'Thank you for your donation!');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while processing your donation.');
        }
    }
}

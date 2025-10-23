<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminEventController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'admin']);
    }

    public function index(Request $request)
    {
        $organizationFilter = $request->get('organization_id', 'all');
        
        $query = Event::with(['organization', 'category'])
            ->withCount(['participations as attendees_count' => function ($q) {
                $q->where('type', 'attend');
            }])
            ->withCount('reviews')
            ->withAvg('reviews', 'rate')
            ->withSum('donations', 'amount');
        
        // Apply advanced filters
        $query->filter($request->only(['search', 'category_id', 'type', 'region', 'city', 'start_date', 'end_date', 'status', 'published']));
        
        if ($organizationFilter !== 'all') {
            $query->where('organization_id', $organizationFilter);
        }
        
        // Apply sorting
        $sortField = $request->get('sort', 'created_at');
        $sortDirection = $request->get('direction', 'desc');
        $query->sort($sortField, $sortDirection);
        
        $events = $query->paginate(20)->appends($request->query());
        
        $organizations = Organization::orderBy('name')->get();
        $categories = \App\Models\EventCategory::orderBy('name')->get();
        
        // Statistics
        $stats = [
            'total_events' => Event::count(),
            'published_events' => Event::where('is_published', true)->count(),
            'draft_events' => Event::where('is_published', false)->count(),
            'total_attendees' => DB::table('participations')->where('type', 'attend')->count(),
            'total_donations' => DB::table('donations')->where('status', 'succeeded')->sum('amount'),
        ];

        return view('admin.events.index', compact('events', 'organizations', 'categories', 'organizationFilter', 'stats'));
    }

    public function show(Event $event)
    {
        $event->load([
            'organization',
            'category',
            'reviews.user',
            'participations' => function ($q) {
                $q->where('type', 'attend')->with('user');
            },
            'donations' => function ($q) {
                $q->where('status', 'succeeded')->with('participation.user');
            }
        ]);

        $stats = [
            'total_attendees' => $event->participations->where('type', 'attend')->count(),
            'total_reviews' => $event->reviews->count(),
            'average_rating' => $event->reviews->avg('rate'),
            'total_donations' => $event->donations->where('status', 'succeeded')->sum('amount'),
            'total_donors' => $event->donations->where('status', 'succeeded')->unique('participation_id')->count(),
        ];

        return view('admin.events.show', compact('event', 'stats'));
    }

    public function destroy(Event $event)
    {
        $eventTitle = $event->title;
        $event->delete();
        
        return redirect()->route('admin.events.index')
            ->with('success', "Event '{$eventTitle}' has been deleted successfully!");
    }

    public function leaderboard()
    {
        // Top events by average rating
        $topRatedEvents = Event::withAvg('reviews', 'rate')
            ->withCount('reviews')
            ->having('reviews_count', '>', 0)
            ->orderByDesc('reviews_avg_rate')
            ->limit(10)
            ->get();

        // Top events by total attendees
        $topAttendedEvents = Event::withCount(['participations as attendees_count' => function ($q) {
                $q->where('type', 'attend');
            }])
            ->having('attendees_count', '>', 0)
            ->orderByDesc('attendees_count')
            ->limit(10)
            ->get();

        // Top events by donations
        $topDonatedEvents = Event::withSum('donations', 'amount')
            ->having('donations_sum_amount', '>', 0)
            ->orderByDesc('donations_sum_amount')
            ->limit(10)
            ->get();

        // Top organizations by events
        $topOrganizations = Organization::withCount('events')
            ->having('events_count', '>', 0)
            ->orderByDesc('events_count')
            ->limit(10)
            ->get();

        return view('admin.events.leaderboard', compact(
            'topRatedEvents',
            'topAttendedEvents',
            'topDonatedEvents',
            'topOrganizations'
        ));
    }

    /**
     * Export events to CSV or PDF
     */
    public function export(Request $request)
    {
        $format = $request->get('format', 'csv');
        
        $events = Event::with(['category', 'organization'])
            ->withCount(['participations as attendees_count' => function ($q) {
                $q->where('type', 'attend');
            }])
            ->filter($request->only(['search', 'category_id', 'type', 'region', 'city', 'start_date', 'end_date', 'status']))
            ->get();

        if ($format === 'csv') {
            return $this->exportCsv($events);
        } elseif ($format === 'pdf') {
            return $this->exportPdf($events);
        }

        return back()->with('error', 'Invalid export format');
    }

    /**
     * Export events to CSV
     */
    private function exportCsv($events)
    {
        $csv = "ID,Title,Category,Type,Organization,Region,City,Start Date,End Date,Attendees,Status\n";
        
        foreach ($events as $event) {
            $csv .= sprintf(
                "%d,\"%s\",\"%s\",%s,\"%s\",%s,%s,%s,%s,%d,%s\n",
                $event->id,
                str_replace('"', '""', $event->title),
                $event->category->name ?? 'N/A',
                $event->type,
                str_replace('"', '""', $event->organization->name ?? 'N/A'),
                $event->region,
                $event->city,
                $event->start_at->format('Y-m-d H:i'),
                $event->end_at->format('Y-m-d H:i'),
                $event->attendees_count ?? 0,
                $event->is_published ? 'Published' : 'Draft'
            );
        }
        
        return response($csv, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="events-export-' . date('Y-m-d') . '.csv"',
        ]);
    }

    /**
     * Export events to PDF
     */
    private function exportPdf($events)
    {
        // Check if DomPDF is available
        if (!class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
            return back()->with('error', 'PDF export requires: composer require barryvdh/laravel-dompdf');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.events.pdf', compact('events'));
        
        return $pdf->download('events-export-' . date('Y-m-d') . '.pdf');
    }

    /**
     * Export single event to ICS calendar file
     */
    public function exportIcs(Event $event)
    {
        $ics = "BEGIN:VCALENDAR\n";
        $ics .= "VERSION:2.0\n";
        $ics .= "PRODID:-//Tounsi-Vert//Events//EN\n";
        $ics .= "CALSCALE:GREGORIAN\n";
        $ics .= "METHOD:PUBLISH\n";
        $ics .= "BEGIN:VEVENT\n";
        $ics .= "UID:" . $event->id . "@tounsivert.tn\n";
        $ics .= "DTSTAMP:" . now()->format('Ymd\THis\Z') . "\n";
        $ics .= "DTSTART:" . $event->start_at->format('Ymd\THis\Z') . "\n";
        $ics .= "DTEND:" . $event->end_at->format('Ymd\THis\Z') . "\n";
        $ics .= "SUMMARY:" . $this->escapeIcsText($event->title) . "\n";
        $ics .= "DESCRIPTION:" . $this->escapeIcsText(strip_tags($event->description)) . "\n";
        
        if ($event->address) {
            $location = $event->address;
            if ($event->city) $location .= ", " . $event->city;
            if ($event->region) $location .= ", " . $event->region;
            $ics .= "LOCATION:" . $this->escapeIcsText($location) . "\n";
        }
        
        if ($event->meeting_url) {
            $ics .= "URL:" . $event->meeting_url . "\n";
        }
        
        $ics .= "STATUS:CONFIRMED\n";
        $ics .= "SEQUENCE:0\n";
        $ics .= "END:VEVENT\n";
        $ics .= "END:VCALENDAR\n";
        
        return response($ics, 200, [
            'Content-Type' => 'text/calendar; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="event-' . $event->id . '.ics"',
        ]);
    }

    /**
     * Escape text for ICS format
     */
    private function escapeIcsText($text)
    {
        $text = str_replace(["\r\n", "\n", "\r"], "\\n", $text);
        $text = str_replace([",", ";"], ["\\,", "\\;"], $text);
        return $text;
    }
}

<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class OrganizerEventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        // Get the user's single organization
        $organization = auth()->user()->organizationsOwned()->first();
        
        // If no organization, redirect to create one
        if (!$organization) {
            return redirect()->route('organizer.organizations.create')
                ->with('info', 'Please create your organization first before creating events.');
        }
        
        // Get all events for the user's organization
        $events = Event::where('organization_id', $organization->id)
            ->with(['category', 'organization', 'participations'])
            ->latest()
            ->paginate(15);

        return view('organizer.events.index', compact('events', 'organization'));
    }

    public function create()
    {
        // Get the user's single organization
        $organization = auth()->user()->organizationsOwned()->first();
        
        // If no organization, redirect to create one
        if (!$organization) {
            return redirect()->route('organizer.organizations.create')
                ->with('info', 'Please create your organization first before creating events.');
        }
        
        $categories = EventCategory::all();

        return view('organizer.events.create', compact('organization', 'categories'));
    }

    public function store(Request $request)
    {
        // Get the user's organization
        $organization = auth()->user()->organizationsOwned()->firstOrFail();
        
        $request->validate([
            'event_category_id' => 'required|exists:event_categories,id',
            'type' => 'required|in:online,onsite,hybrid',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'start_at' => 'required|date|after:now',
            'end_at' => 'nullable|date|after:start_at',
            'max_participants' => 'nullable|integer|min:1',
            'meeting_url' => 'nullable|url',
            'address' => 'nullable|string',
            'region' => 'nullable|string|max:120',
            'city' => 'nullable|string|max:120',
            'zipcode' => 'nullable|string|max:20',
            'poster_path' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('poster_path');
        $data['organization_id'] = $organization->id;

        if ($request->hasFile('poster_path')) {
            $data['poster_path'] = $request->file('poster_path')->store('posters', 'public');
        }

        $event = Event::create($data);

        return redirect()->route('organizer.events.show', $event)->with('success', 'Event created successfully!');
    }

    public function show(Event $event)
    {
        $this->authorize('view', $event);
        
        $event->load(['category', 'participations.user', 'reviews']);
        $attendees = $event->attendees()->with('user')->get();
        $donations = $event->donations()->where('status', 'succeeded')->with('participation.user')->get();

        return view('organizer.events.show', compact('event', 'attendees', 'donations'));
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);
        
        $organization = auth()->user()->organizationsOwned()->first();
        $categories = EventCategory::all();

        return view('organizer.events.edit', compact('event', 'organization', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $request->validate([
            'event_category_id' => 'required|exists:event_categories,id',
            'type' => 'required|in:online,onsite,hybrid',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after:start_at',
            'max_participants' => 'nullable|integer|min:1',
            'meeting_url' => 'nullable|url',
            'address' => 'nullable|string',
            'region' => 'nullable|string|max:120',
            'city' => 'nullable|string|max:120',
            'zipcode' => 'nullable|string|max:20',
            'poster_path' => 'nullable|image|max:2048',
        ]);

        $data = $request->except('poster_path');

        if ($request->hasFile('poster_path')) {
            $data['poster_path'] = $request->file('poster_path')->store('posters', 'public');
        }

        $event->update($data);

        return redirect()->route('organizer.events.show', $event)->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        
        $event->delete();

        return redirect()->route('organizer.events.index')->with('success', 'Event deleted successfully!');
    }
}

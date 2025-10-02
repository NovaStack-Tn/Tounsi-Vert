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
        $organizations = auth()->user()->organizationsOwned;
        $events = Event::whereIn('organization_id', $organizations->pluck('id'))
            ->with('category')
            ->latest()
            ->paginate(15);

        return view('organizer.events.index', compact('events'));
    }

    public function create()
    {
        $organizations = auth()->user()->organizationsOwned;
        $categories = EventCategory::all();

        return view('organizer.events.create', compact('organizations', 'categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
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

        // Check ownership
        $organization = auth()->user()->organizationsOwned()->findOrFail($request->organization_id);

        $data = $request->except('poster_path');

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
        
        $organizations = auth()->user()->organizationsOwned;
        $categories = EventCategory::all();

        return view('organizer.events.edit', compact('event', 'organizations', 'categories'));
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

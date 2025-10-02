<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\Organization;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrganizerEventController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $events = Event::whereHas('organization', function ($query) {
            $query->where('owner_id', Auth::id());
        })->with('category', 'organization')->paginate(10);

        return view('organizer.events.index', compact('events'));
    }

    public function create()
    {
        $organizations = Organization::where('owner_id', Auth::id())->get();
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
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after:start_at',
            'max_participants' => 'nullable|integer|min:1',
            'meeting_url' => 'nullable|url',
            'address' => 'nullable|string',
            'region' => 'nullable|string',
            'city' => 'nullable|string',
            'zipcode' => 'nullable|string',
            'poster_path' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('poster_path')) {
            $data['poster_path'] = $request->file('poster_path')->store('posters', 'public');
        }

        Event::create($data);

        return redirect()->route('organizer.events.index')->with('success', 'Événement créé avec succès!');
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        $organizations = Organization::where('owner_id', Auth::id())->get();
        $categories = EventCategory::all();

        return view('organizer.events.edit', compact('event', 'organizations', 'categories'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $request->validate([
            'organization_id' => 'required|exists:organizations,id',
            'event_category_id' => 'required|exists:event_categories,id',
            'type' => 'required|in:online,onsite,hybrid',
            'title' => 'required|string|max:150',
            'description' => 'nullable|string',
            'start_at' => 'required|date',
            'end_at' => 'nullable|date|after:start_at',
            'max_participants' => 'nullable|integer|min:1',
            'meeting_url' => 'nullable|url',
            'address' => 'nullable|string',
            'region' => 'nullable|string',
            'city' => 'nullable|string',
            'zipcode' => 'nullable|string',
            'poster_path' => 'nullable|image|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('poster_path')) {
            $data['poster_path'] = $request->file('poster_path')->store('posters', 'public');
        }

        $event->update($data);

        return redirect()->route('organizer.events.index')->with('success', 'Événement mis à jour avec succès!');
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);
        $event->delete();

        return redirect()->route('organizer.events.index')->with('success', 'Événement supprimé avec succès!');
    }
}

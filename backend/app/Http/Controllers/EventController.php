<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::query()->where('is_published', true);

        if ($request->filled('category')) {
            $query->where('event_category_id', $request->category);
        }

        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('date')) {
            $query->whereDate('start_at', $request->date);
        }

        $events = $query->with(['organization', 'category'])
            ->orderBy('start_at', 'asc')
            ->paginate(12);

        $categories = EventCategory::all();

        return view('events.index', compact('events', 'categories'));
    }

    public function show(Event $event)
    {
        $event->load(['organization', 'category', 'reviews.user', 'participations']);
        
        $attendeesCount = $event->participations()->where('type', 'attend')->count();
        $followersCount = $event->participations()->where('type', 'follow')->count();
        $averageRating = $event->reviews()->avg('rate');

        return view('events.show', compact('event', 'attendeesCount', 'followersCount', 'averageRating'));
    }
}

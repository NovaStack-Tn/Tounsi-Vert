<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with(['organization', 'category'])
            ->where('is_published', true);

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

        $events = $query->orderBy('start_at')->paginate(12);
        $categories = EventCategory::all();

        return view('events.index', compact('events', 'categories'));
    }

    public function show(Event $event)
    {
        $event->load(['organization.category', 'category', 'reviews.user', 'participations']);
        
        $attendeesCount = $event->attendees()->count();
        $averageRating = $event->reviews()->avg('rate') ?? 0;
        
        // Check if user has joined
        $hasJoined = false;
        $userParticipation = null;
        if (auth()->check()) {
            $userParticipation = $event->participations()
                ->where('user_id', auth()->id())
                ->where('type', 'attend')
                ->first();
            $hasJoined = $userParticipation !== null;
        }
        
        // Check if user has already reviewed
        $hasReviewed = false;
        $userReview = null;
        if (auth()->check()) {
            $userReview = $event->reviews()->where('user_id', auth()->id())->first();
            $hasReviewed = $userReview !== null;
        }
        
        // Get reviews with pagination
        $reviews = $event->reviews()
            ->with('user')
            ->latest()
            ->paginate(5);

        return view('events.show', compact('event', 'attendeesCount', 'averageRating', 'hasJoined', 'hasReviewed', 'userReview', 'reviews'));
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EventCategory;
use Illuminate\Http\Request;

class AdminEventCategoryController extends Controller
{
    /**
     * Display a listing of event categories
     */
    public function index()
    {
        $categories = EventCategory::withCount('events')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.categories.events.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        return view('admin.categories.events.create');
    }

    /**
     * Store a newly created category
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120|unique:event_categories,name',
        ]);

        EventCategory::create($validated);

        return redirect()->route('admin.event-categories.index')
            ->with('success', 'Event category created successfully.');
    }

    /**
     * Show the form for editing the specified category
     */
    public function edit(EventCategory $eventCategory)
    {
        $eventsCount = $eventCategory->events()->count();
        
        return view('admin.categories.events.edit', compact('eventCategory', 'eventsCount'));
    }

    /**
     * Update the specified category
     */
    public function update(Request $request, EventCategory $eventCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:120|unique:event_categories,name,' . $eventCategory->id,
        ]);

        $eventCategory->update($validated);

        return redirect()->route('admin.event-categories.index')
            ->with('success', 'Event category updated successfully.');
    }

    /**
     * Remove the specified category
     */
    public function destroy(EventCategory $eventCategory)
    {
        // Check if category has events
        if ($eventCategory->events()->count() > 0) {
            return redirect()->route('admin.event-categories.index')
                ->with('error', 'Cannot delete category with existing events. Please reassign or delete events first.');
        }

        $eventCategory->delete();

        return redirect()->route('admin.event-categories.index')
            ->with('success', 'Event category deleted successfully.');
    }
}

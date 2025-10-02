@extends('layouts.public')

@section('title', 'My Events')

@section('content')
<div class="bg-primary-custom text-white py-4">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="bi bi-calendar-event"></i> My Events</h1>
                <p class="mb-0">Manage and track all your events</p>
            </div>
            <a href="{{ route('organizer.events.create') }}" class="btn btn-light">
                <i class="bi bi-plus-circle me-1"></i>Create Event
            </a>
        </div>
    </div>
</div>

<div class="container py-5">
    <!-- Filter Tabs -->
    <ul class="nav nav-tabs mb-4" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#all">
                <i class="bi bi-list"></i> All Events ({{ $events->total() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#published">
                <i class="bi bi-check-circle"></i> Published ({{ $events->where('is_published', true)->count() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#draft">
                <i class="bi bi-pencil-square"></i> Drafts ({{ $events->where('is_published', false)->count() }})
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#upcoming">
                <i class="bi bi-calendar-check"></i> Upcoming
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#past">
                <i class="bi bi-calendar-x"></i> Past
            </a>
        </li>
    </ul>

    @if($events->count() > 0)
        <div class="tab-content">
            <!-- All Events -->
            <div class="tab-pane fade show active" id="all">
                <div class="row">
                    @foreach($events as $event)
                        @include('organizer.events._event-card')
                    @endforeach
                </div>
            </div>

            <!-- Published -->
            <div class="tab-pane fade" id="published">
                <div class="row">
                    @foreach($events->where('is_published', true) as $event)
                        @include('organizer.events._event-card')
                    @endforeach
                </div>
            </div>

            <!-- Draft -->
            <div class="tab-pane fade" id="draft">
                <div class="row">
                    @foreach($events->where('is_published', false) as $event)
                        @include('organizer.events._event-card')
                    @endforeach
                </div>
            </div>

            <!-- Upcoming -->
            <div class="tab-pane fade" id="upcoming">
                <div class="row">
                    @foreach($events->where('start_at', '>', now()) as $event)
                        @include('organizer.events._event-card')
                    @endforeach
                </div>
            </div>

            <!-- Past -->
            <div class="tab-pane fade" id="past">
                <div class="row">
                    @foreach($events->where('start_at', '<', now()) as $event)
                        @include('organizer.events._event-card')
                    @endforeach
                </div>
            </div>
        </div>

        <div class="mt-4">
            {{ $events->links() }}
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-calendar-event" style="font-size: 5rem; color: #ddd;"></i>
            <h3 class="mt-3">No Events Yet</h3>
            <p class="text-muted">Create your first event to start engaging with the community</p>
            <a href="{{ route('organizer.events.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Create Your First Event
            </a>
        </div>
    @endif
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Events Management')

@section('content')
<style>
    .event-card {
        transition: all 0.3s ease;
        border-left: 4px solid #28a745;
        cursor: pointer;
    }
    .event-card:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.15);
        transform: translateX(5px);
    }
    .stat-badge {
        font-size: 0.85rem;
        padding: 0.4rem 0.8rem;
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-calendar-event-fill text-success me-2"></i>Events Management</h2>
            <p class="text-muted">Manage all events and view statistics</p>
        </div>
        <a href="{{ route('admin.events.leaderboard') }}" class="btn btn-primary">
            <i class="bi bi-trophy-fill me-2"></i>View Leaderboard
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <h3 class="mb-0">{{ $stats['total_events'] }}</h3>
                    <small>Total Events</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="card-body text-white">
                    <h3 class="mb-0">{{ $stats['published_events'] }}</h3>
                    <small>Published</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                <div class="card-body text-white">
                    <h3 class="mb-0">{{ $stats['total_attendees'] }}</h3>
                    <small>Total Attendees</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                <div class="card-body text-white">
                    <h3 class="mb-0">${{ number_format($stats['total_donations'], 2) }}</h3>
                    <small>Total Donations</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Advanced Filters Component -->
    @include('admin.events._filters')

    <!-- Events List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 p-4">
            <h5 class="mb-0"><i class="bi bi-list me-2"></i>All Events ({{ $events->total() }})</h5>
        </div>
        <div class="card-body p-4">
            @forelse($events as $event)
                <div class="event-card bg-light p-4 rounded mb-3" onclick="window.location='{{ route('admin.events.show', $event) }}'">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                @if($event->image_path)
                                    <img src="{{ Storage::url($event->image_path) }}" alt="{{ $event->title }}" class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                @else
                                    <div class="bg-success text-white rounded me-3 d-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                        <i class="bi bi-calendar-event" style="font-size: 2rem;"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="mb-2">{{ $event->title }}</h6>
                                    <p class="mb-2 text-muted small">
                                        <i class="bi bi-building me-1"></i>{{ $event->organization->name }}
                                    </p>
                                    <p class="mb-0 text-muted small">
                                        <i class="bi bi-tag me-1"></i>{{ $event->category->name }}
                                        <span class="ms-3"><i class="bi bi-calendar me-1"></i>{{ $event->start_at->format('M d, Y') }}</span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-center">
                            <div class="d-flex justify-content-around flex-wrap gap-2">
                                <span class="badge bg-primary stat-badge">
                                    <i class="bi bi-people me-1"></i>{{ $event->attendees_count }} Attendees
                                </span>
                                <span class="badge bg-warning text-dark stat-badge">
                                    <i class="bi bi-star-fill me-1"></i>{{ number_format($event->reviews_avg_rate ?? 0, 1) }} ({{ $event->reviews_count }})
                                </span>
                                <span class="badge bg-success stat-badge">
                                    <i class="bi bi-cash me-1"></i>${{ number_format($event->donations_sum_amount ?? 0, 2) }}
                                </span>
                            </div>
                            <div class="mt-2">
                                @if($event->is_published)
                                    <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Published</span>
                                @else
                                    <span class="badge bg-secondary"><i class="bi bi-pencil me-1"></i>Draft</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-2 text-end">
                            <div onclick="event.stopPropagation();">
                                <a href="{{ route('admin.events.ics', $event) }}" class="btn btn-info btn-sm mb-2 w-100" title="Download Calendar">
                                    <i class="bi bi-calendar-plus me-1"></i>ICS
                                </a>
                                <form action="{{ route('admin.events.destroy', $event) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm w-100" onclick="return confirm('Are you sure you want to delete this event? This action cannot be undone.')">
                                        <i class="bi bi-trash me-1"></i>Delete
                                    </button>
                                </form>
                            </div>
                            <small class="text-muted d-block mt-2">
                                {{ $event->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-calendar-event" style="font-size: 5rem; opacity: 0.2;"></i>
                    <p class="text-muted mt-3">No events found.</p>
                </div>
            @endforelse

            @if($events->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $events->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@extends('layouts.organizer')

@section('title', 'Organizer Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your organizations and events')

@section('content')
<div>
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm stat-card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">My Organizations</h6>
                            <h2 class="mb-0">{{ $stats['total_organizations'] }}</h2>
                            <small>{{ $stats['verified_organizations'] }} verified</small>
                        </div>
                        <i class="bi bi-building" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm stat-card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Events</h6>
                            <h2 class="mb-0">{{ $stats['total_events'] }}</h2>
                            <small>{{ $stats['published_events'] }} published</small>
                        </div>
                        <i class="bi bi-calendar-event" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm stat-card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Attendees</h6>
                            <h2 class="mb-0">{{ $stats['total_attendees'] }}</h2>
                        </div>
                        <i class="bi bi-people" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm stat-card bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-dark mb-1" style="opacity: 0.7;">Donations Received</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_donations'], 2) }} TND</h2>
                        </div>
                        <i class="bi bi-currency-dollar" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('organizer.organizations.create') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-plus-circle"></i> Create Organization
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('organizer.events.create') }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-calendar-plus"></i> Create Event
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('organizer.organizations.index') }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-building"></i> My Organizations
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('organizer.events.index') }}" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-calendar-event"></i> My Events
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Recent Events -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-calendar-check"></i> Recent Events</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($recentEvents as $event)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $event->title }}</h6>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-building"></i> {{ $event->organization->name }}
                                    </p>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-calendar"></i> {{ $event->start_at->format('M d, Y H:i') }}
                                    </p>
                                    <div class="mt-2">
                                        @if($event->is_published)
                                            <span class="badge bg-success">Published</span>
                                        @else
                                            <span class="badge bg-warning">Draft</span>
                                        @endif
                                        <span class="badge bg-info">{{ $event->participations->where('type', 'attend')->count() }} attendees</span>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('organizer.events.show', $event) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">No events yet</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- My Organizations -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-building"></i> My Organizations</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($organizations as $org)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $org->name }}</h6>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-tag"></i> {{ $org->category->name }}
                                    </p>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-geo-alt"></i> {{ $org->city }}, {{ $org->region }}
                                    </p>
                                    <div class="mt-2">
                                        @if($org->is_verified)
                                            <span class="badge bg-success">Verified</span>
                                        @else
                                            <span class="badge bg-warning">Pending</span>
                                        @endif
                                        <span class="badge bg-info">{{ $org->events->count() }} events</span>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('organizer.organizations.show', $org) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">No organizations yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.admin')

@section('title', 'Events Leaderboard')

@section('content')
<style>
    .leaderboard-card {
        transition: all 0.3s ease;
    }
    .leaderboard-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15);
    }
    .rank-badge {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: bold;
    }
    .rank-1 { background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: white; }
    .rank-2 { background: linear-gradient(135deg, #C0C0C0 0%, #A9A9A9 100%); color: white; }
    .rank-3 { background: linear-gradient(135deg, #CD7F32 0%, #8B4513 100%); color: white; }
    .rank-other { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-trophy-fill text-warning me-2"></i>Events Leaderboard</h2>
            <p class="text-muted">Top performing events and organizations</p>
        </div>
        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Events
        </a>
    </div>

    <!-- Top Organizations by Events Count -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
            <h5 class="mb-0 text-white"><i class="bi bi-building me-2"></i>Top Organizations by Events</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                @forelse($topOrganizations as $index => $org)
                    <div class="col-md-6">
                        <div class="leaderboard-card bg-light p-3 rounded">
                            <div class="d-flex align-items-center">
                                <div class="rank-badge {{ $index < 3 ? 'rank-' . ($index + 1) : 'rank-other' }} me-3">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $org->name }}</h6>
                                    <p class="mb-0 text-muted small">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $org->city }}, {{ $org->region }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <h4 class="text-primary mb-0">{{ $org->events_count }}</h4>
                                    <small class="text-muted">Events</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4">
                        <p class="text-muted">No data available</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Top Rated Events -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                    <h5 class="mb-0 text-white"><i class="bi bi-star-fill me-2"></i>Top Rated Events</h5>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    @forelse($topRatedEvents as $index => $event)
                        <div class="leaderboard-card bg-light p-3 rounded mb-3" onclick="window.location='{{ route('admin.events.show', $event) }}'" style="cursor: pointer;">
                            <div class="d-flex align-items-center">
                                <div class="rank-badge {{ $index < 3 ? 'rank-' . ($index + 1) : 'rank-other' }} me-3">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ Str::limit($event->title, 30) }}</h6>
                                    <p class="mb-0 text-muted small">
                                        <i class="bi bi-building me-1"></i>{{ $event->organization->name }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <div class="text-warning mb-1">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= round($event->reviews_avg_rate) ? '-fill' : '' }}"></i>
                                        @endfor
                                    </div>
                                    <h5 class="mb-0">{{ number_format($event->reviews_avg_rate, 1) }}</h5>
                                    <small class="text-muted">{{ $event->reviews_count }} reviews</small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">No rated events yet</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Attended Events -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                    <h5 class="mb-0 text-white"><i class="bi bi-people-fill me-2"></i>Most Attended Events</h5>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    @forelse($topAttendedEvents as $index => $event)
                        <div class="leaderboard-card bg-light p-3 rounded mb-3" onclick="window.location='{{ route('admin.events.show', $event) }}'" style="cursor: pointer;">
                            <div class="d-flex align-items-center">
                                <div class="rank-badge {{ $index < 3 ? 'rank-' . ($index + 1) : 'rank-other' }} me-3">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ Str::limit($event->title, 30) }}</h6>
                                    <p class="mb-0 text-muted small">
                                        <i class="bi bi-building me-1"></i>{{ $event->organization->name }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <h4 class="text-success mb-0">{{ $event->attendees_count }}</h4>
                                    <small class="text-muted">Attendees</small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">No attended events yet</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Donated Events -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                    <h5 class="mb-0 text-white"><i class="bi bi-cash-stack me-2"></i>Top Donated Events</h5>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    @forelse($topDonatedEvents as $index => $event)
                        <div class="leaderboard-card bg-light p-3 rounded mb-3" onclick="window.location='{{ route('admin.events.show', $event) }}'" style="cursor: pointer;">
                            <div class="d-flex align-items-center">
                                <div class="rank-badge {{ $index < 3 ? 'rank-' . ($index + 1) : 'rank-other' }} me-3">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ Str::limit($event->title, 30) }}</h6>
                                    <p class="mb-0 text-muted small">
                                        <i class="bi bi-building me-1"></i>{{ $event->organization->name }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <h4 class="text-info mb-0">${{ number_format($event->donations_sum_amount, 2) }}</h4>
                                    <small class="text-muted">Donated</small>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">No donations yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

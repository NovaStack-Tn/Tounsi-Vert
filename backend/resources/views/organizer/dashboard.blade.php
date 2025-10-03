@extends('layouts.organizer')

@section('title', 'Organizer Dashboard')
@section('page-title', $organization->name . ' Dashboard')
@section('page-subtitle', 'Overview of your organization performance')

@section('content')
<style>
    .stat-card {
        transition: all 0.3s ease;
        border-radius: 15px;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.15);
    }
    .chart-container {
        position: relative;
        height: 300px;
    }
    .review-card {
        border-left: 4px solid #2d6a4f;
        transition: all 0.3s ease;
    }
    .review-card:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transform: translateX(5px);
    }
    .event-mini-card {
        transition: all 0.3s ease;
    }
    .event-mini-card:hover {
        background: #f8f9fa;
        transform: scale(1.02);
    }
</style>

<div>
    <!-- Organization Info Header -->
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);">
        <div class="card-body text-white p-4">
            <div class="row align-items-center">
                <div class="col-md-2 text-center">
                    @if($organization->logo_path)
                        <img src="{{ Storage::url($organization->logo_path) }}" alt="{{ $organization->name }}" class="rounded" style="width: 100px; height: 100px; object-fit: cover;">
                    @else
                        <div class="bg-white text-primary rounded d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px; font-size: 3rem;">
                            <i class="bi bi-building"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-7">
                    <h2 class="mb-2">{{ $organization->name }}</h2>
                    <p class="mb-1"><i class="bi bi-tag me-2"></i>{{ $organization->category->name }}</p>
                    <p class="mb-0"><i class="bi bi-geo-alt me-2"></i>{{ $organization->city }}, {{ $organization->region }}</p>
                </div>
                <div class="col-md-3 text-end">
                    @if($organization->is_verified)
                        <span class="badge bg-success px-3 py-2 mb-2"><i class="bi bi-patch-check-fill me-1"></i>Verified</span>
                    @else
                        <span class="badge bg-warning px-3 py-2 mb-2"><i class="bi bi-clock me-1"></i>Pending Verification</span>
                    @endif
                    <br>
                    <a href="{{ route('organizer.organizations.edit') }}" class="btn btn-light btn-sm mt-2">
                        <i class="bi bi-pencil me-1"></i>Edit Organization
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row 1 -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card bg-primary text-white h-100">
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

        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card bg-success text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Attendees</h6>
                            <h2 class="mb-0">{{ $stats['total_attendees'] }}</h2>
                            <small>across all events</small>
                        </div>
                        <i class="bi bi-people-fill" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card bg-warning text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Donations</h6>
                            <h2 class="mb-0">${{ number_format($stats['total_donations_amount'], 2) }}</h2>
                            <small>{{ $stats['total_donations_count'] }} donations</small>
                        </div>
                        <i class="bi bi-heart-fill" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card bg-danger text-white h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Followers</h6>
                            <h2 class="mb-0">{{ $stats['total_followers'] }}</h2>
                            <small>following your organization</small>
                        </div>
                        <i class="bi bi-heart-fill" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards Row 2 -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card bg-info text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-calendar-check" style="font-size: 2.5rem; opacity: 0.7;"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['upcoming_events'] }}</h3>
                    <small>Upcoming Events</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card bg-secondary text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-clock-history" style="font-size: 2.5rem; opacity: 0.7;"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['past_events'] }}</h3>
                    <small>Past Events</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card bg-dark text-white h-100">
                <div class="card-body text-center">
                    <i class="bi bi-file-earmark-text" style="font-size: 2.5rem; opacity: 0.7;"></i>
                    <h3 class="mt-2 mb-0">{{ $stats['draft_events'] }}</h3>
                    <small>Draft Events</small>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card" style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);">
                <div class="card-body text-center">
                    <i class="bi bi-star-fill text-white" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-2 mb-0 text-white">{{ number_format($stats['average_rating'], 1) }}</h3>
                    <small class="text-white">Average Rating ({{ $stats['total_reviews'] }} reviews)</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 1 -->
    <div class="row mb-4">
        <!-- Events & Attendees Chart -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="bi bi-graph-up text-primary me-2"></i>Events & Attendees Trend (Last 6 Months)</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="eventsAttendeesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Types Pie Chart -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="bi bi-pie-chart-fill text-success me-2"></i>Event Types</h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="eventTypesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2 -->
    <div class="row mb-4">
        <!-- Donations Chart -->
        <div class="col-md-8 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="bi bi-cash-stack text-success me-2"></i>Donations Trend (Last 6 Months)</h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="donationsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Event Categories Pie Chart -->
        <div class="col-md-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="bi bi-pie-chart text-info me-2"></i>Event Categories</h5>
                </div>
                <div class="card-body">
                    <div style="height: 300px; position: relative;">
                        <canvas id="categoriesChart"></canvas>
                    </div>
                    @if(empty($categoryData))
                        <p class="text-muted text-center mb-0">No events yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Reviews Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0">
                    <h5 class="mb-0"><i class="bi bi-star-fill text-warning me-2"></i>Recent Reviews ({{ $stats['total_reviews'] }})</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($recentReviews as $review)
                            <div class="col-md-6 mb-3">
                                <div class="review-card bg-light p-3 rounded">
                                    <div class="d-flex justify-content-between mb-2">
                                        <strong>{{ $review->user->full_name }}</strong>
                                        <span class="text-warning">
                                            @for($i = 0; $i < $review->rate; $i++)
                                                <i class="bi bi-star-fill"></i>
                                            @endfor
                                        </span>
                                    </div>
                                    <small class="text-muted d-block mb-2"><i class="bi bi-calendar-event me-1"></i>{{ $review->event->title }}</small>
                                    @if($review->comment)
                                        <p class="mb-0 small">{{ $review->comment }}</p>
                                    @endif
                                    <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4">
                                <i class="bi bi-chat-quote" style="font-size: 3rem; opacity: 0.2;"></i>
                                <p class="text-muted mt-2">No reviews yet</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Events -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-calendar-event-fill text-primary me-2"></i>Recent Events</h5>
                <a href="{{ route('organizer.events.index') }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-eye me-1"></i>View All
                </a>
            </div>
        </div>
        <div class="card-body">
            <div class="row g-3">
                @forelse($recentEvents as $event)
                    <div class="col-md-6">
                        <div class="event-mini-card p-3 border rounded">
                            <div class="d-flex align-items-start">
                                @if($event->poster_path)
                                    <img src="{{ Storage::url($event->poster_path) }}" alt="{{ $event->title }}" class="rounded me-3" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px;">
                                        <i class="bi bi-calendar-event" style="font-size: 1.5rem;"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $event->title }}</h6>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-tag me-1"></i>{{ $event->category->name }}
                                    </p>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-calendar me-1"></i>{{ $event->start_at->format('M d, Y H:i') }}
                                    </p>
                                    <div class="d-flex gap-2">
                                        @if($event->is_published)
                                            <span class="badge bg-success">Published</span>
                                        @else
                                            <span class="badge bg-warning">Draft</span>
                                        @endif
                                        <span class="badge bg-info">{{ $event->participations->where('type', 'attend')->count() }} attendees</span>
                                    </div>
                                </div>
                                <a href="{{ route('organizer.events.show', $event) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center py-4">
                        <i class="bi bi-calendar-x" style="font-size: 4rem; opacity: 0.2;"></i>
                        <p class="text-muted mt-2">No events yet</p>
                        <a href="{{ route('organizer.events.create') }}" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Create Your First Event
                        </a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Events & Attendees Trend Chart (Line Chart)
    const eventsAttendeesCtx = document.getElementById('eventsAttendeesChart');
    new Chart(eventsAttendeesCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode(array_column($monthlyEventsData, 'month')) !!},
            datasets: [
                {
                    label: 'Events Created',
                    data: {!! json_encode(array_column($monthlyEventsData, 'count')) !!},
                    backgroundColor: 'rgba(45, 106, 79, 0.2)',
                    borderColor: 'rgba(45, 106, 79, 1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Attendees',
                    data: {!! json_encode(array_column($monthlyAttendeesData, 'count')) !!},
                    backgroundColor: 'rgba(82, 183, 136, 0.2)',
                    borderColor: 'rgba(82, 183, 136, 1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: true,
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Donations Trend Chart (Bar Chart)
    const donationsCtx = document.getElementById('donationsChart');
    new Chart(donationsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_column($monthlyDonationsData, 'month')) !!},
            datasets: [{
                label: 'Donations ($)',
                data: {!! json_encode(array_column($monthlyDonationsData, 'amount')) !!},
                backgroundColor: 'rgba(255, 193, 7, 0.8)',
                borderColor: 'rgba(255, 193, 7, 1)',
                borderWidth: 2,
                borderRadius: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });

    // Event Types Pie Chart
    const eventTypesCtx = document.getElementById('eventTypesChart');
    new Chart(eventTypesCtx, {
        type: 'doughnut',
        data: {
            labels: ['Online', 'On-site', 'Hybrid'],
            datasets: [{
                data: [
                    {{ $eventTypeData['online'] }},
                    {{ $eventTypeData['onsite'] }},
                    {{ $eventTypeData['hybrid'] }}
                ],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Event Categories Pie Chart
    @if(!empty($categoryData))
    const categoriesCtx = document.getElementById('categoriesChart');
    new Chart(categoriesCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_column($categoryData, 'name')) !!},
            datasets: [{
                data: {!! json_encode(array_column($categoryData, 'count')) !!},
                backgroundColor: [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(199, 199, 199, 0.8)',
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 206, 86, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(199, 199, 199, 1)',
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
    @endif
</script>
@endsection

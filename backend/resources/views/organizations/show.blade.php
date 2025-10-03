@extends('layouts.public')

@section('title', $organization->name)

@section('content')
<style>
    .org-hero {
        background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
        color: white;
        padding: 60px 0 40px;
        margin-bottom: 40px;
    }
    .org-logo-large {
        width: 150px;
        height: 150px;
        object-fit: cover;
        border-radius: 20px;
        border: 5px solid white;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    .stat-box {
        text-align: center;
        padding: 20px;
        border-radius: 12px;
        background: white;
        border: 2px solid #e9ecef;
        transition: all 0.3s ease;
    }
    .stat-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-color: #52b788;
    }
    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        color: #2d6a4f;
    }
    .event-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        overflow: hidden;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .event-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    .rating-stars {
        color: #ffc107;
    }
</style>

<div class="org-hero">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-2 text-center">
                @if($organization->logo_path)
                    <img src="{{ Storage::url($organization->logo_path) }}" alt="{{ $organization->name }}" class="org-logo-large">
                @else
                    <div class="org-logo-large bg-white text-primary d-flex align-items-center justify-content-center">
                        <i class="bi bi-building" style="font-size: 4rem; color: #2d6a4f;"></i>
                    </div>
                @endif
            </div>
            <div class="col-md-7">
                <div class="mb-2">
                    <span class="badge bg-light text-dark px-3 py-2">{{ $organization->category->name }}</span>
                    @if($organization->is_verified)
                        <span class="badge bg-success px-3 py-2"><i class="bi bi-patch-check-fill"></i> Verified Organization</span>
                    @endif
                </div>
                <h1 class="display-4 fw-bold mb-2">{{ $organization->name }}</h1>
                <p class="lead mb-0 opacity-75">
                    <i class="bi bi-geo-alt-fill me-2"></i>{{ $organization->city }}, {{ $organization->region }}
                </p>
            </div>
            <div class="col-md-3 text-end">
                @auth
                    @if($isFollowing)
                        <form action="{{ route('organizations.unfollow', $organization) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-lg w-100 mb-2">
                                <i class="bi bi-heart-fill me-2"></i>Following
                            </button>
                        </form>
                    @else
                        <form action="{{ route('organizations.follow', $organization) }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-light btn-lg w-100 mb-2">
                                <i class="bi bi-heart me-2"></i>Follow
                            </button>
                        </form>
                    @endif
                @else
                    <a href="{{ route('login') }}" class="btn btn-light btn-lg w-100 mb-2">
                        <i class="bi bi-heart me-2"></i>Follow
                    </a>
                @endauth
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <!-- Statistics -->
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-box">
                <div class="stat-number">{{ $followersCount }}</div>
                <div class="text-muted">Followers</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <div class="stat-number">{{ $events->total() }}</div>
                <div class="text-muted">Events</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <div class="stat-number">${{ number_format($totalDonations, 2) }}</div>
                <div class="text-muted">Total Donations</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-box">
                <div class="stat-number rating-stars">
                    <i class="bi bi-star-fill"></i> {{ number_format($averageRating, 1) }}
                </div>
                <div class="text-muted">Average Rating</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8 mb-4">
            <!-- About Section -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h3 class="mb-3"><i class="bi bi-info-circle-fill text-primary me-2"></i>About</h3>
                    <p class="lead">{{ $organization->description ?: 'No description available.' }}</p>
                </div>
            </div>

            <!-- Contact Information -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h3 class="mb-3"><i class="bi bi-telephone-fill text-primary me-2"></i>Contact Information</h3>
                    <div class="row">
                        <div class="col-md-6 mb-2">
                            <i class="bi bi-geo-alt text-primary me-2"></i>
                            <strong>Address:</strong><br>
                            <span class="ms-4">{{ $organization->address }}<br>{{ $organization->city }}, {{ $organization->region }} {{ $organization->zipcode }}</span>
                        </div>
                        @if($organization->phone_number)
                        <div class="col-md-6 mb-2">
                            <i class="bi bi-telephone text-primary me-2"></i>
                            <strong>Phone:</strong><br>
                            <span class="ms-4">{{ $organization->phone_number }}</span>
                        </div>
                        @endif
                    </div>

                    @if($organization->socialLinks->count() > 0)
                        <hr>
                        <h5 class="mb-3"><i class="bi bi-share-fill text-primary me-2"></i>Social Links</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach($organization->socialLinks as $link)
                                <a href="{{ $link->url }}" target="_blank" class="btn btn-outline-primary">
                                    <i class="bi bi-link-45deg"></i> {{ $link->title }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Events -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 p-4">
                    <h3 class="mb-0"><i class="bi bi-calendar-event-fill text-primary me-2"></i>Events ({{ $events->total() }})</h3>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        @forelse($events as $event)
                            <div class="col-md-6">
                                <div class="event-card card h-100">
                                    @if($event->poster_path)
                                        <img src="{{ Storage::url($event->poster_path) }}" class="card-img-top" alt="{{ $event->title }}" style="height: 200px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                            <i class="bi bi-calendar-event" style="font-size: 4rem; color: #dee2e6;"></i>
                                        </div>
                                    @endif
                                    <div class="card-body">
                                        <span class="badge bg-primary mb-2">{{ $event->category->name }}</span>
                                        <h5 class="card-title">{{ $event->title }}</h5>
                                        <p class="text-muted small mb-2">
                                            <i class="bi bi-calendar3"></i> {{ $event->start_at->format('M d, Y - H:i') }}
                                        </p>
                                        <p class="text-muted small mb-2">
                                            <i class="bi bi-geo-alt"></i> {{ $event->city }}, {{ $event->region }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="bi bi-people"></i> {{ $event->participations_count }} participants
                                            </small>
                                            @if($event->reviews_avg_rate)
                                                <small class="rating-stars">
                                                    <i class="bi bi-star-fill"></i> {{ number_format($event->reviews_avg_rate, 1) }}
                                                </small>
                                            @endif
                                        </div>
                                        <a href="{{ route('events.show', $event) }}" class="btn btn-primary w-100 mt-3">View Details</a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <i class="bi bi-calendar-x" style="font-size: 5rem; opacity: 0.2;"></i>
                                <p class="text-muted mt-3">No events available yet.</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    @if($events->hasPages())
                        <div class="mt-4">
                            {{ $events->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Owner Info -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-circle me-2"></i>Organization Owner</h5>
                </div>
                <div class="card-body text-center p-4">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        {{ strtoupper(substr($organization->owner->first_name, 0, 1)) }}{{ strtoupper(substr($organization->owner->last_name, 0, 1)) }}
                    </div>
                    <h5 class="mb-1">{{ $organization->owner->full_name }}</h5>
                    <p class="text-muted small mb-0">{{ $organization->owner->email }}</p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span><i class="bi bi-heart-fill text-danger me-2"></i>Followers</span>
                        <strong>{{ $followersCount }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span><i class="bi bi-calendar-check text-primary me-2"></i>Total Events</span>
                        <strong>{{ $events->total() }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                        <span><i class="bi bi-currency-dollar text-success me-2"></i>Donations</span>
                        <strong>${{ number_format($totalDonations, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="bi bi-star-fill text-warning me-2"></i>Avg Rating</span>
                        <strong>{{ number_format($averageRating, 1) }}/5</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

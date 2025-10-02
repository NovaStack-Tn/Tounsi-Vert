@extends('layouts.public')

@section('title', 'TounsiVert - Home')

@section('content')
<!-- Hero Section -->
<div class="hero-section text-center">
    <div class="container">
        <h1 class="display-3 fw-bold mb-4">Welcome to TounsiVert</h1>
        <p class="lead mb-5">Join us in making a positive impact across Tunisia through community-driven events and initiatives</p>
        <div>
            <a href="{{ route('events.index') }}" class="btn btn-light btn-lg me-3">
                <i class="bi bi-calendar-event"></i> Browse Events
            </a>
            <a href="{{ route('organizations.index') }}" class="btn btn-outline-light btn-lg">
                <i class="bi bi-building"></i> View Organizations
            </a>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="container py-5">
    <div class="row text-center">
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-calendar-check text-success" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">{{ $upcomingEvents->count() }}+</h3>
                    <p class="text-muted">Upcoming Events</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-people text-primary" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">1000+</h3>
                    <p class="text-muted">Active Members</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-building text-info" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">{{ $featuredOrganizations->count() }}+</h3>
                    <p class="text-muted">Organizations</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <i class="bi bi-heart text-danger" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">5000+</h3>
                    <p class="text-muted">Lives Impacted</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Events -->
<div class="container py-5">
    <h2 class="text-center mb-5">Upcoming Events</h2>
    <div class="row">
        @forelse($upcomingEvents as $event)
            <div class="col-md-4 mb-4">
                <div class="card h-100 card-hover">
                    @if($event->poster_path)
                        <img src="{{ Storage::url($event->poster_path) }}" class="card-img-top" alt="{{ $event->title }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-calendar-event text-white" style="font-size: 4rem;"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <span class="badge bg-primary mb-2">{{ $event->category->name }}</span>
                        <span class="badge bg-secondary mb-2">{{ ucfirst($event->type) }}</span>
                        <h5 class="card-title">{{ $event->title }}</h5>
                        <p class="card-text text-muted small">
                            <i class="bi bi-building"></i> {{ $event->organization->name }}
                        </p>
                        <p class="card-text text-muted small">
                            <i class="bi bi-calendar"></i> {{ $event->start_at->format('M d, Y') }}
                        </p>
                        <p class="card-text text-muted small">
                            <i class="bi bi-geo-alt"></i> {{ $event->city }}, {{ $event->region }}
                        </p>
                        <a href="{{ route('events.show', $event) }}" class="btn btn-outline-primary btn-sm">View Details</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center">
                <p class="text-muted">No upcoming events at the moment. Check back soon!</p>
            </div>
        @endforelse
    </div>
    <div class="text-center mt-4">
        <a href="{{ route('events.index') }}" class="btn btn-primary">View All Events</a>
    </div>
</div>

<!-- Featured Organizations -->
<div class="bg-light py-5">
    <div class="container">
        <h2 class="text-center mb-5">Featured Organizations</h2>
        <div class="row">
            @forelse($featuredOrganizations as $organization)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 card-hover">
                        @if($organization->logo_path)
                            <img src="{{ Storage::url($organization->logo_path) }}" class="card-img-top" alt="{{ $organization->name }}" style="height: 200px; object-fit: contain; padding: 20px;">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-building text-secondary" style="font-size: 4rem;"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            @if($organization->is_verified)
                                <span class="badge bg-success mb-2"><i class="bi bi-check-circle"></i> Verified</span>
                            @endif
                            <h5 class="card-title">{{ $organization->name }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($organization->description, 100) }}</p>
                            <p class="card-text text-muted small">
                                <i class="bi bi-people"></i> {{ $organization->followers_count }} followers
                            </p>
                            <a href="{{ route('organizations.show', $organization) }}" class="btn btn-outline-primary btn-sm">View Profile</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center">
                    <p class="text-muted">No organizations yet.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>

<!-- CTA Section -->
<div class="container py-5 text-center">
    <h2 class="mb-4">Ready to Make a Difference?</h2>
    <p class="lead mb-4">Join our community and start participating in events that matter</p>
    @guest
        <a href="{{ route('register') }}" class="btn btn-success btn-lg">Get Started Today</a>
    @else
        <a href="{{ route('events.index') }}" class="btn btn-success btn-lg">Explore Events</a>
    @endguest
</div>
@endsection

@extends('layouts.public')

@section('title', 'TounsiVert - Make a Difference')

@section('content')
<style>
    .hero-section {
        background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
        color: white;
        padding: 120px 0 80px;
        position: relative;
        overflow: hidden;
    }
    .hero-section::before {
        content: '🌱 🌍 💚 🤝 ⭐ 🎯 ✨';
        position: absolute;
        top: 30px;
        left: 0;
        right: 0;
        font-size: 3rem;
        text-align: center;
        opacity: 0.15;
        letter-spacing: 40px;
        animation: float 6s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
    .stat-card {
        border-radius: 20px;
        transition: all 0.4s ease;
        border: none;
        background: white;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-15px);
        box-shadow: 0 20px 40px rgba(45, 106, 79, 0.2);
    }
    .stat-icon {
        font-size: 3.5rem;
        background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    .section-title {
        position: relative;
        display: inline-block;
        padding-bottom: 15px;
    }
    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 4px;
        background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
        border-radius: 2px;
    }
    .event-card, .org-card {
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.4s ease;
        border: none;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .event-card:hover, .org-card:hover {
        transform: translateY(-15px) scale(1.03);
        box-shadow: 0 20px 40px rgba(45, 106, 79, 0.3);
    }
    .event-img {
        height: 220px;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .event-card:hover .event-img {
        transform: scale(1.15);
    }
    .cta-section {
        background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
        color: white;
        border-radius: 25px;
        padding: 60px 40px;
        margin: 80px 0;
    }
    .feature-icon {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        background: linear-gradient(135deg, #52b788 0%, #95d5b2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 2rem;
        color: white;
    }
</style>

<!-- Hero Section -->
<div class="hero-section">
    <div class="container text-center position-relative">
        <h1 class="display-2 fw-bold mb-4" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.2);">Welcome to TounsiVert</h1>
        <p class="lead mb-5" style="font-size: 1.5rem; max-width: 800px; margin: 0 auto;">Join us in making a positive impact across Tunisia through community-driven events and environmental initiatives</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <a href="{{ route('events.index') }}" class="btn btn-light btn-lg px-5 py-3" style="border-radius: 50px; font-weight: 600;">
                <i class="bi bi-calendar-event me-2"></i>Browse Events
            </a>
            <a href="{{ route('organizations.index') }}" class="btn btn-outline-light btn-lg px-5 py-3" style="border-radius: 50px; font-weight: 600;">
                <i class="bi bi-building me-2"></i>View Organizations
            </a>
        </div>
    </div>
</div>

<!-- Stats Section -->
<div class="container py-5">
    <div class="row g-4">
        <div class="col-md-3">
            <div class="stat-card card h-100 text-center p-4">
                <i class="bi bi-calendar-check stat-icon"></i>
                <h2 class="mt-3 mb-2" style="color: #2d6a4f; font-weight: 700;">{{ $stats['total_events'] }}+</h2>
                <p class="text-muted mb-0">Published Events</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card h-100 text-center p-4">
                <i class="bi bi-building stat-icon"></i>
                <h2 class="mt-3 mb-2" style="color: #2d6a4f; font-weight: 700;">{{ $stats['total_organizations'] }}+</h2>
                <p class="text-muted mb-0">Verified Organizations</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card h-100 text-center p-4">
                <i class="bi bi-people stat-icon"></i>
                <h2 class="mt-3 mb-2" style="color: #2d6a4f; font-weight: 700;">{{ number_format($stats['total_attendees']) }}+</h2>
                <p class="text-muted mb-0">Active Members</p>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card card h-100 text-center p-4">
                <i class="bi bi-cash-stack stat-icon"></i>
                <h2 class="mt-3 mb-2" style="color: #2d6a4f; font-weight: 700;">${{ number_format($stats['total_donations'], 0) }}+</h2>
                <p class="text-muted mb-0">Total Donations</p>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Events -->
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="section-title display-5 fw-bold">Upcoming Events</h2>
        <p class="text-muted mt-3">Join exciting events and make a difference in your community</p>
    </div>
    <div class="row g-4">
        @forelse($upcomingEvents as $event)
            <div class="col-md-4">
                <div class="event-card card h-100">
                    <div style="overflow: hidden;">
                        @if($event->poster_path)
                            <img src="{{ Storage::url($event->poster_path) }}" class="event-img w-100" alt="{{ $event->title }}">
                        @else
                            <div class="event-img bg-gradient d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);">
                                <i class="bi bi-calendar-event text-white" style="font-size: 5rem;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <span class="badge bg-success" style="border-radius: 20px; padding: 5px 15px;">{{ $event->category->name }}</span>
                        </div>
                        <h5 class="card-title fw-bold mb-3">{{ Str::limit($event->title, 45) }}</h5>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-building text-success me-1"></i>{{ $event->organization->name }}
                        </p>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-calendar text-success me-1"></i>{{ $event->start_at->format('M d, Y') }}
                        </p>
                        <a href="{{ route('events.show', $event) }}" class="btn btn-success w-100" style="border-radius: 50px;">
                            <i class="bi bi-arrow-right-circle me-2"></i>View Details
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-calendar-x" style="font-size: 5rem; opacity: 0.2;"></i>
                <p class="text-muted mt-3">No upcoming events at the moment</p>
            </div>
        @endforelse
    </div>
    <div class="text-center mt-5">
        <a href="{{ route('events.index') }}" class="btn btn-outline-success btn-lg" style="border-radius: 50px; padding: 12px 40px; font-weight: 600;">
            <i class="bi bi-calendar-event me-2"></i>View All Events
        </a>
    </div>
</div>

<!-- Features Section -->
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="section-title display-5 fw-bold">Why Join TounsiVert?</h2>
        <p class="text-muted mt-3">Discover what makes our platform special</p>
    </div>
    <div class="row g-4">
        <div class="col-md-4 text-center">
            <div class="feature-icon">
                <i class="bi bi-calendar-event-fill"></i>
            </div>
            <h5 class="fw-bold mb-3">Discover Events</h5>
            <p class="text-muted">Find and join impactful events happening across Tunisia</p>
        </div>
        <div class="col-md-4 text-center">
            <div class="feature-icon">
                <i class="bi bi-people-fill"></i>
            </div>
            <h5 class="fw-bold mb-3">Connect & Engage</h5>
            <p class="text-muted">Meet like-minded people and build lasting connections</p>
        </div>
        <div class="col-md-4 text-center">
            <div class="feature-icon">
                <i class="bi bi-heart-fill"></i>
            </div>
            <h5 class="fw-bold mb-3">Make Impact</h5>
            <p class="text-muted">Contribute to causes and see real change in your community</p>
        </div>
    </div>
</div>

<!-- Featured Organizations -->
<div class="container py-5">
    <div class="text-center mb-5">
        <h2 class="section-title display-5 fw-bold">Featured Organizations</h2>
        <p class="text-muted mt-3">Connect with verified organizations making a difference</p>
    </div>
    <div class="row g-4">
        @forelse($featuredOrganizations as $org)
            <div class="col-md-4">
                <div class="org-card card h-100">
                    <div class="p-4 text-center" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%); min-height: 180px; display: flex; align-items: center; justify-content: center;">
                        @if($org->logo_path)
                            <img src="{{ Storage::url($org->logo_path) }}" alt="{{ $org->name }}" style="max-height: 140px; max-width: 100%; object-fit: contain;">
                        @else
                            <i class="bi bi-building" style="font-size: 5rem; color: #52b788; opacity: 0.3;"></i>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <span class="badge bg-success" style="border-radius: 20px; padding: 5px 15px;">{{ $org->category->name }}</span>
                            @if($org->is_verified)
                                <span class="badge bg-primary" style="border-radius: 20px; padding: 5px 15px;"><i class="bi bi-patch-check-fill"></i> Verified</span>
                            @endif
                        </div>
                        <h5 class="fw-bold mb-3">{{ Str::limit($org->name, 35) }}</h5>
                        <p class="text-muted small mb-3">
                            <i class="bi bi-people text-success me-1"></i>{{ $org->followers_count }} followers
                        </p>
                        <a href="{{ route('organizations.show', $org) }}" class="btn btn-success w-100" style="border-radius: 50px;">
                            <i class="bi bi-arrow-right-circle me-2"></i>View Profile
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12 text-center py-5">
                <i class="bi bi-building-x" style="font-size: 5rem; opacity: 0.2;"></i>
                <p class="text-muted mt-3">No organizations available</p>
            </div>
        @endforelse
    </div>
    <div class="text-center mt-5">
        <a href="{{ route('organizations.index') }}" class="btn btn-outline-success btn-lg" style="border-radius: 50px; padding: 12px 40px; font-weight: 600;">
            <i class="bi bi-building me-2"></i>View All Organizations
        </a>
    </div>
</div>

<!-- CTA Section -->
<div class="container">
    <div class="cta-section text-center">
        <i class="bi bi-rocket-takeoff-fill mb-4" style="font-size: 4rem;"></i>
        <h2 class="display-5 fw-bold mb-4">Ready to Make a Difference?</h2>
        <p class="lead mb-4" style="max-width: 700px; margin: 0 auto;">Join thousands of Tunisians working together to create positive change in our communities</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            @guest
                <a href="{{ route('register') }}" class="btn btn-light btn-lg px-5 py-3" style="border-radius: 50px; font-weight: 600;">
                    <i class="bi bi-person-plus me-2"></i>Join Now
                </a>
            @endguest
            <a href="{{ route('leaderboard') }}" class="btn btn-outline-light btn-lg px-5 py-3" style="border-radius: 50px; font-weight: 600;">
                <i class="bi bi-trophy me-2"></i>View Leaderboard
            </a>
        </div>
    </div>
</div>
@endsection

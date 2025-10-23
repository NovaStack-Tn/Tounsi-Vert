@extends('layouts.public')

@section('title', 'Browse Events')

@section('content')
<style>
    .events-hero {
        background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
        color: white;
        padding: 80px 0 60px;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
    }
    .events-hero::before {
        content: '🎉 📅 🌟 🎯 ✨ 🎊';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        font-size: 2.5rem;
        text-align: center;
        opacity: 0.2;
        letter-spacing: 30px;
        animation: float 4s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-15px); }
    }
    .event-card {
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.4s ease;
        border: none;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .event-card:hover {
        transform: translateY(-15px) scale(1.02);
        box-shadow: 0 20px 40px rgba(45, 106, 79, 0.3);
    }
    .event-img {
        height: 220px;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .event-card:hover .event-img {
        transform: scale(1.1);
    }
    .event-badge {
        border-radius: 20px;
        padding: 5px 15px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .filter-card {
        border-radius: 20px;
        border: none;
        box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        background: white;
    }
    .search-btn {
        border-radius: 50px;
        padding: 10px 30px;
        font-weight: 600;
    }
</style>

<div class="events-hero">
    <div class="container text-center">
        <i class="bi bi-calendar-event-fill mb-3" style="font-size: 4rem;"></i>
        <h1 class="display-3 fw-bold mb-3">Discover Events</h1>
        <p class="lead mb-0">Join impactful events and make a difference across Tunisia</p>
    </div>
</div>

<div class="container py-5">
    <!-- Filters -->
    <div class="filter-card card mb-5">
        <div class="card-body p-4">
            <h5 class="mb-4"><i class="bi bi-funnel-fill text-success me-2"></i>Filter Events</h5>
            <form method="GET" action="{{ route('events.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Category</label>
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>🌐 Online</option>
                        <option value="onsite" {{ request('type') == 'onsite' ? 'selected' : '' }}>📍 Onsite</option>
                        <option value="hybrid" {{ request('type') == 'hybrid' ? 'selected' : '' }}>🔄 Hybrid</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Region</label>
                    <input type="text" name="region" class="form-control" placeholder="Enter region" value="{{ request('region') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">City</label>
                    <input type="text" name="city" class="form-control" placeholder="Enter city" value="{{ request('city') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success search-btn me-2"><i class="bi bi-search me-2"></i>Search Events</button>
                    <a href="{{ route('events.index') }}" class="btn btn-outline-secondary search-btn"><i class="bi bi-arrow-counterclockwise me-2"></i>Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="row">
        @forelse($events as $event)
            <div class="col-md-4 mb-4">
                <div class="event-card card h-100">
                    <div style="overflow: hidden; border-radius: 20px 20px 0 0;">
                        @if($event->poster_path)
                            <img src="{{ Storage::url($event->poster_path) }}" class="event-img w-100" alt="{{ $event->title }}">
                        @else
                            <div class="event-img bg-gradient d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);">
                                <i class="bi bi-calendar-event text-white" style="font-size: 4rem;"></i>
                            </div>
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <div class="mb-3">
                            <span class="event-badge badge bg-success">{{ $event->category->name }}</span>
                            <span class="event-badge badge bg-info ms-1">{{ ucfirst($event->type) }}</span>
                        </div>
                        <h5 class="card-title fw-bold mb-3">{{ Str::limit($event->title, 50) }}</h5>
                        <p class="card-text text-muted small mb-2">
                            <i class="bi bi-building text-success me-1"></i>{{ $event->organization->name }}
                        </p>
                        <p class="card-text text-muted small mb-2">
                            <i class="bi bi-calendar text-success me-1"></i>{{ $event->start_at->format('M d, Y - H:i') }}
                        </p>
                        <p class="card-text text-muted small mb-3">
                            <i class="bi bi-geo-alt text-success me-1"></i>{{ $event->city }}, {{ $event->region }}
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('events.show', $event) }}" class="btn btn-success w-100 search-btn">
                                <i class="bi bi-arrow-right-circle me-2"></i>View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center p-5" style="border-radius: 20px;">
                    <i class="bi bi-calendar-x" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h5 class="mt-3">No events found matching your criteria</h5>
                    <p class="text-muted">Try adjusting your filters or browse all events</p>
                    <a href="{{ route('events.index') }}" class="btn btn-success search-btn mt-2">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>View All Events
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $events->links() }}
    </div>
</div>
@endsection

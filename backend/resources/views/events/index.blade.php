@extends('layouts.public')

@section('title', 'Browse Events')

@section('content')
<div class="bg-primary-custom text-white py-5">
    <div class="container">
        <h1 class="display-4">Browse Events</h1>
        <p class="lead">Discover and join impactful events across Tunisia</p>
    </div>
</div>

<div class="container py-5">
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('events.index') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">Category</label>
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
                    <label class="form-label">Type</label>
                    <select name="type" class="form-select">
                        <option value="">All Types</option>
                        <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>Online</option>
                        <option value="onsite" {{ request('type') == 'onsite' ? 'selected' : '' }}>Onsite</option>
                        <option value="hybrid" {{ request('type') == 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Region</label>
                    <input type="text" name="region" class="form-control" placeholder="Enter region" value="{{ request('region') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" placeholder="Enter city" value="{{ request('city') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
                    <a href="{{ route('events.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="row">
        @forelse($events as $event)
            <div class="col-md-4 mb-4">
                <div class="card h-100 card-hover">
                    @if($event->poster_path)
                        <img src="{{ Storage::url($event->poster_path) }}" class="card-img-top" alt="{{ $event->title }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="card-img-top bg-secondary d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-calendar-event text-white" style="font-size: 4rem;"></i>
                        </div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <span class="badge bg-primary">{{ $event->category->name }}</span>
                            <span class="badge bg-secondary">{{ ucfirst($event->type) }}</span>
                        </div>
                        <h5 class="card-title">{{ $event->title }}</h5>
                        <p class="card-text text-muted small">
                            <i class="bi bi-building"></i> {{ $event->organization->name }}
                        </p>
                        <p class="card-text text-muted small">
                            <i class="bi bi-calendar"></i> {{ $event->start_at->format('M d, Y - H:i') }}
                        </p>
                        <p class="card-text text-muted small">
                            <i class="bi bi-geo-alt"></i> {{ $event->city }}, {{ $event->region }}
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('events.show', $event) }}" class="btn btn-outline-primary btn-sm w-100">View Details</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> No events found matching your criteria.
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

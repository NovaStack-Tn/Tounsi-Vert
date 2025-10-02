@extends('layouts.public')

@section('title', 'Browse Organizations')

@section('content')
<div class="bg-primary-custom text-white py-5">
    <div class="container">
        <h1 class="display-4">Browse Organizations</h1>
        <p class="lead">Discover verified organizations making a difference in Tunisia</p>
    </div>
</div>

<div class="container py-5">
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('organizations.index') }}" class="row g-3">
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <label class="form-label">Region</label>
                    <input type="text" name="region" class="form-control" placeholder="Enter region" value="{{ request('region') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">City</label>
                    <input type="text" name="city" class="form-control" placeholder="Enter city" value="{{ request('city') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Search</button>
                    <a href="{{ route('organizations.index') }}" class="btn btn-outline-secondary">Clear Filters</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Organizations Grid -->
    <div class="row">
        @forelse($organizations as $organization)
            <div class="col-md-4 mb-4">
                <div class="card h-100 card-hover">
                    @if($organization->logo_path)
                        <img src="{{ Storage::url($organization->logo_path) }}" class="card-img-top" alt="{{ $organization->name }}" style="height: 200px; object-fit: contain; padding: 20px;">
                    @else
                        <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-building text-secondary" style="font-size: 4rem;"></i>
                        </div>
                    @endif
                    <div class="card-body d-flex flex-column">
                        <div class="mb-2">
                            <span class="badge bg-primary">{{ $organization->category->name }}</span>
                            @if($organization->is_verified)
                                <span class="badge bg-success"><i class="bi bi-check-circle"></i> Verified</span>
                            @endif
                        </div>
                        <h5 class="card-title">{{ $organization->name }}</h5>
                        <p class="card-text text-muted small">{{ Str::limit($organization->description, 120) }}</p>
                        <p class="card-text text-muted small">
                            <i class="bi bi-geo-alt"></i> {{ $organization->city }}, {{ $organization->region }}
                        </p>
                        <p class="card-text text-muted small">
                            <i class="bi bi-people"></i> {{ $organization->followers_count }} followers
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('organizations.show', $organization) }}" class="btn btn-outline-primary btn-sm w-100">View Profile</a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info text-center">
                    <i class="bi bi-info-circle"></i> No organizations found matching your criteria.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $organizations->links() }}
    </div>
</div>
@endsection

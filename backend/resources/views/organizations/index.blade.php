@extends('layouts.public')

@section('title', 'Browse Organizations')

@section('content')
<style>
    .orgs-hero {
        background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
        color: white;
        padding: 80px 0 60px;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
    }
    .orgs-hero::before {
        content: '🏢 🌍 💚 🤝 ⭐ 🎯';
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
    .org-card {
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.4s ease;
        border: none;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    .org-card:hover {
        transform: translateY(-15px) scale(1.02);
        box-shadow: 0 20px 40px rgba(45, 106, 79, 0.3);
    }
    .org-logo-container {
        height: 200px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        transition: all 0.4s ease;
    }
    .org-card:hover .org-logo-container {
        background: linear-gradient(135deg, #52b788 0%, #2d6a4f 100%);
    }
    .org-logo {
        max-height: 160px;
        max-width: 100%;
        object-fit: contain;
        transition: transform 0.4s ease;
    }
    .org-card:hover .org-logo {
        transform: scale(1.1);
    }
    .filter-card {
        border-radius: 20px;
        border: none;
        box-shadow: 0 5px 25px rgba(0,0,0,0.1);
    }
    .search-btn {
        border-radius: 50px;
        padding: 10px 30px;
        font-weight: 600;
    }
    .verified-badge {
        border-radius: 20px;
        padding: 5px 15px;
        font-size: 0.85rem;
        font-weight: 600;
    }
</style>

<div class="orgs-hero">
    <div class="container text-center">
        <i class="bi bi-building-fill mb-3" style="font-size: 4rem;"></i>
        <h1 class="display-3 fw-bold mb-3">Discover Organizations</h1>
        <p class="lead mb-0">Connect with verified organizations making a difference across Tunisia</p>
    </div>
</div>

<div class="container py-5">
    <!-- Filters -->
    <div class="filter-card card mb-5">
        <div class="card-body p-4">
            <h5 class="mb-4"><i class="bi bi-funnel-fill text-success me-2"></i>Filter Organizations</h5>
            <form method="GET" action="{{ route('organizations.index') }}" class="row g-3">
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <label class="form-label fw-bold">Region</label>
                    <input type="text" name="region" class="form-control" placeholder="Enter region" value="{{ request('region') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">City</label>
                    <input type="text" name="city" class="form-control" placeholder="Enter city" value="{{ request('city') }}">
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-success search-btn me-2"><i class="bi bi-search me-2"></i>Search Organizations</button>
                    <a href="{{ route('organizations.index') }}" class="btn btn-outline-secondary search-btn"><i class="bi bi-arrow-counterclockwise me-2"></i>Clear</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Organizations Grid -->
    <div class="row">
        @forelse($organizations as $organization)
            <div class="col-md-4 mb-4">
                <div class="org-card card h-100">
                    <div class="org-logo-container">
                        @if($organization->logo_path)
                            <img src="{{ Storage::url($organization->logo_path) }}" class="org-logo" alt="{{ $organization->name }}">
                        @else
                            <i class="bi bi-building text-success" style="font-size: 5rem; opacity: 0.3;"></i>
                        @endif
                    </div>
                    <div class="card-body d-flex flex-column p-4">
                        <div class="mb-3">
                            <span class="verified-badge badge bg-success">{{ $organization->category->name }}</span>
                            @if($organization->is_verified)
                                <span class="verified-badge badge bg-primary ms-1"><i class="bi bi-patch-check-fill"></i> Verified</span>
                            @endif
                        </div>
                        <h5 class="card-title fw-bold mb-3">{{ Str::limit($organization->name, 40) }}</h5>
                        <p class="card-text text-muted small mb-3">{{ Str::limit($organization->description, 100) }}</p>
                        <p class="card-text text-muted small mb-2">
                            <i class="bi bi-geo-alt text-success me-1"></i>{{ $organization->city }}, {{ $organization->region }}
                        </p>
                        <p class="card-text text-muted small mb-3">
                            <i class="bi bi-people text-success me-1"></i>{{ $organization->followers_count }} followers
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('organizations.show', $organization) }}" class="btn btn-success w-100 search-btn">
                                <i class="bi bi-arrow-right-circle me-2"></i>View Profile
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning text-center p-5" style="border-radius: 20px;">
                    <i class="bi bi-building-x" style="font-size: 4rem; opacity: 0.3;"></i>
                    <h5 class="mt-3">No organizations found matching your criteria</h5>
                    <p class="text-muted">Try adjusting your filters or browse all organizations</p>
                    <a href="{{ route('organizations.index') }}" class="btn btn-success search-btn mt-2">
                        <i class="bi bi-arrow-counterclockwise me-2"></i>View All Organizations
                    </a>
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

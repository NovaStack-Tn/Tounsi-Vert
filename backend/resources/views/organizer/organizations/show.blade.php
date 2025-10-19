@extends('layouts.organizer')

@section('title', $organization->name . ' - Management')
@section('page-title', $organization->name)
@section('page-subtitle', $organization->is_verified ? 'Verified Organization' : 'Pending Verification')

@section('content')
<style>
    .org-header-card {
        background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
        color: white;
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 30px;
        box-shadow: 0 10px 30px rgba(45, 106, 79, 0.3);
    }
    .org-logo {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 15px;
        border: 4px solid white;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .stat-card {
        transition: all 0.3s ease;
        border-radius: 12px;
        border: none;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }
    .follower-avatar {
        width: 45px;
        height: 45px;
        border-radius: 50%;
        background: linear-gradient(135deg, #52b788 0%, #2d6a4f 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1.2rem;
    }
    .info-row {
        padding: 15px 0;
        border-bottom: 1px solid #f0f0f0;
    }
    .info-row:last-child {
        border-bottom: none;
    }
    .action-btn {
        transition: all 0.3s ease;
    }
    .action-btn:hover {
        transform: translateX(5px);
    }
</style>

<!-- Organization Header Card -->
<div class="org-header-card">
    <div class="row align-items-center">
        <div class="col-md-2 text-center">
            @if($organization->logo_path)
                <img src="{{ Storage::url($organization->logo_path) }}" class="org-logo" alt="{{ $organization->name }}">
            @else
                <div class="org-logo d-flex align-items-center justify-content-center bg-white text-primary">
                    <i class="bi bi-building" style="font-size: 3rem;"></i>
                </div>
            @endif
        </div>
        <div class="col-md-7">
            <h2 class="mb-2 fw-bold">{{ $organization->name }}</h2>
            <p class="mb-2 opacity-75">
                <i class="bi bi-tag-fill me-2"></i>{{ $organization->category->name }}
            </p>
            <p class="mb-2 opacity-75">
                <i class="bi bi-geo-alt-fill me-2"></i>{{ $organization->city }}, {{ $organization->region }}
            </p>
            <div class="mt-3">
                @if($organization->is_verified)
                    <span class="badge bg-success bg-opacity-25 text-white px-3 py-2">
                        <i class="bi bi-patch-check-fill"></i> Verified Organization
                    </span>
                @else
                    <span class="badge bg-warning bg-opacity-25 text-white px-3 py-2">
                        <i class="bi bi-hourglass"></i> Pending Verification
                    </span>
                @endif
            </div>
        </div>
        <div class="col-md-3 text-end">
            <a href="{{ route('organizer.organizations.edit') }}" class="btn btn-light btn-lg mb-2 w-100">
                <i class="bi bi-pencil-fill me-2"></i>Edit Details
            </a>
            <a href="{{ route('organizations.show', $organization) }}" target="_blank" class="btn btn-outline-light btn-lg w-100">
                <i class="bi bi-box-arrow-up-right me-2"></i>Public View
            </a>
        </div>
    </div>
</div>

<div>
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm stat-card border-primary">
                <div class="card-body">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-calendar-event text-primary" style="font-size: 2rem;"></i>
                    </div>
                    <h2 class="mb-1 fw-bold text-primary">{{ $organization->events->count() }}</h2>
                    <p class="text-muted mb-0">Total Events</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm stat-card border-success">
                <div class="card-body">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-people-fill text-success" style="font-size: 2rem;"></i>
                    </div>
                    <h2 class="mb-1 fw-bold text-success">{{ $followersCount }}</h2>
                    <p class="text-muted mb-0">Followers</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm stat-card border-info">
                <div class="card-body">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-calendar-check-fill text-info" style="font-size: 2rem;"></i>
                    </div>
                    <h2 class="mb-1 fw-bold text-info">{{ $organization->events->where('is_published', true)->count() }}</h2>
                    <p class="text-muted mb-0">Published Events</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm stat-card border-warning">
                <div class="card-body">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 70px; height: 70px;">
                        <i class="bi bi-graph-up text-warning" style="font-size: 2rem;"></i>
                    </div>
                    <h2 class="mb-1 fw-bold text-warning">{{ $organization->events->where('is_published', false)->count() }}</h2>
                    <p class="text-muted mb-0">Draft Events</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Organization Details -->
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #2d6a4f 0%, #40916c 100%);">
                    <h5 class="mb-0"><i class="bi bi-info-circle-fill me-2"></i>Organization Information</h5>
                </div>
                <div class="card-body p-4">
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-3">
                                <strong class="text-muted"><i class="bi bi-building me-2"></i>Name</strong>
                            </div>
                            <div class="col-md-9">
                                <span class="fw-semibold">{{ $organization->name }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-3">
                                <strong class="text-muted"><i class="bi bi-file-text me-2"></i>Description</strong>
                            </div>
                            <div class="col-md-9">
                                <p class="mb-0">{{ $organization->description ?: 'No description provided' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-3">
                                <strong class="text-muted"><i class="bi bi-tag me-2"></i>Category</strong>
                            </div>
                            <div class="col-md-9">
                                <span class="badge bg-info px-3 py-2">{{ $organization->category->name }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-3">
                                <strong class="text-muted"><i class="bi bi-geo-alt me-2"></i>Location</strong>
                            </div>
                            <div class="col-md-9">
                                <span>{{ $organization->address }}, {{ $organization->city }}, {{ $organization->region }} {{ $organization->zipcode }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-3">
                                <strong class="text-muted"><i class="bi bi-telephone me-2"></i>Phone</strong>
                            </div>
                            <div class="col-md-9">
                                <span>{{ $organization->phone_number ?: 'Not provided' }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="row">
                            <div class="col-md-3">
                                <strong class="text-muted"><i class="bi bi-patch-check me-2"></i>Status</strong>
                            </div>
                            <div class="col-md-9">
                                @if($organization->is_verified)
                                    <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i>Verified</span>
                                @else
                                    <span class="badge bg-warning px-3 py-2"><i class="bi bi-hourglass me-1"></i>Pending Verification</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Social Links -->
            @if($organization->socialLinks->count() > 0)
                <div class="card shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="bi bi-share"></i> Social Links</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            @foreach($organization->socialLinks as $link)
                                <a href="{{ $link->url }}" target="_blank" class="list-group-item list-group-item-action">
                                    <i class="bi bi-link-45deg"></i> {{ $link->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-md-4 mb-4">
            <!-- Quick Actions -->
            <div class="card shadow-sm mb-4 border-0">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #52b788 0%, #40916c 100%);">
                    <h5 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body p-3">
                    <a href="{{ route('organizer.events.create') }}?organization={{ $organization->id }}" class="btn btn-primary w-100 mb-2 action-btn">
                        <i class="bi bi-plus-circle me-2"></i>Create Event
                    </a>
                    <a href="{{ route('organizer.organizations.edit') }}" class="btn btn-outline-primary w-100 mb-2 action-btn">
                        <i class="bi bi-pencil me-2"></i>Edit Organization
                    </a>
                    <a href="{{ route('organizations.show', $organization) }}" target="_blank" class="btn btn-outline-secondary w-100 action-btn">
                        <i class="bi bi-box-arrow-up-right me-2"></i>View Public Page
                    </a>
                </div>
            </div>

            <!-- Followers -->
            <div class="card shadow-sm border-0">
                <div class="card-header text-white" style="background: linear-gradient(135deg, #52b788 0%, #2d6a4f 100%);">
                    <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Followers ({{ $followersCount }})</h5>
                </div>
                <div class="card-body p-3" style="max-height: 500px; overflow-y: auto;">
                    @forelse($followers as $follower)
                        <div class="d-flex align-items-center mb-3 pb-3 border-bottom">
                            <div class="follower-avatar me-3">
                                {{ strtoupper(substr($follower->first_name, 0, 1)) }}
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-semibold">{{ $follower->full_name }}</h6>
                                <small class="text-muted">
                                    <i class="bi bi-envelope me-1"></i>{{ $follower->email }}
                                </small><br>
                                <small class="text-muted">
                                    <i class="bi bi-star-fill text-warning me-1"></i>{{ $follower->score }} pts
                                </small>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="bi bi-people" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="text-muted mt-2 mb-0">No followers yet</p>
                            <small class="text-muted">Share your organization to gain followers!</small>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Events Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-calendar-event"></i> Events ({{ $organization->events->count() }})</h5>
            <a href="{{ route('organizer.events.create') }}?organization={{ $organization->id }}" class="btn btn-light btn-sm">
                <i class="bi bi-plus-circle"></i> New Event
            </a>
        </div>
        <div class="card-body p-0">
            @if($organization->events->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Type</th>
                                <th>Date</th>
                                <th>Location</th>
                                <th>Status</th>
                                <th>Participants</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($organization->events as $event)
                                <tr>
                                    <td>
                                        <strong>{{ $event->title }}</strong><br>
                                        <small class="text-muted">{{ $event->category->name }}</small>
                                    </td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($event->type) }}</span></td>
                                    <td><small>{{ $event->start_at->format('M d, Y H:i') }}</small></td>
                                    <td><small>{{ $event->city }}</small></td>
                                    <td>
                                        @if($event->is_published)
                                            <span class="badge bg-success">Published</span>
                                        @else
                                            <span class="badge bg-warning">Draft</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-info">{{ $event->participations->where('type', 'attend')->count() }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('organizer.events.show', $event) }}" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('organizer.events.edit', $event) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-calendar-x" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="text-muted mt-2">No events created yet</p>
                    <a href="{{ route('organizer.events.create') }}?organization={{ $organization->id }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle"></i> Create First Event
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection

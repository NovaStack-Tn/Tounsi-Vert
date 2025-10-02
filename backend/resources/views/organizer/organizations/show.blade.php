@extends('layouts.public')

@section('title', $organization->name . ' - Management')

@section('content')
<div class="bg-primary-custom text-white py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('organizer.organizations.index') }}" class="text-white">My Organizations</a></li>
                <li class="breadcrumb-item active text-white">{{ $organization->name }}</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="bi bi-building"></i> {{ $organization->name }}</h1>
                @if($organization->is_verified)
                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Verified</span>
                @else
                    <span class="badge bg-warning"><i class="bi bi-hourglass"></i> Pending Verification</span>
                @endif
            </div>
            <div>
                <a href="{{ route('organizer.organizations.edit', $organization) }}" class="btn btn-light me-2">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="{{ route('organizations.show', $organization) }}" target="_blank" class="btn btn-outline-light">
                    <i class="bi bi-box-arrow-up-right"></i> Public View
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-calendar-event text-primary" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-2">{{ $organization->events->count() }}</h3>
                    <p class="text-muted mb-0">Total Events</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-people text-success" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-2">{{ $followersCount }}</h3>
                    <p class="text-muted mb-0">Followers</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-calendar-check text-info" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-2">{{ $organization->events->where('is_published', true)->count() }}</h3>
                    <p class="text-muted mb-0">Published Events</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-eye text-warning" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-2">{{ $organization->category->name }}</h3>
                    <p class="text-muted mb-0">Category</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Organization Details -->
    <div class="row">
        <div class="col-md-8 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Organization Details</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th width="30%">Name:</th>
                            <td>{{ $organization->name }}</td>
                        </tr>
                        <tr>
                            <th>Description:</th>
                            <td>{{ $organization->description ?: 'No description' }}</td>
                        </tr>
                        <tr>
                            <th>Category:</th>
                            <td><span class="badge bg-info">{{ $organization->category->name }}</span></td>
                        </tr>
                        <tr>
                            <th>Location:</th>
                            <td>{{ $organization->address }}, {{ $organization->city }}, {{ $organization->region }} {{ $organization->zipcode }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $organization->phone_number ?: 'Not provided' }}</td>
                        </tr>
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($organization->is_verified)
                                    <span class="badge bg-success">Verified</span>
                                @else
                                    <span class="badge bg-warning">Pending Verification</span>
                                @endif
                            </td>
                        </tr>
                    </table>
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
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('organizer.events.create') }}?organization={{ $organization->id }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create Event
                    </a>
                    <a href="{{ route('organizer.organizations.edit', $organization) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-pencil"></i> Edit Organization
                    </a>
                    <a href="{{ route('organizations.show', $organization) }}" target="_blank" class="btn btn-outline-info">
                        <i class="bi bi-box-arrow-up-right"></i> View Public Page
                    </a>
                </div>
            </div>

            <!-- Logo -->
            @if($organization->logo_path)
                <div class="card shadow-sm">
                    <div class="card-header">
                        <h6 class="mb-0">Organization Logo</h6>
                    </div>
                    <div class="card-body text-center">
                        <img src="{{ Storage::url($organization->logo_path) }}" class="img-fluid" alt="{{ $organization->name }}">
                    </div>
                </div>
            @endif
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
</div>
@endsection

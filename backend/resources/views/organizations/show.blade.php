@extends('layouts.public')

@section('title', $organization->name)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mb-4">
            <!-- Organization Details -->
            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    @if($organization->logo_path)
                        <div class="text-center mb-4">
                            <img src="{{ Storage::url($organization->logo_path) }}" alt="{{ $organization->name }}" style="max-height: 200px;">
                        </div>
                    @endif
                    
                    <div class="mb-3">
                        <span class="badge bg-primary">{{ $organization->category->name }}</span>
                        @if($organization->is_verified)
                            <span class="badge bg-success"><i class="bi bi-check-circle"></i> Verified</span>
                        @endif
                    </div>
                    
                    <h1 class="card-title">{{ $organization->name }}</h1>
                    
                    <hr>
                    
                    <h5>About</h5>
                    <p>{{ $organization->description }}</p>
                    
                    <hr>
                    
                    <h5>Contact Information</h5>
                    <ul class="list-unstyled">
                        <li><strong><i class="bi bi-geo-alt"></i> Address:</strong> {{ $organization->address }}, {{ $organization->city }}, {{ $organization->region }}</li>
                        @if($organization->phone_number)
                            <li><strong><i class="bi bi-telephone"></i> Phone:</strong> {{ $organization->phone_number }}</li>
                        @endif
                    </ul>
                    
                    @if($organization->socialLinks->count() > 0)
                        <hr>
                        <h5>Social Links</h5>
                        <div class="d-flex gap-2 flex-wrap">
                            @foreach($organization->socialLinks as $link)
                                <a href="{{ $link->url }}" target="_blank" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-link"></i> {{ $link->title }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>

            <!-- Events -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Upcoming Events</h5>
                </div>
                <div class="card-body">
                    @forelse($organization->events as $event)
                        <div class="border-bottom pb-3 mb-3">
                            <h6><a href="{{ route('events.show', $event) }}">{{ $event->title }}</a></h6>
                            <p class="text-muted small mb-1">
                                <i class="bi bi-calendar"></i> {{ $event->start_at->format('M d, Y - H:i') }}
                            </p>
                            <p class="text-muted small mb-0">
                                <i class="bi bi-geo-alt"></i> {{ $event->city }}, {{ $event->region }}
                            </p>
                        </div>
                    @empty
                        <p class="text-muted">No upcoming events scheduled.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Stats Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary-custom text-white">
                    <h5 class="mb-0">Organization Stats</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span><i class="bi bi-people"></i> Followers</span>
                            <strong>{{ $followersCount }}</strong>
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between">
                            <span><i class="bi bi-calendar-event"></i> Events</span>
                            <strong>{{ $organization->events->count() }}</strong>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Owner Info -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Owner</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><strong>{{ $organization->owner->full_name }}</strong></p>
                    <p class="text-muted small">{{ $organization->owner->email }}</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

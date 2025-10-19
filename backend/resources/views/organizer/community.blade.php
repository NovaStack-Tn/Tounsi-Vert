@extends('layouts.organizer')

@section('title', 'Community - ' . $organization->name)
@section('page-title', 'Community')
@section('page-subtitle', 'Your followers and supporters')

@section('content')
<style>
    .follower-card {
        transition: all 0.3s ease;
        border-left: 4px solid #2d6a4f;
    }
    .follower-card:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transform: translateX(5px);
    }
    .user-avatar-large {
        width: 60px;
        height: 60px;
        background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
        border: 3px solid #95d5b2;
    }
</style>

<div>
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Followers</h6>
                            <h2 class="mb-0">{{ $stats['total_followers'] }}</h2>
                            <small>People following {{ $organization->name }}</small>
                        </div>
                        <i class="bi bi-people-fill" style="font-size: 4rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #52b788 0%, #95d5b2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">New This Month</h6>
                            <h2 class="mb-0">{{ $stats['new_this_month'] }}</h2>
                            <small>New followers in {{ now()->format('F Y') }}</small>
                        </div>
                        <i class="bi bi-graph-up-arrow" style="font-size: 4rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Followers List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 p-4">
            <h5 class="mb-0"><i class="bi bi-people-fill text-primary me-2"></i>All Followers ({{ $followers->total() }})</h5>
        </div>
        <div class="card-body p-4">
            @forelse($followers as $follower)
                <div class="follower-card bg-light p-3 rounded mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center">
                                <div class="user-avatar-large rounded-circle text-white d-flex align-items-center justify-content-center me-3" style="font-size: 1.5rem; font-weight: 700;">
                                    {{ strtoupper(substr($follower->first_name, 0, 1)) }}{{ strtoupper(substr($follower->last_name, 0, 1)) }}
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $follower->full_name }}</h6>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-envelope me-1"></i>{{ $follower->email }}
                                    </p>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $follower->city }}, {{ $follower->region }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="mb-2">
                                <span class="badge bg-success px-3 py-2">
                                    <i class="bi bi-calendar-check me-1"></i>{{ $follower->events_attended_count }} Events Attended
                                </span>
                            </div>
                            <div>
                                <span class="badge bg-info px-3 py-2">
                                    <i class="bi bi-star-fill me-1"></i>{{ $follower->score }} Impact Points
                                </span>
                            </div>
                        </div>
                        <div class="col-md-3 text-end">
                            <small class="text-muted d-block mb-2">
                                <i class="bi bi-clock me-1"></i>Followed {{ $follower->pivot->created_at->diffForHumans() }}
                            </small>
                            <small class="text-muted">
                                <i class="bi bi-calendar-event me-1"></i>{{ $follower->pivot->created_at->format('M d, Y') }}
                            </small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-people" style="font-size: 5rem; opacity: 0.2;"></i>
                    <p class="text-muted mt-3">No followers yet</p>
                    <p class="text-muted">Share your organization to grow your community!</p>
                </div>
            @endforelse

            @if($followers->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $followers->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

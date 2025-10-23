@extends('layouts.public')

@section('title', 'My Dashboard')

@section('content')
<div class="bg-primary-custom text-white py-4">
    <div class="container">
        <h1>Welcome, {{ $user->full_name }}!</h1>
        <p class="mb-0">Your impact score: <strong class="fs-4">{{ $user->score }} points</strong></p>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <!-- Stats Cards -->
        <div class="col-md-4 mb-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-calendar-check text-success" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">{{ $eventsAttended }}</h3>
                    <p class="text-muted">Events Attended</p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card text-center shadow-sm">
                <div class="card-body">
                    <i class="bi bi-heart text-danger" style="font-size: 3rem;"></i>
                    <h3 class="mt-3">{{ $totalDonations }}</h3>
                    <p class="text-muted">Donations Made</p>
                </div>
            </div>
        </div>

        <!-- Donations Card -->
        <div class="col-md-3">
            <div class="stat-card card shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <i class="bi bi-heart-fill stat-icon text-danger"></i>
                    <h2 class="mt-3 mb-2">${{ number_format($totalDonationsAmount, 2) }}</h2>
                    <p class="text-muted mb-3">{{ $totalDonationsCount }} Donations Made</p>
                    <a href="{{ route('donations.index') }}" class="btn btn-outline-danger btn-sm">
                        <i class="bi bi-gear me-1"></i>Manage Donations
                    </a>
                </div>
            </div>
        </div>

        <!-- Followed Organizations Card -->
        <div class="col-md-3">
            <div class="stat-card card shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <i class="bi bi-building-fill stat-icon text-primary"></i>
                    <h2 class="mt-3 mb-2">{{ $followedOrganizations }}</h2>
                    <p class="text-muted mb-3">Organizations Following</p>
                    <a href="{{ route('organizations.index') }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-search me-1"></i>Browse More
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- My Participations -->
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">My Participations</h5>
        </div>
        <div class="card-body">
            @forelse($participations as $participation)
                <div class="border-bottom pb-3 mb-3">
                    <div class="row">
                        <div class="col-md-8">
                            <h6><a href="{{ route('events.show', $participation->event) }}">{{ $participation->event->title }}</a></h6>
                            <p class="text-muted small mb-1">
                                <span class="badge bg-{{ $participation->type == 'attend' ? 'success' : ($participation->type == 'donation' ? 'danger' : 'info') }}">
                                    {{ ucfirst($participation->type) }}
                                </span>
                            </p>
                            <p class="text-muted small mb-0">
                                <i class="bi bi-calendar"></i> {{ $participation->created_at->format('M d, Y') }}
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            @if($participation->type == 'donation' && $participation->donation)
                                <p class="text-success mb-0">
                                    <strong>{{ $participation->donation->amount }} TND</strong>
                                </p>
                                <span class="badge bg-{{ $participation->donation->status == 'succeeded' ? 'success' : 'warning' }}">
                                    {{ ucfirst($participation->donation->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-muted text-center">You haven't participated in any events yet. <a href="{{ route('events.index') }}">Browse events</a></p>
            @endforelse

            <div class="d-flex justify-content-center mt-4">
                {{ $participations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection

@extends('layouts.public')

@section('title', 'My Profile')

@section('content')
<style>
    .profile-header {
        background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
        color: white;
        padding: 40px 0;
    }
    .profile-avatar {
        width: 120px;
        height: 120px;
        background: white;
        border: 5px solid #95d5b2;
        box-shadow: 0 10px 30px rgba(0,0,0,0.3);
    }
    .stat-card {
        transition: all 0.3s ease;
        border: none;
        border-radius: 15px;
        overflow: hidden;
    }
    .stat-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }
    .stat-icon {
        font-size: 3.5rem;
        opacity: 0.9;
    }
    .participation-card {
        transition: all 0.3s ease;
        border-left: 4px solid #2d6a4f;
    }
    .participation-card:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transform: translateX(5px);
    }
</style>

<div class="profile-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-2 text-center">
                <div class="profile-avatar rounded-circle text-primary d-inline-flex align-items-center justify-content-center mx-auto" style="font-size: 3rem; font-weight: 700;">
                    {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                </div>
            </div>
            <div class="col-md-7">
                <h1 class="display-5 fw-bold mb-2">{{ $user->full_name }}</h1>
                <p class="lead mb-1"><i class="bi bi-envelope me-2"></i>{{ $user->email }}</p>
                <p class="mb-0"><i class="bi bi-calendar me-2"></i>Member since {{ $user->created_at->format('F Y') }}</p>
            </div>
            <div class="col-md-3 text-end">
                <div class="bg-white text-dark rounded p-3 d-inline-block">
                    <div class="text-warning mb-1" style="font-size: 2.5rem;">
                        <i class="bi bi-star-fill"></i> {{ $user->score }}
                    </div>
                    <small class="text-muted fw-bold">Impact Points</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <!-- Statistics Cards -->
    <div class="row g-4 mb-5">
        <!-- Total Score Card -->
        <div class="col-md-3">
            <div class="stat-card card shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <i class="bi bi-trophy-fill stat-icon text-warning"></i>
                    <h2 class="mt-3 mb-2">{{ $user->score }}</h2>
                    <p class="text-muted mb-3">Total Impact Points</p>
                    <button class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#scoreBreakdownModal">
                        <i class="bi bi-info-circle me-1"></i>How it's calculated
                    </button>
                </div>
            </div>
        </div>

        <!-- Events Attended Card -->
        <div class="col-md-3">
            <div class="stat-card card shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <i class="bi bi-calendar-check-fill stat-icon text-success"></i>
                    <h2 class="mt-3 mb-2">{{ $eventsAttended }}</h2>
                    <p class="text-muted mb-3">Events Attended</p>
                    <a href="{{ route('dashboard', ['type' => 'attend']) }}#participations" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-eye me-1"></i>View Details
                    </a>
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
                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#donationHistoryModal">
                        <i class="bi bi-clock-history me-1"></i>View History
                    </button>
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
    <div class="card shadow-sm border-0" id="participations">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap">
                <h3 class="mb-0"><i class="bi bi-bookmark-star-fill text-primary me-2"></i>My Participations ({{ $participations->total() }})</h3>
            </div>
            
            <!-- Filter Tabs -->
            <div class="mt-3">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link {{ $filterType == 'all' ? 'active' : '' }}" href="{{ route('dashboard', ['type' => 'all']) }}#participations">
                            <i class="bi bi-list-ul me-1"></i>All
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $filterType == 'attend' ? 'active' : '' }}" href="{{ route('dashboard', ['type' => 'attend']) }}#participations">
                            <i class="bi bi-check-circle me-1"></i>Attended
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $filterType == 'donation' ? 'active' : '' }}" href="{{ route('dashboard', ['type' => 'donation']) }}#participations">
                            <i class="bi bi-heart-fill me-1"></i>Donations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $filterType == 'follow' ? 'active' : '' }}" href="{{ route('dashboard', ['type' => 'follow']) }}#participations">
                            <i class="bi bi-bookmark-fill me-1"></i>Following
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ $filterType == 'share' ? 'active' : '' }}" href="{{ route('dashboard', ['type' => 'share']) }}#participations">
                            <i class="bi bi-share-fill me-1"></i>Shared
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="card-body p-4">
            @forelse($participations as $participation)
                <div class="participation-card bg-light p-3 rounded mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <div class="d-flex align-items-start">
                                @if($participation->event->poster_path)
                                    <img src="{{ Storage::url($participation->event->poster_path) }}" alt="{{ $participation->event->title }}" class="rounded me-3" style="width: 80px; height: 80px; object-fit: cover;">
                                @else
                                    <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center me-3" style="width: 80px; height: 80px;">
                                        <i class="bi bi-calendar-event" style="font-size: 2rem;"></i>
                                    </div>
                                @endif
                                <div>
                                    <h6 class="mb-1">
                                        <a href="{{ route('events.show', $participation->event) }}" class="text-decoration-none">
                                            {{ $participation->event->title }}
                                        </a>
                                    </h6>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-building me-1"></i>{{ $participation->event->organization->name }}
                                    </p>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-calendar me-1"></i>{{ $participation->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <span class="badge bg-{{ $participation->type == 'attend' ? 'success' : ($participation->type == 'donation' ? 'danger' : ($participation->type == 'follow' ? 'info' : 'secondary')) }} px-3 py-2">
                                @if($participation->type == 'attend')
                                    <i class="bi bi-check-circle me-1"></i>Attended
                                @elseif($participation->type == 'donation')
                                    <i class="bi bi-heart-fill me-1"></i>Donated
                                @elseif($participation->type == 'follow')
                                    <i class="bi bi-bookmark-fill me-1"></i>Following
                                @else
                                    <i class="bi bi-share-fill me-1"></i>Shared
                                @endif
                            </span>
                        </div>
                        <div class="col-md-2 text-end">
                            @if($participation->type == 'donation' && $participation->donation)
                                <p class="text-success mb-1 fw-bold">${{ number_format($participation->donation->amount, 2) }}</p>
                                <span class="badge bg-{{ $participation->donation->status == 'succeeded' ? 'success' : 'warning' }}">
                                    {{ ucfirst($participation->donation->status) }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-inbox" style="font-size: 5rem; opacity: 0.2;"></i>
                    @if($filterType == 'all')
                        <p class="text-muted mt-3">You haven't participated in any events yet.</p>
                        <a href="{{ route('events.index') }}" class="btn btn-primary">
                            <i class="bi bi-calendar-event me-2"></i>Browse Events
                        </a>
                    @elseif($filterType == 'attend')
                        <p class="text-muted mt-3">You haven't attended any events yet.</p>
                        <a href="{{ route('events.index') }}" class="btn btn-success">
                            <i class="bi bi-calendar-check me-2"></i>Find Events to Attend
                        </a>
                    @elseif($filterType == 'donation')
                        <p class="text-muted mt-3">You haven't made any donations yet.</p>
                        <a href="{{ route('events.index') }}" class="btn btn-danger">
                            <i class="bi bi-heart me-2"></i>Find Events to Support
                        </a>
                    @elseif($filterType == 'follow')
                        <p class="text-muted mt-3">You aren't following any events yet.</p>
                        <a href="{{ route('events.index') }}" class="btn btn-info">
                            <i class="bi bi-bookmark me-2"></i>Find Events to Follow
                        </a>
                    @else
                        <p class="text-muted mt-3">You haven't shared any events yet.</p>
                        <a href="{{ route('events.index') }}" class="btn btn-secondary">
                            <i class="bi bi-share me-2"></i>Find Events to Share
                        </a>
                    @endif
                </div>
            @endforelse

            @if($participations->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $participations->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Score Breakdown Modal -->
<div class="modal fade" id="scoreBreakdownModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="bi bi-trophy-fill me-2"></i>How Your Score is Calculated</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="lead mb-4">Your impact score is calculated based on your participation:</p>
                
                <div class="mb-3 p-3 bg-light rounded">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="bi bi-check-circle text-success me-2"></i><strong>Events Attended</strong></span>
                        <span class="badge bg-success">{{ $scoreBreakdown['attend']['points_each'] }} pts each</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">{{ $scoreBreakdown['attend']['count'] }} events</span>
                        <strong class="text-success">{{ $scoreBreakdown['attend']['total'] }} pts</strong>
                    </div>
                </div>

                <div class="mb-3 p-3 bg-light rounded">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="bi bi-bookmark text-info me-2"></i><strong>Events Followed</strong></span>
                        <span class="badge bg-info">{{ $scoreBreakdown['follow']['points_each'] }} pt each</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">{{ $scoreBreakdown['follow']['count'] }} events</span>
                        <strong class="text-info">{{ $scoreBreakdown['follow']['total'] }} pts</strong>
                    </div>
                </div>

                <div class="mb-3 p-3 bg-light rounded">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="bi bi-share text-secondary me-2"></i><strong>Events Shared</strong></span>
                        <span class="badge bg-secondary">{{ $scoreBreakdown['share']['points_each'] }} pts each</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">{{ $scoreBreakdown['share']['count'] }} events</span>
                        <strong class="text-secondary">{{ $scoreBreakdown['share']['total'] }} pts</strong>
                    </div>
                </div>

                <hr>

                <div class="d-flex justify-content-between align-items-center p-3 bg-warning bg-opacity-10 rounded">
                    <strong class="fs-5">Total Impact Score:</strong>
                    <span class="badge bg-warning text-dark fs-5">{{ $user->score }} pts</span>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <small><i class="bi bi-lightbulb me-1"></i>Tip: Attend more events to earn more points and increase your impact!</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Donation History Modal -->
<div class="modal fade" id="donationHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-heart-fill me-2"></i>Your Donation History</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-success">
                    <h6 class="mb-2"><i class="bi bi-check-circle me-2"></i>Thank you for your generosity!</h6>
                    <p class="mb-0">You've donated <strong>${{ number_format($totalDonationsAmount, 2) }}</strong> across <strong>{{ $totalDonationsCount }}</strong> events.</p>
                </div>

                @forelse($donations as $donation)
                    <div class="border-bottom pb-3 mb-3">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="mb-1">
                                    <a href="{{ route('events.show', $donation->event) }}" class="text-decoration-none">
                                        {{ $donation->event->title }}
                                    </a>
                                </h6>
                                <p class="text-muted small mb-1">
                                    <i class="bi bi-building me-1"></i>{{ $donation->event->organization->name }}
                                </p>
                                <p class="text-muted small mb-0">
                                    <i class="bi bi-calendar me-1"></i>{{ $donation->donation->paid_at ? $donation->donation->paid_at->format('M d, Y H:i') : $donation->created_at->format('M d, Y H:i') }}
                                </p>
                            </div>
                            <div class="text-end">
                                <p class="text-success fw-bold mb-1 fs-5">${{ number_format($donation->donation->amount, 2) }}</p>
                                <span class="badge bg-success">Succeeded</span>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4">
                        <i class="bi bi-heart" style="font-size: 4rem; opacity: 0.2;"></i>
                        <p class="text-muted mt-2">No donation history yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

@endsection

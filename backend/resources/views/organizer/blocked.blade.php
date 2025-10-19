@extends('layouts.organizer')

@section('title', 'Organization Suspended')
@section('page-title', 'Organization Suspended')
@section('page-subtitle', 'Your organization has been suspended by administrators')

@section('content')
<style>
    .blocked-container {
        min-height: 60vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .blocked-card {
        max-width: 600px;
        animation: shake 0.5s;
    }
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-10px); }
        20%, 40%, 60%, 80% { transform: translateX(10px); }
    }
</style>

<div class="blocked-container">
    <div class="blocked-card">
        <div class="card border-0 shadow-lg">
            <div class="card-body text-center p-5">
                <div class="mb-4">
                    <i class="bi bi-ban text-danger" style="font-size: 6rem;"></i>
                </div>
                
                <h2 class="text-danger mb-3">Organization Suspended</h2>
                
                <div class="alert alert-danger mb-4">
                    <h5 class="alert-heading mb-3">
                        <i class="bi bi-exclamation-triangle me-2"></i>Access Blocked
                    </h5>
                    <p class="mb-0">
                        Your organization <strong>"{{ $organization->name }}"</strong> has been suspended by our administrators due to policy violations.
                    </p>
                </div>

                <div class="bg-light p-4 rounded mb-4">
                    <h6 class="mb-3"><i class="bi bi-info-circle text-primary me-2"></i>What does this mean?</h6>
                    <ul class="text-start text-muted small">
                        <li class="mb-2">You cannot create or edit events</li>
                        <li class="mb-2">Your existing events are hidden from users</li>
                        <li class="mb-2">You cannot access organization management features</li>
                        <li>All organizer functionalities are disabled</li>
                    </ul>
                </div>

                <div class="alert alert-warning mb-4">
                    <i class="bi bi-envelope me-2"></i>
                    <strong>Need Help?</strong> Please contact our support team for assistance.
                </div>

                <div class="d-grid gap-2">
                    <a href="{{ route('home') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-house me-2"></i>Return to Home
                    </a>
                    <button class="btn btn-outline-secondary" disabled>
                        <i class="bi bi-tools me-2"></i>Contact Administrator (Coming Soon)
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

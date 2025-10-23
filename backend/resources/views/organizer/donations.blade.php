@extends('layouts.organizer')

@section('title', 'Donations - ' . $organization->name)
@section('page-title', 'Donations History')
@section('page-subtitle', 'Track all donations to your events')

@section('content')
<style>
    .donation-card {
        transition: all 0.3s ease;
        border-left: 4px solid #ffc107;
    }
    .donation-card:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transform: translateX(5px);
    }
</style>

<div>
    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #ffc107 0%, #ffed4e 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Raised</h6>
                            <h2 class="mb-0">${{ number_format($stats['total_amount'], 2) }}</h2>
                            <small>All time donations</small>
                        </div>
                        <i class="bi bi-cash-stack" style="font-size: 4rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #28a745 0%, #5cb85c 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Donations</h6>
                            <h2 class="mb-0">{{ $stats['total_count'] }}</h2>
                            <small>Number of donations</small>
                        </div>
                        <i class="bi bi-heart-fill" style="font-size: 4rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #17a2b8 0%, #5bc0de 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">This Month</h6>
                            <h2 class="mb-0">${{ number_format($stats['this_month'], 2) }}</h2>
                            <small>{{ now()->format('F Y') }}</small>
                        </div>
                        <i class="bi bi-calendar-month" style="font-size: 4rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Insights Banner -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="text-white mb-2">
                                <i class="bi bi-robot me-2"></i>
                                🤖 Get AI-Powered Insights on Your Donations
                            </h5>
                            <p class="text-white mb-0 opacity-75">
                                Discover trends, get actionable recommendations, and generate personalized thank-you messages with artificial intelligence.
                            </p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('organizer.ai') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-lightbulb me-2"></i>
                                View AI Insights
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Donations List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 p-4">
            <h5 class="mb-0"><i class="bi bi-cash-stack text-warning me-2"></i>All Donations ({{ $donations->total() }})</h5>
        </div>
        <div class="card-body p-4">
            @forelse($donations as $donation)
                <div class="donation-card bg-light p-3 rounded mb-3">
                    <div class="row align-items-center">
                        <div class="col-md-5">
                            <div class="d-flex align-items-center">
                                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                    <i class="bi bi-heart-fill"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1">{{ $donation->participation->user->full_name }}</h6>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-envelope me-1"></i>{{ $donation->participation->user->email }}
                                    </p>
                                    <p class="text-muted small mb-0">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $donation->participation->user->city }}, {{ $donation->participation->user->region }}
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-1">
                                <small class="text-muted"><i class="bi bi-calendar-event me-1"></i><strong>Event:</strong></small>
                                <p class="mb-1">{{ $donation->event->title }}</p>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-receipt me-1"></i>Ref: {{ $donation->payment_ref ?? 'N/A' }}
                            </small>
                        </div>
                        <div class="col-md-3 text-end">
                            <div class="mb-2">
                                <h3 class="text-success mb-0">${{ number_format($donation->amount, 2) }}</h3>
                            </div>
                            <div class="mb-2">
                                <span class="badge bg-success px-3 py-2">
                                    <i class="bi bi-check-circle me-1"></i>Succeeded
                                </span>
                            </div>
                            <small class="text-muted d-block">
                                <i class="bi bi-clock me-1"></i>{{ $donation->paid_at ? $donation->paid_at->format('M d, Y H:i') : $donation->created_at->format('M d, Y H:i') }}
                            </small>
                            <small class="text-muted">
                                {{ $donation->paid_at ? $donation->paid_at->diffForHumans() : $donation->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-cash-stack" style="font-size: 5rem; opacity: 0.2;"></i>
                    <p class="text-muted mt-3">No donations yet</p>
                    <p class="text-muted">When people donate to your events, they'll appear here.</p>
                </div>
            @endforelse

            @if($donations->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $donations->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Summary Section -->
    @if($donations->count() > 0)
    <div class="card border-0 shadow-sm mt-4" style="background: linear-gradient(135deg, #28a745 0%, #5cb85c 100%);">
        <div class="card-body text-white text-center p-4">
            <i class="bi bi-heart-fill" style="font-size: 3rem; opacity: 0.5;"></i>
            <h4 class="mt-3 mb-2">Thank You to All Our Supporters!</h4>
            <p class="mb-0">Your generosity helps us make a real difference in our community. Together, we've raised <strong>${{ number_format($stats['total_amount'], 2) }}</strong> through {{ $stats['total_count'] }} donations.</p>
        </div>
    </div>
    @endif
</div>
@endsection

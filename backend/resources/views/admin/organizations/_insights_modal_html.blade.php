{{-- Organization Insights Modal (HTML Only) --}}
<div class="modal fade" id="insightsModal{{ $organization->id }}" tabindex="-1" aria-labelledby="insightsModalLabel{{ $organization->id }}" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <h5 class="modal-title" id="insightsModalLabel{{ $organization->id }}">
                    <i class="bi bi-graph-up-arrow me-2"></i>{{ $organization->name }} - Insights
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                @php
                    $insights = $organization->getDonationInsights();
                    $completeness = $organization->profile_completeness;
                @endphp

                <!-- Profile Completeness & Donation Insights -->
                <div class="row g-3 mb-4">
                    <!-- Profile Completeness Card -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                                <h6 class="mb-0">
                                    <i class="bi bi-clipboard-check me-2"></i>Profile Completeness
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="text-center mb-3">
                                    <div class="position-relative d-inline-block">
                                        <svg width="120" height="120">
                                            <circle cx="60" cy="60" r="50" fill="none" stroke="#e9ecef" stroke-width="10"/>
                                            <circle cx="60" cy="60" r="50" fill="none" stroke="#667eea" stroke-width="10"
                                                    stroke-dasharray="{{ $completeness * 3.14 }} 314"
                                                    stroke-dashoffset="0"
                                                    transform="rotate(-90 60 60)"
                                                    stroke-linecap="round"/>
                                        </svg>
                                        <div class="position-absolute top-50 start-50 translate-middle">
                                            <h2 class="mb-0">{{ $completeness }}%</h2>
                                        </div>
                                    </div>
                                </div>
                                
                                @if($completeness < 100)
                                    <div class="alert alert-info mb-0">
                                        <small>
                                            <i class="bi bi-lightbulb me-1"></i>
                                            <strong>Tip:</strong> Complete your profile to increase visibility.
                                        </small>
                                    </div>
                                @else
                                    <div class="alert alert-success mb-0">
                                        <small>
                                            <i class="bi bi-check-circle me-1"></i>
                                            <strong>Excellent!</strong> Profile is 100% complete.
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Donation Insights Card -->
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="bi bi-graph-up me-2"></i>Donation Insights (30 Days)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row text-center">
                                    <div class="col-6 mb-3">
                                        <h4 class="text-success mb-0">{{ number_format($insights['total'], 2) }} TND</h4>
                                        <small class="text-muted">Total Donations</small>
                                    </div>
                                    <div class="col-6 mb-3">
                                        <h4 class="text-primary mb-0">{{ $insights['donors'] }}</h4>
                                        <small class="text-muted">Unique Donors</small>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="text-warning mb-0">{{ number_format($insights['avg_donation'], 2) }} TND</h4>
                                        <small class="text-muted">Average Donation</small>
                                    </div>
                                    <div class="col-6">
                                        <h4 class="mb-0 {{ $insights['trend'] >= 0 ? 'text-success' : 'text-danger' }}">
                                            {{ $insights['trend'] > 0 ? '+' : '' }}{{ $insights['trend'] }}%
                                            <i class="bi bi-{{ $insights['trend'] >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                        </h4>
                                        <small class="text-muted">30-Day Trend</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Row -->
                <div class="row g-3">
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm text-center">
                            <div class="card-body">
                                <h3 class="text-primary mb-0">{{ $organization->events_count ?? 0 }}</h3>
                                <small class="text-muted">Total Events</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm text-center">
                            <div class="card-body">
                                <h3 class="text-info mb-0">{{ $organization->followers_count ?? 0 }}</h3>
                                <small class="text-muted">Followers</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm text-center">
                            <div class="card-body">
                                <h3 class="text-success mb-0">{{ number_format($organization->donations_sum_amount ?? 0, 2) }} TND</h3>
                                <small class="text-muted">All-Time Donations</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card border-0 shadow-sm text-center">
                            <div class="card-body">
                                @if($organization->is_verified)
                                    <span class="badge bg-success" style="font-size: 1.2rem; padding: 0.5rem 1rem;">
                                        <i class="bi bi-shield-check me-1"></i>Verified
                                    </span>
                                @elseif($organization->is_blocked)
                                    <span class="badge bg-danger" style="font-size: 1.2rem; padding: 0.5rem 1rem;">
                                        <i class="bi bi-ban me-1"></i>Blocked
                                    </span>
                                @else
                                    <span class="badge bg-warning" style="font-size: 1.2rem; padding: 0.5rem 1rem;">
                                        <i class="bi bi-clock me-1"></i>Pending
                                    </span>
                                @endif
                                <small class="d-block text-muted mt-2">Status</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

@extends('layouts.public')

@section('title', 'My Reports')

@section('content')
<style>
    .report-card {
        transition: all 0.3s ease;
        border-left: 4px solid #dc3545;
    }
    .report-card:hover {
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        transform: translateX(5px);
    }
</style>

<div class="container py-5">
    <!-- Header -->
    <div class="mb-4">
        <h2 class="mb-2"><i class="bi bi-flag-fill text-danger me-2"></i>My Reports</h2>
        <p class="text-muted">View and track all your submitted reports</p>
    </div>

    <!-- Filter Tabs -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link {{ $status == 'all' ? 'active' : '' }}" href="{{ route('member.reports.index', ['status' => 'all']) }}">
                        <i class="bi bi-list-ul me-1"></i>All Reports
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status == 'open' ? 'active' : '' }}" href="{{ route('member.reports.index', ['status' => 'open']) }}">
                        <i class="bi bi-clock me-1"></i>Open
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status == 'in_review' ? 'active' : '' }}" href="{{ route('member.reports.index', ['status' => 'in_review']) }}">
                        <i class="bi bi-gear me-1"></i>In Review
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status == 'resolved' ? 'active' : '' }}" href="{{ route('member.reports.index', ['status' => 'resolved']) }}">
                        <i class="bi bi-check-circle me-1"></i>Resolved
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status == 'dismissed' ? 'active' : '' }}" href="{{ route('member.reports.index', ['status' => 'dismissed']) }}">
                        <i class="bi bi-x-circle me-1"></i>Dismissed
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Reports List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 p-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list me-2"></i>Reports ({{ $reports->total() }})</h5>
            </div>
        </div>
        <div class="card-body p-4">
            @forelse($reports as $report)
                <div class="report-card bg-light p-3 rounded mb-3">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="d-flex align-items-start mb-2">
                                <div class="me-3">
                                    @if($report->organization_id)
                                        <i class="bi bi-building text-danger" style="font-size: 2rem;"></i>
                                    @else
                                        <i class="bi bi-calendar-event text-danger" style="font-size: 2rem;"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">
                                        @if($report->organization_id)
                                            <span class="badge bg-warning text-dark me-2">Organization</span>
                                            {{ $report->organization->name ?? 'N/A' }}
                                        @elseif($report->event_id)
                                            <span class="badge bg-info me-2">Event</span>
                                            {{ $report->event->title ?? 'N/A' }}
                                        @else
                                            <span class="badge bg-secondary me-2">General</span>
                                        @endif
                                    </h6>
                                    <p class="mb-2">
                                        <strong class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Reason:</strong> 
                                        {{ $report->reason }}
                                    </p>
                                    @if($report->details)
                                        <p class="text-muted small mb-0">
                                            <strong>Details:</strong> {{ Str::limit($report->details, 150) }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 text-end">
                            <div class="mb-2">
                                @if($report->status == 'open')
                                    <span class="badge bg-warning px-3 py-2">
                                        <i class="bi bi-clock me-1"></i>Open
                                    </span>
                                @elseif($report->status == 'in_review')
                                    <span class="badge bg-primary px-3 py-2">
                                        <i class="bi bi-gear me-1"></i>In Review
                                    </span>
                                @elseif($report->status == 'resolved')
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="bi bi-check-circle me-1"></i>Resolved
                                    </span>
                                @elseif($report->status == 'dismissed')
                                    <span class="badge bg-danger px-3 py-2">
                                        <i class="bi bi-x-circle me-1"></i>Dismissed
                                    </span>
                                @endif
                            </div>
                            <small class="text-muted d-block">
                                <i class="bi bi-calendar me-1"></i>{{ $report->created_at->format('M d, Y') }}
                            </small>
                            <small class="text-muted">
                                {{ $report->created_at->diffForHumans() }}
                            </small>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-flag" style="font-size: 5rem; opacity: 0.2;"></i>
                    @if($status == 'all')
                        <p class="text-muted mt-3">You haven't submitted any reports yet.</p>
                    @elseif($status == 'open')
                        <p class="text-muted mt-3">No open reports.</p>
                    @elseif($status == 'in_review')
                        <p class="text-muted mt-3">No reports in review.</p>
                    @elseif($status == 'resolved')
                        <p class="text-muted mt-3">No resolved reports.</p>
                    @else
                        <p class="text-muted mt-3">No dismissed reports.</p>
                    @endif
                </div>
            @endforelse

            @if($reports->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Info Box -->
    <div class="alert alert-info mt-4">
        <i class="bi bi-info-circle me-2"></i>
        <strong>Note:</strong> Our team reviews all reports carefully. Response time may vary depending on the complexity of the issue. Thank you for helping us maintain a safe community.
    </div>
</div>
@endsection

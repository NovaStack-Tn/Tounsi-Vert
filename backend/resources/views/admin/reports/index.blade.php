@extends('layouts.admin')

@section('title', 'Reports Management')

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
    .action-btn {
        transition: all 0.3s ease;
    }
    .action-btn:hover {
        transform: scale(1.05);
    }
</style>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-flag-fill text-danger me-2"></i>Reports Management</h2>
            <p class="text-muted">Review and manage user reports</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <h3 class="mb-0">{{ $stats['total'] }}</h3>
                    <small>Total Reports</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                <div class="card-body text-white">
                    <h3 class="mb-0">{{ $stats['open'] }}</h3>
                    <small>Open</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);">
                <div class="card-body text-white">
                    <h3 class="mb-0">{{ $stats['in_review'] }}</h3>
                    <small>In Review</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%);">
                <div class="card-body text-white">
                    <h3 class="mb-0">{{ $stats['resolved'] }}</h3>
                    <small>Resolved</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                <div class="card-body text-white">
                    <h3 class="mb-0">{{ $stats['dismissed'] }}</h3>
                    <small>Dismissed</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link {{ $status == 'all' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'all']) }}">
                        <i class="bi bi-list-ul me-1"></i>All ({{ $stats['total'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status == 'open' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'open']) }}">
                        <i class="bi bi-clock me-1"></i>Open ({{ $stats['open'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status == 'in_review' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'in_review']) }}">
                        <i class="bi bi-gear me-1"></i>In Review ({{ $stats['in_review'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status == 'resolved' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'resolved']) }}">
                        <i class="bi bi-check-circle me-1"></i>Resolved ({{ $stats['resolved'] }})
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $status == 'dismissed' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'dismissed']) }}">
                        <i class="bi bi-x-circle me-1"></i>Dismissed ({{ $stats['dismissed'] }})
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Reports List -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 p-4">
            <h5 class="mb-0"><i class="bi bi-list me-2"></i>Reports ({{ $reports->total() }})</h5>
        </div>
        <div class="card-body p-4">
            @forelse($reports as $report)
                <div class="report-card bg-light p-4 rounded mb-3">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="d-flex align-items-start mb-3">
                                <div class="me-3">
                                    @if($report->organization_id)
                                        <i class="bi bi-building text-danger" style="font-size: 2.5rem;"></i>
                                    @else
                                        <i class="bi bi-calendar-event text-danger" style="font-size: 2.5rem;"></i>
                                    @endif
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-2">
                                        @if($report->organization_id)
                                            <span class="badge bg-warning text-dark me-2">Organization</span>
                                            {{ $report->organization->name ?? 'N/A' }}
                                        @elseif($report->event_id)
                                            <span class="badge bg-info me-2">Event</span>
                                            {{ $report->event->title ?? 'N/A' }}
                                        @endif
                                    </h6>
                                    <p class="mb-2">
                                        <strong><i class="bi bi-person me-1"></i>Reported by:</strong> 
                                        {{ $report->user->full_name }} ({{ $report->user->email }})
                                    </p>
                                    <p class="mb-2">
                                        <strong class="text-danger"><i class="bi bi-exclamation-triangle me-1"></i>Reason:</strong> 
                                        {{ $report->reason }}
                                    </p>
                                    @if($report->details)
                                        <p class="text-muted small mb-0">
                                            <strong>Details:</strong> {{ $report->details }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 text-center">
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

                        <div class="col-md-4">
                            <div class="d-grid gap-2">
                                @if($report->status != 'resolved')
                                    <form action="{{ route('admin.reports.resolve', $report) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success w-100 action-btn" onclick="return confirm('Mark this report as resolved?')">
                                            <i class="bi bi-check-circle me-1"></i>Mark as Resolved
                                        </button>
                                    </form>
                                @endif

                                @if($report->status != 'dismissed')
                                    <form action="{{ route('admin.reports.dismiss', $report) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary w-100 action-btn" onclick="return confirm('Dismiss this report?')">
                                            <i class="bi bi-x-circle me-1"></i>Dismiss Report
                                        </button>
                                    </form>
                                @endif

                                @if($report->organization_id && $report->status != 'resolved')
                                    @if(!$report->organization->is_blocked)
                                        <form action="{{ route('admin.reports.suspendOrganization', $report) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-danger w-100 action-btn" onclick="return confirm('Are you sure you want to SUSPEND this organization? This action will block all their activities.')">
                                                <i class="bi bi-ban me-1"></i>Suspend Organization
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-dark w-100" disabled>
                                            <i class="bi bi-ban me-1"></i>Already Suspended
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="bi bi-flag" style="font-size: 5rem; opacity: 0.2;"></i>
                    <p class="text-muted mt-3">No reports found for this filter.</p>
                </div>
            @endforelse

            @if($reports->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $reports->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

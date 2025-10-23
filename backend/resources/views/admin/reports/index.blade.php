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
            <p class="text-muted">Review and manage user reports with AI-powered analysis</p>
        </div>
        <div>
            <a href="{{ route('admin.reports.analytics') }}" class="btn btn-primary me-2">
                <i class="bi bi-graph-up me-1"></i>Analytics
            </a>
            <button class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#advancedSearch">
                <i class="bi bi-search me-1"></i>Advanced Search
            </button>
        </div>
    </div>

    <!-- Advanced Search -->
    <div class="collapse mb-4" id="advancedSearch">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <form action="{{ route('admin.reports.index') }}" method="GET">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label small">Search Text</label>
                            <input type="text" name="search" class="form-control" placeholder="Search in reason or details..." value="{{ request('search') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Date From</label>
                            <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small">Date To</label>
                            <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Status</label>
                            <select name="status" class="form-select">
                                <option value="all">All Statuses</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_review" {{ request('status') == 'in_review' ? 'selected' : '' }}>In Review</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="dismissed" {{ request('status') == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Priority</label>
                            <select name="priority" class="form-select">
                                <option value="all">All Priorities</option>
                                <option value="critical" {{ request('priority') == 'critical' ? 'selected' : '' }}>Critical</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>High</option>
                                <option value="medium" {{ request('priority') == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>Low</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Category</label>
                            <select name="category" class="form-select">
                                <option value="all">All Categories</option>
                                <option value="spam" {{ request('category') == 'spam' ? 'selected' : '' }}>Spam</option>
                                <option value="inappropriate" {{ request('category') == 'inappropriate' ? 'selected' : '' }}>Inappropriate</option>
                                <option value="fraud" {{ request('category') == 'fraud' ? 'selected' : '' }}>Fraud</option>
                                <option value="harassment" {{ request('category') == 'harassment' ? 'selected' : '' }}>Harassment</option>
                                <option value="violence" {{ request('category') == 'violence' ? 'selected' : '' }}>Violence</option>
                                <option value="misinformation" {{ request('category') == 'misinformation' ? 'selected' : '' }}>Misinformation</option>
                                <option value="copyright" {{ request('category') == 'copyright' ? 'selected' : '' }}>Copyright</option>
                                <option value="other" {{ request('category') == 'other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Sort By</label>
                            <select name="sort_by" class="form-select">
                                <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date Created</option>
                                <option value="ai_risk_score" {{ request('sort_by') == 'ai_risk_score' ? 'selected' : '' }}>AI Risk Score</option>
                                <option value="priority" {{ request('sort_by') == 'priority' ? 'selected' : '' }}>Priority</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search me-1"></i>Search
                            </button>
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-1"></i>Clear
                            </a>
                        </div>
                    </div>
                </form>
            </div>
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
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #28a745 0%, #218838 100%);">
                <div class="card-body text-white">
                    <h3 class="mb-0">{{ $stats['resolved'] }}</h3>
                    <small>Resolved</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);">
                <div class="card-body text-white">
                    <h3 class="mb-0">{{ $stats['dismissed'] }}</h3>
                    <small>Dismissed</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);">
                <div class="card-body text-white">
                    <h3 class="mb-0">{{ $stats['auto_flagged'] ?? 0 }}</h3>
                    <small><i class="bi bi-robot me-1"></i>AI Flagged</small>
                </div>
            </div>
        </div>
    </div>

    <!-- AI Insights Alert -->
    @if(($stats['high_risk'] ?? 0) > 0)
        <div class="alert alert-danger border-0 shadow-sm mb-4">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill me-3" style="font-size: 2rem;"></i>
                <div>
                    <h5 class="mb-1"><i class="bi bi-robot me-2"></i>AI Alert: High Risk Reports Detected</h5>
                    <p class="mb-0">
                        There are <strong>{{ $stats['high_risk'] }}</strong> reports with high AI risk scores (≥70) that require immediate attention.
                        <a href="{{ route('admin.reports.index', ['sort_by' => 'ai_risk_score', 'sort_order' => 'desc']) }}" class="alert-link">View High Risk Reports →</a>
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-12">
                    <ul class="nav nav-pills mb-3">
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'all' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'all', 'priority' => $priority, 'category' => $category]) }}">
                                <i class="bi bi-list-ul me-1"></i>All ({{ $stats['total'] }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'open' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'open', 'priority' => $priority, 'category' => $category]) }}">
                                <i class="bi bi-clock me-1"></i>Open ({{ $stats['open'] }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'in_review' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'in_review', 'priority' => $priority, 'category' => $category]) }}">
                                <i class="bi bi-gear me-1"></i>In Review ({{ $stats['in_review'] }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'resolved' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'resolved', 'priority' => $priority, 'category' => $category]) }}">
                                <i class="bi bi-check-circle me-1"></i>Resolved ({{ $stats['resolved'] }})
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ $status == 'dismissed' ? 'active' : '' }}" href="{{ route('admin.reports.index', ['status' => 'dismissed', 'priority' => $priority, 'category' => $category]) }}">
                                <i class="bi bi-x-circle me-1"></i>Dismissed ({{ $stats['dismissed'] }})
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted">Filter by Priority</label>
                    <select class="form-select" onchange="window.location.href='{{ route('admin.reports.index') }}?status={{ $status }}&priority=' + this.value + '&category={{ $category }}'">
                        <option value="all" {{ $priority == 'all' ? 'selected' : '' }}>All Priorities</option>
                        <option value="critical" {{ $priority == 'critical' ? 'selected' : '' }}>Critical</option>
                        <option value="high" {{ $priority == 'high' ? 'selected' : '' }}>High</option>
                        <option value="medium" {{ $priority == 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="low" {{ $priority == 'low' ? 'selected' : '' }}>Low</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small text-muted">Filter by Category</label>
                    <select class="form-select" onchange="window.location.href='{{ route('admin.reports.index') }}?status={{ $status }}&priority={{ $priority }}&category=' + this.value">
                        <option value="all" {{ $category == 'all' ? 'selected' : '' }}>All Categories</option>
                        <option value="spam" {{ $category == 'spam' ? 'selected' : '' }}>Spam</option>
                        <option value="inappropriate" {{ $category == 'inappropriate' ? 'selected' : '' }}>Inappropriate</option>
                        <option value="fraud" {{ $category == 'fraud' ? 'selected' : '' }}>Fraud</option>
                        <option value="harassment" {{ $category == 'harassment' ? 'selected' : '' }}>Harassment</option>
                        <option value="violence" {{ $category == 'violence' ? 'selected' : '' }}>Violence</option>
                        <option value="misinformation" {{ $category == 'misinformation' ? 'selected' : '' }}>Misinformation</option>
                        <option value="copyright" {{ $category == 'copyright' ? 'selected' : '' }}>Copyright</option>
                        <option value="other" {{ $category == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
            </div>
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
                                    <div class="mb-2">
                                        <span class="badge bg-{{ $report->priorityBadge }} me-1">{{ ucfirst($report->priority) }}</span>
                                        <span class="badge bg-secondary me-1">{{ $report->categoryLabel }}</span>
                                        @if($report->ai_risk_score > 0)
                                            <span class="badge bg-{{ $report->aiRiskBadge }} me-1" title="AI Risk Score">
                                                <i class="bi bi-robot me-1"></i>Risk: {{ $report->ai_risk_score }}
                                            </span>
                                        @endif
                                        @if($report->ai_auto_flagged)
                                            <span class="badge bg-danger me-1" title="Auto-flagged by AI">
                                                <i class="bi bi-flag-fill me-1"></i>AI Flagged
                                            </span>
                                        @endif
                                    </div>
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
                                            <strong>Details:</strong> {{ Str::limit($report->details, 150) }}
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
                                <a href="{{ route('admin.reports.show', $report) }}" class="btn btn-primary action-btn">
                                    <i class="bi bi-eye me-1"></i>View Details & Actions
                                </a>

                                @if($report->status != 'resolved')
                                    <form action="{{ route('admin.reports.updateStatus', $report) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="resolved">
                                        <button type="submit" class="btn btn-success w-100 action-btn" onclick="return confirm('Mark this report as resolved?')">
                                            <i class="bi bi-check-circle me-1"></i>Mark as Resolved
                                        </button>
                                    </form>
                                @endif

                                @if($report->status != 'dismissed')
                                    <form action="{{ route('admin.reports.updateStatus', $report) }}" method="POST" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="status" value="dismissed">
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

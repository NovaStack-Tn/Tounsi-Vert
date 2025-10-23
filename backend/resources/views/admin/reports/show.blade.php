@extends('layouts.admin')

@section('title', 'Report Details #' . $report->id)

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Reports
        </a>
    </div>

    <div class="row">
        <!-- Report Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="bi bi-flag-fill me-2"></i>Report #{{ $report->id }}</h4>
                </div>
                <div class="card-body p-4">
                    <!-- Status & Priority Badges -->
                    <div class="mb-4">
                        <span class="badge bg-{{ $report->statusBadge }} px-3 py-2 me-2">
                            {{ ucfirst($report->status) }}
                        </span>
                        <span class="badge bg-{{ $report->priorityBadge }} px-3 py-2 me-2">
                            Priority: {{ ucfirst($report->priority) }}
                        </span>
                        <span class="badge bg-secondary px-3 py-2 me-2">
                            {{ $report->categoryLabel }}
                        </span>
                        @if($report->ai_risk_score > 0)
                            <span class="badge bg-{{ $report->aiRiskBadge }} px-3 py-2 me-2">
                                <i class="bi bi-robot me-1"></i>AI Risk: {{ $report->ai_risk_score }}/100
                            </span>
                        @endif
                        @if($report->ai_auto_flagged)
                            <span class="badge bg-danger px-3 py-2">
                                <i class="bi bi-flag-fill me-1"></i>AI Auto-Flagged
                            </span>
                        @endif
                    </div>

                    <!-- AI Analysis -->
                    @if($report->ai_analysis)
                        <div class="mb-4 p-3 bg-light rounded border-start border-4 border-info">
                            <h6 class="text-info mb-3"><i class="bi bi-robot me-2"></i>AI Analysis</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <p class="mb-2">
                                        <strong>Suggested Category:</strong> 
                                        <span class="badge bg-info">{{ ucfirst($report->ai_suggested_category ?? 'N/A') }}</span>
                                    </p>
                                    <p class="mb-2">
                                        <strong>Confidence:</strong> {{ $report->ai_confidence }}%
                                    </p>
                                    <p class="mb-0">
                                        <strong>Risk Level:</strong> 
                                        <span class="badge bg-{{ $report->aiRiskBadge }}">{{ ucfirst($report->aiRiskLevel) }}</span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    @if(isset($report->ai_analysis['category_scores']))
                                        <small class="text-muted d-block mb-1">Category Scores:</small>
                                        @foreach($report->ai_analysis['category_scores'] as $cat => $score)
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small>{{ ucfirst($cat) }}:</small>
                                                <div class="progress flex-grow-1 mx-2" style="height: 10px; max-width: 100px;">
                                                    <div class="progress-bar bg-info" style="width: {{ $score }}%"></div>
                                                </div>
                                                <small>{{ round($score) }}%</small>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            @if($report->ai_analysis['requires_immediate_attention'] ?? false)
                                <div class="alert alert-danger mt-3 mb-0">
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <strong>AI Alert:</strong> This report requires immediate attention!
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- Reported Item -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="text-muted mb-2">Reported Item:</h6>
                        @if($report->organization_id)
                            <div class="d-flex align-items-center">
                                <i class="bi bi-building text-warning me-3" style="font-size: 2rem;"></i>
                                <div>
                                    <h5 class="mb-1">{{ $report->organization->name }}</h5>
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $report->organization->city }}, {{ $report->organization->region }}
                                    </p>
                                    <a href="{{ route('organizations.show', $report->organization) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>View Organization
                                    </a>
                                </div>
                            </div>
                        @elseif($report->event_id)
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-event text-info me-3" style="font-size: 2rem;"></i>
                                <div>
                                    <h5 class="mb-1">{{ $report->event->title }}</h5>
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $report->event->start_at->format('M d, Y') }}
                                    </p>
                                    <a href="{{ route('events.show', $report->event) }}" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="bi bi-box-arrow-up-right me-1"></i>View Event
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Reporter Info -->
                    <div class="mb-4 p-3 bg-light rounded">
                        <h6 class="text-muted mb-2">Reported By:</h6>
                        <div class="d-flex align-items-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                {{ strtoupper(substr($report->user->first_name, 0, 1)) }}{{ strtoupper(substr($report->user->last_name, 0, 1)) }}
                            </div>
                            <div>
                                <h6 class="mb-0">{{ $report->user->full_name }}</h6>
                                <small class="text-muted">{{ $report->user->email }}</small><br>
                                <small class="text-muted">Member since {{ $report->user->created_at->format('M Y') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Report Content -->
                    <div class="mb-4">
                        <h6 class="text-danger mb-2"><i class="bi bi-exclamation-triangle me-2"></i>Reason:</h6>
                        <p class="lead">{{ $report->reason }}</p>
                    </div>

                    @if($report->details)
                        <div class="mb-4">
                            <h6 class="text-muted mb-2">Details:</h6>
                            <p class="text-justify">{{ $report->details }}</p>
                        </div>
                    @endif

                    <!-- Timestamps -->
                    <div class="border-top pt-3">
                        <small class="text-muted">
                            <i class="bi bi-clock me-1"></i>Submitted: {{ $report->created_at->format('M d, Y H:i') }} ({{ $report->created_at->diffForHumans() }})
                        </small>
                        @if($report->resolved_at)
                            <br>
                            <small class="text-muted">
                                <i class="bi bi-check-circle me-1"></i>Resolved: {{ $report->resolved_at->format('M d, Y H:i') }} 
                                @if($report->resolver)
                                    by {{ $report->resolver->full_name }}
                                @endif
                            </small>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Actions History -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Actions History ({{ $report->actions->count() }})</h5>
                </div>
                <div class="card-body p-4">
                    @forelse($report->actions as $action)
                        <div class="border-start border-4 border-{{ $action->actionTypeBadge }} ps-4 pb-4 position-relative">
                            <div class="position-absolute" style="left: -8px; top: 0;">
                                <div class="bg-{{ $action->actionTypeBadge }} rounded-circle" style="width: 12px; height: 12px;"></div>
                            </div>
                            
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <span class="badge bg-{{ $action->actionTypeBadge }} mb-2">{{ $action->actionTypeLabel }}</span>
                                    <h6 class="mb-1">{{ $action->admin->full_name }}</h6>
                                    <small class="text-muted">
                                        <i class="bi bi-calendar3 me-1"></i>{{ $action->action_taken_at->format('M d, Y H:i') }}
                                        <span class="ms-2">({{ $action->action_taken_at->diffForHumans() }})</span>
                                    </small>
                                </div>
                            </div>

                            @if($action->action_note)
                                <div class="alert alert-light mb-2">
                                    <strong>Public Note:</strong><br>
                                    {{ $action->action_note }}
                                </div>
                            @endif

                            @if($action->internal_note)
                                <div class="alert alert-warning mb-0">
                                    <strong><i class="bi bi-lock me-1"></i>Internal Note:</strong><br>
                                    {{ $action->internal_note }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-4">
                            <i class="bi bi-hourglass-split" style="font-size: 3rem; opacity: 0.2;"></i>
                            <p class="text-muted mt-2">No actions taken yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Actions Panel -->
        <div class="col-lg-4">
            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Quick Actions</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.updateStatus', $report) }}" method="POST" class="mb-3">
                        @csrf
                        <label class="form-label">Update Status</label>
                        <div class="input-group">
                            <select name="status" class="form-select" required>
                                <option value="open" {{ $report->status == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_review" {{ $report->status == 'in_review' ? 'selected' : '' }}>In Review</option>
                                <option value="resolved" {{ $report->status == 'resolved' ? 'selected' : '' }}>Resolved</option>
                                <option value="dismissed" {{ $report->status == 'dismissed' ? 'selected' : '' }}>Dismissed</option>
                            </select>
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>

                    <form action="{{ route('admin.reports.updateStatus', $report) }}" method="POST" class="mb-3">
                        @csrf
                        <label class="form-label">Update Priority</label>
                        <div class="input-group">
                            <select name="priority" class="form-select" required>
                                <option value="low" {{ $report->priority == 'low' ? 'selected' : '' }}>Low</option>
                                <option value="medium" {{ $report->priority == 'medium' ? 'selected' : '' }}>Medium</option>
                                <option value="high" {{ $report->priority == 'high' ? 'selected' : '' }}>High</option>
                                <option value="critical" {{ $report->priority == 'critical' ? 'selected' : '' }}>Critical</option>
                            </select>
                            <button type="submit" class="btn btn-warning">Update</button>
                        </div>
                    </form>

                    @if($report->organization_id && !$report->organization->is_blocked)
                        <form action="{{ route('admin.reports.suspendOrganization', $report) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Are you sure you want to SUSPEND this organization?')">
                                <i class="bi bi-ban me-1"></i>Suspend Organization
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Add Action -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add Action</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.addAction', $report) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label class="form-label">Action Type <span class="text-danger">*</span></label>
                            <select name="action_type" class="form-select" required>
                                <option value="">Select action...</option>
                                <option value="reviewed">Reviewed</option>
                                <option value="investigating">Investigating</option>
                                <option value="resolved">Resolved</option>
                                <option value="dismissed">Dismissed</option>
                                <option value="warning_sent">Warning Sent</option>
                                <option value="content_removed">Content Removed</option>
                                <option value="account_suspended">Account Suspended</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Public Note</label>
                            <textarea name="action_note" class="form-control" rows="3" placeholder="This note will be visible to the reporter..."></textarea>
                            <small class="text-muted">Optional - Visible to users</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Internal Note</label>
                            <textarea name="internal_note" class="form-control" rows="3" placeholder="Internal notes for admin team..."></textarea>
                            <small class="text-muted">Optional - Only visible to admins</small>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-plus-circle me-1"></i>Add Action
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

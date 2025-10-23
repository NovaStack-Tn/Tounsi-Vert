@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Overview of your platform statistics')

@section('content')
<div>
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Users</h6>
                            <h2 class="mb-0">{{ $stats['total_users'] }}</h2>
                        </div>
                        <i class="bi bi-people" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Organizations</h6>
                            <h2 class="mb-0">{{ $stats['total_organizations'] }}</h2>
                            <small>{{ $stats['verified_organizations'] }} verified</small>
                        </div>
                        <i class="bi bi-building" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Events</h6>
                            <h2 class="mb-0">{{ $stats['total_events'] }}</h2>
                            <small>{{ $stats['published_events'] }} published</small>
                        </div>
                        <i class="bi bi-calendar-event" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm bg-warning text-dark">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-dark mb-1" style="opacity: 0.7;">Open Reports</h6>
                            <h2 class="mb-0">{{ $stats['open_reports'] }}</h2>
                        </div>
                        <i class="bi bi-exclamation-triangle" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-white-50 mb-1">Total Donations</h6>
                            <h2 class="mb-0">{{ number_format($stats['total_donations'], 2) }} TND</h2>
                        </div>
                        <i class="bi bi-currency-dollar" style="font-size: 3rem; opacity: 0.5;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.organizations.index') }}" class="btn btn-outline-primary w-100">
                                <i class="bi bi-building"></i> Manage Organizations
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('admin.reports.index') }}" class="btn btn-outline-warning w-100">
                                <i class="bi bi-flag"></i> View Reports ({{ $stats['open_reports'] }})
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('events.index') }}" class="btn btn-outline-info w-100">
                                <i class="bi bi-calendar"></i> View All Events
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="{{ route('organizations.index') }}" class="btn btn-outline-success w-100">
                                <i class="bi bi-eye"></i> Public View
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Management Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h5 class="mb-0">
                        <i class="bi bi-tags-fill me-2"></i>Category Management
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Organization Categories -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-primary h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-primary text-white rounded-circle p-3 me-3">
                                            <i class="bi bi-building" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">Organization Categories</h5>
                                            <small class="text-muted">Manage categories for organizations</small>
                                        </div>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.org-categories.index') }}" class="btn btn-primary">
                                            <i class="bi bi-list-ul me-2"></i>View All Categories
                                        </a>
                                        <a href="{{ route('admin.org-categories.create') }}" class="btn btn-outline-primary">
                                            <i class="bi bi-plus-circle me-2"></i>Add New Category
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Event Categories -->
                        <div class="col-md-6 mb-3">
                            <div class="card border-success h-100">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="bg-success text-white rounded-circle p-3 me-3">
                                            <i class="bi bi-calendar-event" style="font-size: 1.5rem;"></i>
                                        </div>
                                        <div>
                                            <h5 class="mb-0">Event Categories</h5>
                                            <small class="text-muted">Manage categories for events</small>
                                        </div>
                                    </div>
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('admin.event-categories.index') }}" class="btn btn-success">
                                            <i class="bi bi-list-ul me-2"></i>View All Categories
                                        </a>
                                        <a href="{{ route('admin.event-categories.create') }}" class="btn btn-outline-success">
                                            <i class="bi bi-plus-circle me-2"></i>Add New Category
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Pending Organizations -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-hourglass-split"></i> Pending Organizations</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($pendingOrganizations as $org)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">{{ $org->name }}</h6>
                                    <p class="text-muted small mb-1">
                                        <i class="bi bi-person"></i> {{ $org->owner->full_name }}
                                    </p>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-geo-alt"></i> {{ $org->city }}, {{ $org->region }}
                                    </p>
                                </div>
                                <div>
                                    <form method="POST" action="{{ route('admin.organizations.verify', $org) }}" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-sm" title="Verify">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('organizations.show', $org) }}" class="btn btn-info btn-sm" title="View" target="_blank">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">No pending organizations</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Reports -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="bi bi-exclamation-circle"></i> Recent Reports</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    @forelse($recentReports as $report)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <div class="flex-grow-1">
                                    <span class="badge bg-{{ $report->status == 'open' ? 'danger' : ($report->status == 'in_review' ? 'warning' : 'success') }} mb-1">
                                        {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                                    </span>
                                    <h6 class="mb-1">{{ $report->reason }}</h6>
                                    <p class="text-muted small mb-1">
                                        By: {{ $report->user->full_name }}
                                    </p>
                                    @if($report->event)
                                        <p class="text-muted small mb-0">
                                            <i class="bi bi-calendar"></i> Event: {{ $report->event->title }}
                                        </p>
                                    @endif
                                    @if($report->organization)
                                        <p class="text-muted small mb-0">
                                            <i class="bi bi-building"></i> Org: {{ $report->organization->name }}
                                        </p>
                                    @endif
                                    <small class="text-muted">{{ $report->created_at->diffForHumans() }}</small>
                                </div>
                                <div>
                                    <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-arrow-right"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center mb-0">No recent reports</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection

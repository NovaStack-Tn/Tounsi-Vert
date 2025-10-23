@extends('layouts.public')

@section('title', 'My Donations')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="bi bi-heart-fill text-danger"></i> Donations Management</h2>
                    <p class="text-muted">View and manage all your donations</p>
                </div>
                <div>
                    <a href="{{ route('donations.statistics') }}" class="btn btn-info me-2">
                        <i class="bi bi-graph-up"></i> Statistics
                    </a>
                    <a href="{{ route('donations.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-lg"></i> New Donation
                    </a>
                </div>
            </div>

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Donations</h5>
                            <h3>{{ $totalCount }}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Amount</h5>
                            <h3>{{ number_format($totalDonations, 2) }} TND</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Export Options</h5>
                            <div class="d-flex gap-2">
                                <a href="{{ route('donations.export.pdf', request()->query()) }}" class="btn btn-outline-danger">
                                    <i class="bi bi-file-pdf"></i> Export PDF
                                </a>
                                <a href="{{ route('donations.export.csv', request()->query()) }}" class="btn btn-outline-success">
                                    <i class="bi bi-file-excel"></i> Export CSV
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-funnel"></i> Filters</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('donations.index') }}">
                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label">Organization</label>
                                <select name="organization_id" class="form-select">
                                    <option value="">All Organizations</option>
                                    @foreach($organizations as $org)
                                        <option value="{{ $org->id }}" {{ request('organization_id') == $org->id ? 'selected' : '' }}>
                                            {{ $org->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Status</label>
                                <select name="status" class="form-select">
                                    <option value="">All Statuses</option>
                                    @foreach($statuses as $status)
                                        <option value="{{ $status }}" {{ request('status') == $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Start Date</label>
                                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">End Date</label>
                                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="w-100">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-search"></i> Filter
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="my_donations" id="myDonations" 
                                       {{ request()->has('my_donations') ? 'checked' : '' }}>
                                <label class="form-check-label" for="myDonations">
                                    Show only my donations
                                </label>
                            </div>
                        </div>
                        @if(request()->hasAny(['organization_id', 'status', 'start_date', 'end_date', 'my_donations']))
                            <div class="mt-2">
                                <a href="{{ route('donations.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-x"></i> Clear Filters
                                </a>
                            </div>
                        @endif
                    </form>
                </div>
            </div>

            <!-- Donations by Organization -->
            @if($donationsByOrganization->count() > 0)
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-building"></i> Donations by Organization</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Organization</th>
                                    <th>Total Donations</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($donationsByOrganization as $item)
                                    <tr>
                                        <td>
                                            @if($item->organization)
                                                <strong>{{ $item->organization->name }}</strong>
                                            @else
                                                <em class="text-muted">N/A</em>
                                            @endif
                                        </td>
                                        <td>{{ $item->donation_count }}</td>
                                        <td><strong>{{ number_format($item->total_amount, 2) }} TND</strong></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endif

            <!-- Donations List -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-list"></i> Donations List</h5>
                </div>
                <div class="card-body">
                    @if($donations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Organization</th>
                                        <th>Event</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($donations as $donation)
                                        <tr>
                                            <td>#{{ $donation->id }}</td>
                                            <td>
                                                @if($donation->organization)
                                                    <a href="{{ route('organizations.show', $donation->organization) }}">
                                                        {{ $donation->organization->name }}
                                                    </a>
                                                @else
                                                    <em class="text-muted">N/A</em>
                                                @endif
                                            </td>
                                            <td>
                                                @if($donation->event)
                                                    <a href="{{ route('events.show', $donation->event) }}">
                                                        {{ $donation->event->title }}
                                                    </a>
                                                @else
                                                    <em class="text-muted">General</em>
                                                @endif
                                            </td>
                                            <td><strong>{{ number_format($donation->amount, 2) }} TND</strong></td>
                                            <td>
                                                @php
                                                    $statusClasses = [
                                                        'pending' => 'warning',
                                                        'succeeded' => 'success',
                                                        'failed' => 'danger',
                                                        'refunded' => 'secondary'
                                                    ];
                                                @endphp
                                                <span class="badge bg-{{ $statusClasses[$donation->status] ?? 'secondary' }}">
                                                    {{ ucfirst($donation->status) }}
                                                </span>
                                            </td>
                                            <td>{{ $donation->created_at->format('M d, Y') }}</td>
                                            <td>
                                                <div class="btn-group btn-group-sm">
                                                    <a href="{{ route('donations.show', $donation) }}" class="btn btn-sm btn-info">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    @if($donation->status === 'pending')
                                                        <a href="{{ route('donations.edit', $donation) }}" class="btn btn-sm btn-warning">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                    @endif
                                                    @if(in_array($donation->status, ['pending', 'failed']))
                                                        <form action="{{ route('donations.destroy', $donation) }}" method="POST" 
                                                              onsubmit="return confirm('Are you sure you want to delete this donation?')">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-sm btn-danger">
                                                                <i class="bi bi-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="d-flex justify-content-center mt-4">
                            {{ $donations->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-inbox" style="font-size: 4rem; color: #ccc;"></i>
                            <h5 class="mt-3">No donations found</h5>
                            <p class="text-muted">Start making a difference by creating your first donation</p>
                            <a href="{{ route('donations.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-lg"></i> Create Donation
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

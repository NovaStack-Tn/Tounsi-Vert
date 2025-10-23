@extends('layouts.admin')

@section('title', 'Organization Insights - ' . $organization->name)

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2>
                <i class="bi bi-graph-up-arrow text-success me-2"></i>
                Organization Insights
            </h2>
            <p class="text-muted">{{ $organization->name }}</p>
        </div>
        <a href="{{ route('admin.organizations.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back to Organizations
        </a>
    </div>

    <!-- Insights Widget -->
    @include('admin.organizations._insights_widget', ['organization' => $organization])

    <!-- Organization Details Card -->
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-building me-2"></i>Organization Details</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 150px;">Name:</th>
                            <td>{{ $organization->name }}</td>
                        </tr>
                        <tr>
                            <th>Category:</th>
                            <td>
                                <span class="badge bg-info">{{ $organization->category->name ?? 'N/A' }}</span>
                            </td>
                        </tr>
                        <tr>
                            <th>Owner:</th>
                            <td>{{ $organization->owner->full_name ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Email:</th>
                            <td>{{ $organization->owner->email ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Phone:</th>
                            <td>{{ $organization->phone_number ?? 'N/A' }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <table class="table table-borderless">
                        <tr>
                            <th style="width: 150px;">Location:</th>
                            <td>{{ $organization->city }}, {{ $organization->region }}</td>
                        </tr>
                        <tr>
                            <th>Address:</th>
                            <td>{{ $organization->address ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>Created:</th>
                            <td>{{ $organization->created_at->format('M d, Y') }}</td>
                        </tr>
                        <tr>
                            <th>Last Updated:</th>
                            <td>{{ $organization->updated_at->diffForHumans() }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Events -->
    @if($organization->events->count() > 0)
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Recent Events</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Type</th>
                            <th>Date</th>
                            <th>Attendees</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($organization->events->take(5) as $event)
                        <tr>
                            <td>{{ $event->title }}</td>
                            <td><span class="badge bg-secondary">{{ $event->type }}</span></td>
                            <td>{{ $event->start_at->format('M d, Y') }}</td>
                            <td>{{ $event->participations->where('type', 'attend')->count() }}</td>
                            <td>
                                @if($event->is_published)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-warning">Draft</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif

    <!-- Recent Donations -->
    @if($organization->donations->count() > 0)
    <div class="card shadow-sm mt-4">
        <div class="card-header bg-light">
            <h5 class="mb-0"><i class="bi bi-cash-coin me-2"></i>Recent Donations</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Donor</th>
                            <th>Amount</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($organization->donations()->with('participation.user')->latest()->take(10)->get() as $donation)
                        <tr>
                            <td>{{ $donation->participation->user->full_name ?? 'Anonymous' }}</td>
                            <td><strong>{{ number_format($donation->amount, 2) }} TND</strong></td>
                            <td>{{ $donation->created_at->format('M d, Y') }}</td>
                            <td>
                                @if($donation->status == 'succeeded')
                                    <span class="badge bg-success">Succeeded</span>
                                @elseif($donation->status == 'pending')
                                    <span class="badge bg-warning">Pending</span>
                                @elseif($donation->status == 'failed')
                                    <span class="badge bg-danger">Failed</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($donation->status) }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

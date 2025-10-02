@extends('layouts.admin')

@section('title', 'Manage Reports')
@section('page-title', 'Reports Management')
@section('page-subtitle', 'Review and moderate user reports')

@section('content')
<div>
    <div class="card shadow-sm">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Reports ({{ $reports->total() }})</h5>
            <div>
                <span class="badge bg-light text-dark">{{ $reports->where('status', 'open')->count() }} Open</span>
                <span class="badge bg-warning text-dark">{{ $reports->where('status', 'in_review')->count() }} In Review</span>
                <span class="badge bg-success">{{ $reports->where('status', 'resolved')->count() }} Resolved</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Reporter</th>
                            <th>Target</th>
                            <th>Reason</th>
                            <th>Details</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $report)
                            <tr class="{{ $report->status == 'open' ? 'table-warning' : '' }}">
                                <td class="align-middle">{{ $report->id }}</td>
                                <td class="align-middle">
                                    <strong>{{ $report->user->full_name }}</strong><br>
                                    <small class="text-muted">{{ $report->user->email }}</small>
                                </td>
                                <td class="align-middle">
                                    @if($report->event)
                                        <span class="badge bg-info mb-1">Event</span><br>
                                        <a href="{{ route('events.show', $report->event) }}" target="_blank" class="small">
                                            {{ Str::limit($report->event->title, 30) }}
                                        </a>
                                    @elseif($report->organization)
                                        <span class="badge bg-success mb-1">Organization</span><br>
                                        <a href="{{ route('organizations.show', $report->organization) }}" target="_blank" class="small">
                                            {{ Str::limit($report->organization->name, 30) }}
                                        </a>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <strong>{{ $report->reason }}</strong>
                                </td>
                                <td class="align-middle">
                                    <small>{{ Str::limit($report->details, 50) }}</small>
                                    @if(strlen($report->details) > 50)
                                        <button type="button" class="btn btn-link btn-sm p-0" data-bs-toggle="modal" data-bs-target="#detailsModal{{ $report->id }}">
                                            Read more
                                        </button>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-{{ $report->status == 'open' ? 'danger' : ($report->status == 'in_review' ? 'warning' : ($report->status == 'resolved' ? 'success' : 'secondary')) }}">
                                        {{ ucfirst(str_replace('_', ' ', $report->status)) }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                    <small>{{ $report->created_at->format('M d, Y') }}</small><br>
                                    <small class="text-muted">{{ $report->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="align-middle">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            Update Status
                                        </button>
                                        <ul class="dropdown-menu">
                                            @foreach(['open', 'in_review', 'resolved', 'dismissed'] as $status)
                                                @if($status != $report->status)
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.reports.updateStatus', $report) }}">
                                                            @csrf
                                                            @method('PATCH')
                                                            <input type="hidden" name="status" value="{{ $status }}">
                                                            <button type="submit" class="dropdown-item">
                                                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                                                            </button>
                                                        </form>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    </div>
                                </td>
                            </tr>

                            <!-- Details Modal -->
                            @if(strlen($report->details) > 50)
                                <div class="modal fade" id="detailsModal{{ $report->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Report Details</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p><strong>Reason:</strong> {{ $report->reason }}</p>
                                                <p><strong>Details:</strong></p>
                                                <p>{{ $report->details }}</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2">No reports found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $reports->links() }}
        </div>
    </div>
@endsection

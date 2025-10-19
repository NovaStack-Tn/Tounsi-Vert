@extends('layouts.admin')

@section('title', 'Organization Requests')
@section('page-title', 'Organization Requests')
@section('page-subtitle', 'Review and approve member applications')

@section('content')
<div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Requests ({{ $requests->total() }})</h5>
            <div>
                <span class="badge bg-warning text-dark">{{ $requests->where('status', 'pending')->count() }} Pending</span>
                <span class="badge bg-success">{{ $requests->where('status', 'approved')->count() }} Approved</span>
                <span class="badge bg-danger">{{ $requests->where('status', 'rejected')->count() }} Rejected</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Organization Name</th>
                            <th>Category</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                            <tr class="{{ $request->status == 'pending' ? 'table-warning' : '' }}">
                                <td class="align-middle">{{ $request->id }}</td>
                                <td class="align-middle">
                                    <strong>{{ $request->user->full_name }}</strong><br>
                                    <small class="text-muted">{{ $request->user->email }}</small>
                                </td>
                                <td class="align-middle">
                                    <strong>{{ $request->organization_name }}</strong><br>
                                    <small class="text-muted">{{ Str::limit($request->description, 50) }}</small>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-info">{{ $request->category->name }}</span>
                                </td>
                                <td class="align-middle">
                                    <small>{{ $request->city }}, {{ $request->region }}</small>
                                </td>
                                <td class="align-middle">
                                    @if($request->status == 'pending')
                                        <span class="badge bg-warning">Pending</span>
                                    @elseif($request->status == 'approved')
                                        <span class="badge bg-success">Approved</span>
                                    @else
                                        <span class="badge bg-danger">Rejected</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <small>{{ $request->created_at->format('M d, Y') }}</small><br>
                                    <small class="text-muted">{{ $request->created_at->diffForHumans() }}</small>
                                </td>
                                <td class="align-middle">
                                    @if($request->status == 'pending')
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $request->id }}">
                                            <i class="bi bi-check-circle"></i> Approve
                                        </button>
                                        <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $request->id }}">
                                            <i class="bi bi-x-circle"></i> Reject
                                        </button>
                                    @else
                                        <span class="text-muted">Reviewed</span>
                                    @endif
                                </td>
                            </tr>

                            <!-- Approve Modal -->
                            <div class="modal fade" id="approveModal{{ $request->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-success text-white">
                                            <h5 class="modal-title">Approve Organization Request</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('admin.organization-requests.approve', $request) }}">
                                            @csrf
                                            <div class="modal-body">
                                                <p><strong>Organization:</strong> {{ $request->organization_name }}</p>
                                                <p><strong>Applicant:</strong> {{ $request->user->full_name }}</p>
                                                <p class="mb-3"><strong>Category:</strong> {{ $request->category->name }}</p>
                                                
                                                <div class="alert alert-info">
                                                    <i class="bi bi-info-circle me-2"></i>
                                                    <strong>This will:</strong>
                                                    <ul class="mb-0 mt-2">
                                                        <li>Create the organization</li>
                                                        <li>Change user role to "organizer"</li>
                                                        <li>Auto-verify the organization</li>
                                                    </ul>
                                                </div>

                                                <div class="mb-3">
                                                    <label class="form-label">Admin Notes (Optional)</label>
                                                    <textarea class="form-control" name="admin_notes" rows="3" placeholder="Add any notes..."></textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-success">
                                                    <i class="bi bi-check-circle"></i> Approve & Create Organization
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <!-- Reject Modal -->
                            <div class="modal fade" id="rejectModal{{ $request->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header bg-danger text-white">
                                            <h5 class="modal-title">Reject Organization Request</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <form method="POST" action="{{ route('admin.organization-requests.reject', $request) }}">
                                            @csrf
                                            <div class="modal-body">
                                                <p><strong>Organization:</strong> {{ $request->organization_name }}</p>
                                                <p class="mb-3"><strong>Applicant:</strong> {{ $request->user->full_name }}</p>
                                                
                                                <div class="mb-3">
                                                    <label class="form-label">Reason for Rejection <span class="text-danger">*</span></label>
                                                    <textarea class="form-control" name="admin_notes" rows="4" required placeholder="Explain why this request is being rejected..."></textarea>
                                                    <small class="text-muted">This will be visible to the applicant</small>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-danger">
                                                    <i class="bi bi-x-circle"></i> Reject Request
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2">No organization requests found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $requests->links() }}
        </div>
    </div>
</div>
@endsection

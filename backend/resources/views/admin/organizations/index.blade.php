@extends('layouts.admin')

@section('title', 'Manage Organizations')
@section('page-title', 'Organizations Management')
@section('page-subtitle', 'Verify and manage all organizations')

@section('content')
<div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Organizations ({{ $organizations->total() }})</h5>
            <div>
                <span class="badge bg-success">{{ $organizations->where('is_verified', true)->count() }} Verified</span>
                <span class="badge bg-warning">{{ $organizations->where('is_verified', false)->count() }} Pending</span>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Owner</th>
                            <th>Location</th>
                            <th>Events</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($organizations as $org)
                            <tr>
                                <td class="align-middle">{{ $org->id }}</td>
                                <td class="align-middle">
                                    @if($org->logo_path)
                                        <img src="{{ Storage::url($org->logo_path) }}" alt="{{ $org->name }}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 5px;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; border-radius: 5px;">
                                            <i class="bi bi-building text-secondary"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <strong>{{ $org->name }}</strong><br>
                                    <small class="text-muted">{{ Str::limit($org->description, 50) }}</small>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-info">{{ $org->category->name }}</span>
                                </td>
                                <td class="align-middle">
                                    {{ $org->owner->full_name }}<br>
                                    <small class="text-muted">{{ $org->owner->email }}</small>
                                </td>
                                <td class="align-middle">
                                    <small>{{ $org->city }}, {{ $org->region }}</small>
                                </td>
                                <td class="align-middle text-center">
                                    <span class="badge bg-secondary">{{ $org->events_count }}</span>
                                </td>
                                <td class="align-middle">
                                    @if($org->is_verified)
                                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Verified</span>
                                    @else
                                        <span class="badge bg-warning"><i class="bi bi-hourglass"></i> Pending</span>
                                    @endif
                                </td>
                                <td class="align-middle">
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('organizations.show', $org) }}" class="btn btn-sm btn-info" target="_blank" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        
                                        @if(!$org->is_verified)
                                            <form method="POST" action="{{ route('admin.organizations.verify', $org) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-success" title="Verify" onclick="return confirm('Verify this organization?')">
                                                    <i class="bi bi-check-circle"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.organizations.unverify', $org) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning" title="Unverify" onclick="return confirm('Unverify this organization?')">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form method="POST" action="{{ route('admin.organizations.destroy', $org) }}" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this organization? This action cannot be undone!')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2">No organizations found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $organizations->links() }}
        </div>
    </div>
@endsection

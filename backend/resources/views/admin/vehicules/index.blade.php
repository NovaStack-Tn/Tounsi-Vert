@extends('layouts.admin')

@section('title', 'Manage Vehicules')
@section('page-title', 'Vehicules Management')
@section('page-subtitle', 'Manage all vehicules')

@section('content')
<div>
    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">All Vehicules ({{ $vehicules->total() }})</h5>
        
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>Capacity</th>
                            <th>Owner</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($vehicules as $vehicule)
                            <tr>
                                <td class="align-middle">{{ $vehicule->id }}</td>
                                <td class="align-middle"><strong>{{ ucfirst($vehicule->type) }}</strong></td>
                                <td class="align-middle">
                                    <small class="text-muted">{{ Str::limit($vehicule->description, 60) ?? 'No description' }}</small>
                                </td>
                                <td class="align-middle text-center">{{ $vehicule->capacity ?? '-' }}</td>
                                <td class="align-middle">
                                    <small class="text-muted">{{ $vehicule->owner->email ?? '' }}</small>
                                </td>
                                <td class="align-middle">{{ $vehicule->location ?? '-' }}</td>
                                <td class="align-middle">
                                    <span class="badge 
                                        @if($vehicule->status === 'available') bg-success 
                                        @elseif($vehicule->status === 'unavailable') bg-warning 
                                        @else bg-secondary @endif">
                                        {{ ucfirst($vehicule->status ?? 'unknown') }}
                                    </span>
                                </td>
                                <td class="align-middle">
                                   
                                    <form method="POST" action="{{ route('admin.vehicules.destroy', $vehicule) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Delete this vehicule? This action cannot be undone!')">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4">
                                    <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                                    <p class="text-muted mt-2">No vehicules found</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $vehicules->links() }}
        </div>
    </div>
</div>
@endsection

@extends('layouts.organizer')

@section('title', 'My Organizations')
@section('page-title', 'My Organizations')
@section('page-subtitle', 'Manage your organizations and track their performance')

@section('content')
<div class="mb-4">
    <a href="{{ route('organizer.organizations.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>Create Organization
    </a>
</div>

<div>
    @if($organizations->count() > 0)
        <div class="row">
            @foreach($organizations as $org)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        @if($org->logo_path)
                            <img src="{{ Storage::url($org->logo_path) }}" class="card-img-top" style="height: 200px; object-fit: contain; padding: 20px;" alt="{{ $org->name }}">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <i class="bi bi-building text-secondary" style="font-size: 4rem;"></i>
                            </div>
                        @endif
                        <div class="card-body">
                            <div class="mb-2">
                                <span class="badge bg-info">{{ $org->category->name }}</span>
                                @if($org->is_verified)
                                    <span class="badge bg-success"><i class="bi bi-check-circle"></i> Verified</span>
                                @else
                                    <span class="badge bg-warning"><i class="bi bi-hourglass"></i> Pending</span>
                                @endif
                            </div>
                            <h5 class="card-title">{{ $org->name }}</h5>
                            <p class="card-text text-muted small">{{ Str::limit($org->description, 100) }}</p>
                            
                            <div class="row text-center mb-3">
                                <div class="col-6">
                                    <div class="border rounded p-2">
                                        <h6 class="mb-0">{{ $org->events->count() }}</h6>
                                        <small class="text-muted">Events</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="border rounded p-2">
                                        <h6 class="mb-0">{{ $org->followers->count() }}</h6>
                                        <small class="text-muted">Followers</small>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="{{ route('organizer.organizations.show', $org) }}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-eye"></i> View Details
                                </a>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('organizer.organizations.edit', $org) }}" class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="{{ route('organizations.show', $org) }}" target="_blank" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-box-arrow-up-right"></i> Public View
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="text-center py-5">
            <i class="bi bi-building" style="font-size: 5rem; color: #ddd;"></i>
            <h3 class="mt-3">No Organizations Yet</h3>
            <p class="text-muted">Create your first organization to start hosting events</p>
            <a href="{{ route('organizer.organizations.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-1"></i>Create Your First Organization
            </a>
        </div>
    @endif
@endsection

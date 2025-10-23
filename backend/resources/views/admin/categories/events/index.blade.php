@extends('layouts.admin')

@section('title', 'Event Categories')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2><i class="bi bi-calendar-check-fill text-success me-2"></i>Event Categories</h2>
                    <p class="text-muted">Manage categories for events</p>
                </div>
                <a href="{{ route('admin.event-categories.create') }}" class="btn btn-success">
                    <i class="bi bi-plus-circle me-1"></i>Add Category
                </a>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Categories Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if($categories->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 5%;">ID</th>
                                <th style="width: 40%;">Category Name</th>
                                <th style="width: 20%;" class="text-center">Events</th>
                                <th style="width: 20%;">Created</th>
                                <th style="width: 15%;" class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $category)
                                <tr>
                                    <td><strong>#{{ $category->id }}</strong></td>
                                    <td>
                                        <i class="bi bi-calendar-event text-success me-2"></i>
                                        <strong>{{ $category->name }}</strong>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-success">
                                            {{ $category->events_count }} event(s)
                                        </span>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar me-1"></i>
                                            {{ $category->created_at->format('M d, Y') }}
                                        </small>
                                    </td>
                                    <td class="text-end">
                                        <div class="btn-group btn-group-sm">
                                            <a href="{{ route('admin.event-categories.edit', $category) }}" 
                                               class="btn btn-outline-primary" 
                                               title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('admin.event-categories.destroy', $category) }}" 
                                                  method="POST" 
                                                  onsubmit="return confirm('Are you sure you want to delete this category?')"
                                                  class="d-inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" 
                                                        class="btn btn-outline-danger" 
                                                        title="Delete"
                                                        {{ $category->events_count > 0 ? 'disabled' : '' }}>
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $categories->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="bi bi-calendar-event" style="font-size: 4rem; opacity: 0.2;"></i>
                    <h5 class="mt-3">No Categories Yet</h5>
                    <p class="text-muted">Start by creating your first event category</p>
                    <a href="{{ route('admin.event-categories.create') }}" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i>Add Category
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

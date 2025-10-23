@extends('layouts.admin')

@section('title', 'Edit Event Category')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <a href="{{ route('admin.event-categories.index') }}" class="btn btn-outline-secondary mb-3">
                <i class="bi bi-arrow-left me-1"></i>Back to Categories
            </a>
            <h2><i class="bi bi-pencil text-warning me-2"></i>Edit Event Category</h2>
            <p class="text-muted">Update category information</p>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('admin.event-categories.update', $eventCategory) }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="name" class="form-label fw-bold">
                                Category Name <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg @error('name') is-invalid @enderror" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name', $eventCategory->name) }}" 
                                   placeholder="e.g., Beach Cleanup, Tree Planting, Recycling Drive, etc."
                                   required
                                   autofocus>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Choose a clear, descriptive name (max 120 characters)
                            </div>
                        </div>

                        <!-- Usage Information -->
                        <div class="alert {{ $eventsCount > 0 ? 'alert-warning' : 'alert-info' }}">
                            <i class="bi bi-calendar-event me-2"></i>
                            <strong>Current Usage:</strong> This category is used by 
                            <strong>{{ $eventsCount }}</strong> event(s).
                            @if($eventsCount > 0)
                                <br><small>⚠️ Changing the name will affect all linked events.</small>
                            @endif
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-between">
                            <form action="{{ route('admin.event-categories.destroy', $eventCategory) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this category?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-outline-danger"
                                        {{ $eventsCount > 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-trash me-1"></i>Delete Category
                                </button>
                            </form>
                            
                            <div class="d-flex gap-2">
                                <a href="{{ route('admin.event-categories.index') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-x-circle me-1"></i>Cancel
                                </a>
                                <button type="submit" class="btn btn-success btn-lg">
                                    <i class="bi bi-check-circle me-1"></i>Update Category
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

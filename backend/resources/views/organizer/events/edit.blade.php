@extends('layouts.organizer')

@section('title', 'Edit Event')
@section('page-title', 'Edit Event')
@section('page-subtitle', $event->title)

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Event: {{ $event->title }}</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('organizer.events.update', $event) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <!-- Event Basic Information -->
                    <h6 class="border-bottom pb-2 mb-3"><i class="bi bi-info-circle me-2"></i>Event Information</h6>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Event Title <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               name="title" 
                               value="{{ old('title', $event->title) }}" 
                               placeholder="Enter event title"
                               required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('event_category_id') is-invalid @enderror" name="event_category_id" required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('event_category_id', $event->event_category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('event_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Event Type <span class="text-danger">*</span></label>
                            <select class="form-select @error('type') is-invalid @enderror" name="type" id="event_type" required>
                                <option value="">Select event type</option>
                                <option value="online" {{ old('type', $event->type) == 'online' ? 'selected' : '' }}>Online</option>
                                <option value="onsite" {{ old('type', $event->type) == 'onsite' ? 'selected' : '' }}>On-site</option>
                                <option value="hybrid" {{ old('type', $event->type) == 'hybrid' ? 'selected' : '' }}>Hybrid (Online + On-site)</option>
                            </select>
                            @error('type')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" 
                                  rows="5" 
                                  placeholder="Describe your event in detail...">{{ old('description', $event->description) }}</textarea>
                        <small class="text-muted">Tell participants what this event is about, what they'll learn or do, and why they should attend.</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Date & Time -->
                    <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="bi bi-calendar-event me-2"></i>Date & Time</h6>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Start Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" 
                                   class="form-control @error('start_at') is-invalid @enderror" 
                                   name="start_at" 
                                   value="{{ old('start_at', $event->start_at->format('Y-m-d\TH:i')) }}" 
                                   required>
                            @error('start_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">End Date & Time</label>
                            <input type="datetime-local" 
                                   class="form-control @error('end_at') is-invalid @enderror" 
                                   name="end_at" 
                                   value="{{ old('end_at', $event->end_at ? $event->end_at->format('Y-m-d\TH:i') : '') }}">
                            <small class="text-muted">Optional - leave blank if single session</small>
                            @error('end_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Maximum Participants</label>
                        <input type="number" 
                               class="form-control @error('max_participants') is-invalid @enderror" 
                               name="max_participants" 
                               value="{{ old('max_participants', $event->max_participants) }}" 
                               min="1" 
                               placeholder="e.g., 50">
                        <small class="text-muted">Leave blank for unlimited participants</small>
                        @error('max_participants')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Online Meeting Details -->
                    <div id="online_section" style="display: none;">
                        <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="bi bi-camera-video me-2"></i>Online Meeting Details</h6>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Meeting URL</label>
                            <input type="url" 
                                   class="form-control @error('meeting_url') is-invalid @enderror" 
                                   name="meeting_url" 
                                   value="{{ old('meeting_url', $event->meeting_url) }}" 
                                   placeholder="https://zoom.us/j/123456789 or https://meet.google.com/...">
                            <small class="text-muted">Provide the link for online participants to join</small>
                            @error('meeting_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Location Details -->
                    <div id="location_section" style="display: none;">
                        <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="bi bi-geo-alt me-2"></i>Event Location</h6>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Address</label>
                            <input type="text" 
                                   class="form-control @error('address') is-invalid @enderror" 
                                   name="address" 
                                   value="{{ old('address', $event->address) }}" 
                                   placeholder="Street address">
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">City</label>
                                <input type="text" 
                                       class="form-control @error('city') is-invalid @enderror" 
                                       name="city" 
                                       value="{{ old('city', $event->city) }}" 
                                       placeholder="City">
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Region</label>
                                <input type="text" 
                                       class="form-control @error('region') is-invalid @enderror" 
                                       name="region" 
                                       value="{{ old('region', $event->region) }}" 
                                       placeholder="Region">
                                @error('region')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-bold">Zip Code</label>
                                <input type="text" 
                                       class="form-control @error('zipcode') is-invalid @enderror" 
                                       name="zipcode" 
                                       value="{{ old('zipcode', $event->zipcode) }}" 
                                       placeholder="Postal code">
                                @error('zipcode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Event Poster -->
                    <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="bi bi-image me-2"></i>Event Poster</h6>

                    @if($event->poster_path)
                        <div class="mb-2">
                            <img src="{{ Storage::url($event->poster_path) }}" alt="Current Poster" style="max-height: 200px; border-radius: 8px;">
                            <small class="d-block text-muted">Current poster</small>
                        </div>
                    @endif

                    <div class="mb-4">
                        <label class="form-label fw-bold">Upload New Event Poster</label>
                        <input type="file" 
                               class="form-control @error('poster_path') is-invalid @enderror" 
                               name="poster_path" 
                               accept="image/*">
                        <small class="text-muted">Upload a new poster to replace the current one (JPG, PNG, max 2MB)</small>
                        @error('poster_path')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Submit Buttons -->
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('organizer.events.show', $event) }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Show/hide sections based on event type
    document.getElementById('event_type').addEventListener('change', function() {
        const type = this.value;
        const onlineSection = document.getElementById('online_section');
        const locationSection = document.getElementById('location_section');

        if (type === 'online') {
            onlineSection.style.display = 'block';
            locationSection.style.display = 'none';
        } else if (type === 'onsite') {
            onlineSection.style.display = 'none';
            locationSection.style.display = 'block';
        } else if (type === 'hybrid') {
            onlineSection.style.display = 'block';
            locationSection.style.display = 'block';
        } else {
            onlineSection.style.display = 'none';
            locationSection.style.display = 'none';
        }
    });

    // Trigger change event on page load to show appropriate sections
    document.addEventListener('DOMContentLoaded', function() {
        const typeSelect = document.getElementById('event_type');
        if (typeSelect.value) {
            typeSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection

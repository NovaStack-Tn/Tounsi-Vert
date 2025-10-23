@extends('layouts.public')

@section('title', 'Submit Report')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0"><i class="bi bi-flag-fill me-2"></i>Submit a Report</h4>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Report Guidelines:</strong> Please provide accurate and detailed information. We take all reports seriously and will review them promptly.
                    </div>

                    <form action="{{ route('reports.store') }}" method="POST">
                        @csrf

                        @if(isset($organizationId))
                            <input type="hidden" name="organization_id" value="{{ $organizationId }}">
                            <div class="alert alert-warning mb-4">
                                <i class="bi bi-building me-2"></i>You are reporting an <strong>Organization</strong>
                            </div>
                        @endif

                        @if(isset($eventId))
                            <input type="hidden" name="event_id" value="{{ $eventId }}">
                            <div class="alert alert-warning mb-4">
                                <i class="bi bi-calendar-event me-2"></i>You are reporting an <strong>Event</strong>
                            </div>
                        @endif

                        <!-- Category -->
                        <div class="mb-4">
                            <label for="category" class="form-label fw-bold">
                                <i class="bi bi-tag text-danger me-2"></i>Report Category <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('category') is-invalid @enderror" id="category" name="category" required>
                                <option value="">Select a category...</option>
                                <option value="spam">Spam</option>
                                <option value="inappropriate">Inappropriate Content</option>
                                <option value="fraud">Fraud/Scam</option>
                                <option value="harassment">Harassment</option>
                                <option value="violence">Violence</option>
                                <option value="misinformation">Misinformation</option>
                                <option value="copyright">Copyright Violation</option>
                                <option value="other">Other</option>
                            </select>
                            @error('category')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Priority -->
                        <div class="mb-4">
                            <label for="priority" class="form-label fw-bold">
                                <i class="bi bi-exclamation-circle text-warning me-2"></i>Priority Level
                            </label>
                            <select class="form-select @error('priority') is-invalid @enderror" id="priority" name="priority">
                                <option value="low">Low - Minor issue</option>
                                <option value="medium" selected>Medium - Needs attention</option>
                                <option value="high">High - Serious concern</option>
                                <option value="critical">Critical - Immediate action required</option>
                            </select>
                            @error('priority')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Reason -->
                        <div class="mb-4">
                            <label for="reason" class="form-label fw-bold">
                                <i class="bi bi-exclamation-triangle text-danger me-2"></i>Brief Summary <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('reason') is-invalid @enderror" 
                                   id="reason" 
                                   name="reason" 
                                   maxlength="200"
                                   placeholder="Briefly describe the issue (max 200 characters)"
                                   value="{{ old('reason') }}"
                                   required>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Details -->
                        <div class="mb-4">
                            <label for="details" class="form-label fw-bold">
                                <i class="bi bi-text-left text-primary me-2"></i>Additional Details
                            </label>
                            <textarea class="form-control @error('details') is-invalid @enderror" 
                                      id="details" 
                                      name="details" 
                                      rows="6" 
                                      placeholder="Please provide more information about your report...">{{ old('details') }}</textarea>
                            @error('details')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">The more details you provide, the better we can address the issue.</small>
                        </div>

                        <!-- Privacy Notice -->
                        <div class="alert alert-secondary mb-4">
                            <i class="bi bi-shield-check me-2"></i>
                            <strong>Privacy:</strong> Your report will be kept confidential and reviewed by our team.
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-danger btn-lg flex-grow-1">
                                <i class="bi bi-send me-2"></i>Submit Report
                            </button>
                            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-lg">
                                <i class="bi bi-x-circle me-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

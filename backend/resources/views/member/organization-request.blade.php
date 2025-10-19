@extends('layouts.public')

@section('title', 'Request Organization')

@section('content')
<div class="bg-primary-custom text-white py-4">
    <div class="container">
        <h1><i class="bi bi-building-add"></i> Request Organization</h1>
        <p class="mb-0">Apply to become an organizer and create your organization</p>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Organization Application Form</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>What happens next?</strong>
                        <ol class="mb-0 mt-2">
                            <li>Submit your organization details</li>
                            <li>Our admin team will review your application</li>
                            <li>Once approved, you'll become an organizer</li>
                            <li>You can then create and manage events</li>
                        </ol>
                    </div>

                    <form method="POST" action="{{ route('organization-request.store') }}">
                        @csrf

                        <h6 class="border-bottom pb-2 mb-3">Organization Information</h6>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Organization Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('organization_name') is-invalid @enderror" 
                                   name="organization_name" 
                                   value="{{ old('organization_name') }}" 
                                   placeholder="Enter organization name"
                                   required>
                            @error('organization_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('category_id') is-invalid @enderror" name="category_id" required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      name="description" 
                                      rows="4" 
                                      placeholder="Describe your organization's mission and activities"
                                      required>{{ old('description') }}</textarea>
                            <small class="text-muted">Max 1000 characters</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <h6 class="border-bottom pb-2 mb-3 mt-4">Location Information</h6>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Address <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('address') is-invalid @enderror" 
                                   name="address" 
                                   value="{{ old('address') }}" 
                                   placeholder="Street address"
                                   required>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">City <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('city') is-invalid @enderror" 
                                       name="city" 
                                       value="{{ old('city') }}" 
                                       placeholder="City"
                                       required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-bold">Region <span class="text-danger">*</span></label>
                                <input type="text" 
                                       class="form-control @error('region') is-invalid @enderror" 
                                       name="region" 
                                       value="{{ old('region') }}" 
                                       placeholder="Region"
                                       required>
                                @error('region')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Zip Code <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('zipcode') is-invalid @enderror" 
                                   name="zipcode" 
                                   value="{{ old('zipcode') }}" 
                                   placeholder="Postal code"
                                   required>
                            @error('zipcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <h6 class="border-bottom pb-2 mb-3 mt-4">Contact Information</h6>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Phone Number</label>
                            <input type="tel" 
                                   class="form-control @error('phone_number') is-invalid @enderror" 
                                   name="phone_number" 
                                   value="{{ old('phone_number') }}" 
                                   placeholder="+216 XX XXX XXX">
                            @error('phone_number')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Website</label>
                            <input type="url" 
                                   class="form-control @error('website') is-invalid @enderror" 
                                   name="website" 
                                   value="{{ old('website') }}" 
                                   placeholder="https://example.com">
                            @error('website')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Important:</strong> Please ensure all information is accurate. False information may result in rejection.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-send me-2"></i>Submit Application
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

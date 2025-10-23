@extends('layouts.organizer')

@section('title', 'Create Organization')
@section('page-title', 'Create Organization')
@section('page-subtitle', 'Register your organization')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #2d6a4f 0%, #40916c 100%);">
                <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Create New Organization</h5>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Note:</strong> Your organization will be pending approval until verified by an administrator.
                </div>

                <form method="POST" action="{{ route('organizer.organizations.store') }}" enctype="multipart/form-data">
                    @csrf

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Organization Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   name="name" 
                                   value="{{ old('name') }}" 
                                   required
                                   placeholder="Enter organization name">
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('org_category_id') is-invalid @enderror" name="org_category_id" required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('org_category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('org_category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Description</label>
                        <textarea class="form-control @error('description') is-invalid @enderror" 
                                  name="description" 
                                  rows="4" 
                                  placeholder="Describe your organization's mission and activities...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="bi bi-geo-alt me-2"></i>Location Information</h6>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Address</label>
                        <input type="text" 
                               class="form-control @error('address') is-invalid @enderror" 
                               name="address" 
                               value="{{ old('address') }}" 
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
                                   value="{{ old('city') }}" 
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
                                   value="{{ old('region') }}" 
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
                                   value="{{ old('zipcode') }}" 
                                   placeholder="Postal code">
                            @error('zipcode')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3 mt-4"><i class="bi bi-telephone me-2"></i>Contact Information</h6>

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
                        <label class="form-label fw-bold">Organization Logo</label>
                        <input type="file" 
                               class="form-control @error('logo_path') is-invalid @enderror" 
                               name="logo_path" 
                               accept="image/*">
                        <small class="text-muted">Upload your organization logo (JPG, PNG, max 2MB)</small>
                        @error('logo_path')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('organizer.dashboard') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-circle me-2"></i>Create Organization
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

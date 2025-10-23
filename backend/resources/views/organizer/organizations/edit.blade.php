@extends('layouts.organizer')

@section('title', 'Edit Organization')
@section('page-title', 'Edit Organization')
@section('page-subtitle', 'Update your organization details')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #2d6a4f 0%, #40916c 100%);">
                <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Organization Details</h5>
            </div>
            <div class="card-body p-4">
                <form method="POST" action="{{ route('organizer.organizations.update') }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Organization Name <span class="text-danger">*</span></label>
                            <input type="text" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   name="name" 
                                   value="{{ old('name', $organization->name) }}" 
                                   required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-bold">Category <span class="text-danger">*</span></label>
                            <select class="form-select @error('org_category_id') is-invalid @enderror" name="org_category_id" required>
                                <option value="">Select a category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('org_category_id', $organization->org_category_id) == $category->id ? 'selected' : '' }}>
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
                                  placeholder="Describe your organization...">{{ old('description', $organization->description) }}</textarea>
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
                               value="{{ old('address', $organization->address) }}" 
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
                                   value="{{ old('city', $organization->city) }}" 
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
                                   value="{{ old('region', $organization->region) }}" 
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
                                   value="{{ old('zipcode', $organization->zipcode) }}" 
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
                               value="{{ old('phone_number', $organization->phone_number) }}" 
                               placeholder="+216 XX XXX XXX">
                        @error('phone_number')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Organization Logo</label>
                        @if($organization->logo_path)
                            <div class="mb-2">
                                <img src="{{ Storage::url($organization->logo_path) }}" alt="Current Logo" style="max-height: 100px; border-radius: 8px;">
                                <small class="d-block text-muted">Current logo</small>
                            </div>
                        @endif
                        <input type="file" 
                               class="form-control @error('logo_path') is-invalid @enderror" 
                               name="logo_path" 
                               accept="image/*">
                        <small class="text-muted">Upload a new logo to replace the current one (JPG, PNG, max 2MB)</small>
                        @error('logo_path')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="{{ route('organizer.organizations.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update Organization
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

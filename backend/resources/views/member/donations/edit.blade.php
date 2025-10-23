@extends('layouts.public')

@section('title', 'Edit Donation')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('donations.show', $donation) }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Donation
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="mb-0"><i class="bi bi-pencil"></i> Edit Donation #{{ $donation->id }}</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle"></i> <strong>Note:</strong> Only pending donations can be edited.
                    </div>

                    <form method="POST" action="{{ route('donations.update', $donation) }}">
                        @csrf
                        @method('PUT')

                        <!-- Organization Selection -->
                        <div class="mb-4">
                            <label for="organization_id" class="form-label">
                                Organization <span class="text-danger">*</span>
                            </label>
                            <select name="organization_id" id="organization_id" 
                                    class="form-select @error('organization_id') is-invalid @enderror" required>
                                <option value="">-- Select Organization --</option>
                                @foreach($organizations as $org)
                                    <option value="{{ $org->id }}" 
                                            {{ (old('organization_id') ?? $donation->organization_id) == $org->id ? 'selected' : '' }}>
                                        {{ $org->name }}
                                        @if($org->is_verified)
                                            ✓
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('organization_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Event Selection (Optional) -->
                        <div class="mb-4">
                            <label for="event_id" class="form-label">
                                Event <small class="text-muted">(Optional)</small>
                            </label>
                            <select name="event_id" id="event_id" class="form-select @error('event_id') is-invalid @enderror">
                                <option value="">-- General Donation --</option>
                                @foreach($events as $evt)
                                    <option value="{{ $evt->id }}" 
                                            {{ (old('event_id') ?? $donation->event_id) == $evt->id ? 'selected' : '' }}>
                                        {{ $evt->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('event_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Amount -->
                        <div class="mb-4">
                            <label for="amount" class="form-label">
                                Donation Amount (TND) <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control form-control-lg @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" min="1.01" max="1000000" step="0.01" 
                                   value="{{ old('amount') ?? $donation->amount }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum: 1.01 TND | Maximum: 1,000,000 TND</div>
                        </div>

                        <!-- Quick Amount Buttons -->
                        <div class="mb-4">
                            <h6>Quick amounts:</h6>
                            <div class="d-grid gap-2 d-md-flex">
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('amount').value = 10">10 TND</button>
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('amount').value = 25">25 TND</button>
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('amount').value = 50">50 TND</button>
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('amount').value = 100">100 TND</button>
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('amount').value = 250">250 TND</button>
                            </div>
                        </div>

                        <!-- Status (Read-only display) -->
                        <div class="mb-4">
                            <label class="form-label">Current Status</label>
                            <div>
                                <span class="badge bg-warning">{{ ucfirst($donation->status) }}</span>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-lg"></i> Update Donation
                            </button>
                            <a href="{{ route('donations.show', $donation) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

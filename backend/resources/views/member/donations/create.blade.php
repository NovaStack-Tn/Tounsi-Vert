@extends('layouts.public')

@section('title', 'Make a Donation')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <!-- Back Button -->
            <div class="mb-3">
                <a href="{{ route('donations.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Donations
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="bi bi-heart-fill"></i> Make a Donation</h4>
                </div>
                <div class="card-body">
                    @if(isset($event))
                        <div class="alert alert-info">
                            <strong>Event:</strong> {{ $event->title }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('donations.store') }}">
                        @csrf

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
                                            {{ (old('organization_id') ?? ($event->organization_id ?? '')) == $org->id ? 'selected' : '' }}>
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
                                            {{ (old('event_id') ?? ($event->id ?? '')) == $evt->id ? 'selected' : '' }}>
                                        {{ $evt->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('event_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Select an event if you want to donate to a specific event, or leave empty for general donation.</div>
                        </div>

                        <!-- Amount -->
                        <div class="mb-4">
                            <label for="amount" class="form-label">
                                Donation Amount (TND) <span class="text-danger">*</span>
                            </label>
                            <input type="number" class="form-control form-control-lg @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" min="1.01" max="1000000" step="0.01" 
                                   value="{{ old('amount') }}" required>
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

                        <!-- Info Alert -->
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> <strong>Note:</strong> This is a demo. In production, this would integrate with a payment gateway.
                        </div>

                        <!-- Submit Buttons -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-heart-fill"></i> Complete Donation
                            </button>
                            <a href="{{ isset($event) ? route('events.show', $event) : route('donations.index') }}" 
                               class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

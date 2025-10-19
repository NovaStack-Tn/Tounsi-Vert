@extends('layouts.public')

@section('title', 'Make a Donation')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-primary-custom text-white">
                    <h4 class="mb-0"><i class="bi bi-heart-fill"></i> Make a Donation</h4>
                </div>
                <div class="card-body">
                    <h5 class="mb-3">{{ $event->title }}</h5>
                    <p class="text-muted">Support this event with your donation</p>

                    <form method="POST" action="{{ route('donations.store', $event) }}">
                        @csrf
                        <div class="mb-4">
                            <label for="amount" class="form-label">Donation Amount (TND)</label>
                            <input type="number" class="form-control form-control-lg @error('amount') is-invalid @enderror" 
                                   id="amount" name="amount" min="1" max="10000" step="0.01" 
                                   value="{{ old('amount') }}" required>
                            @error('amount')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">Minimum: 1 TND | Maximum: 10,000 TND</div>
                        </div>

                        <div class="mb-4">
                            <h6>Quick amounts:</h6>
                            <div class="d-grid gap-2 d-md-flex">
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('amount').value = 10">10 TND</button>
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('amount').value = 25">25 TND</button>
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('amount').value = 50">50 TND</button>
                                <button type="button" class="btn btn-outline-primary" onclick="document.getElementById('amount').value = 100">100 TND</button>
                            </div>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> <strong>Note:</strong> This is a demo. In production, this would integrate with a payment gateway.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-heart-fill"></i> Complete Donation
                            </button>
                            <a href="{{ route('events.show', $event) }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

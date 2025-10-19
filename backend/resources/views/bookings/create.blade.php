@extends('layouts.public')

@section('title', 'Book Vehicule')

@section('content')
<div class="bg-primary-custom text-white py-4">
    <div class="container">
        <h1><i class="bi bi-calendar2-check"></i> Book {{ $vehicule->model ?? 'Vehicule' }}</h1>
        <p class="mb-0">Plan your trip and request this vehicle</p>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Booking Form</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('bookings.store') }}" id="bookingForm">
                        @csrf

                        <input type="hidden" name="vehicule_id" value="{{ $vehicule->id }}">

                        <div class="mb-3">
                            <label class="form-label fw-bold">Pickup Location <span class="text-danger">*</span></label>
                            <input type="text" name="pickup_location" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Dropoff Location <span class="text-danger">*</span></label>
                            <input type="text" name="dropoff_location" class="form-control" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Scheduled Time</label>
                            <input type="datetime-local" name="scheduled_time" class="form-control">
                            <small class="text-muted">Available: {{ $vehicule->availability_start }} → {{ $vehicule->availability_end }}</small>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Notes</label>
                            <textarea name="notes" rows="3" class="form-control" placeholder="Any special instructions?"></textarea>
                        </div>

                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i> Booking will be pending until confirmed by owner.
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg"><i class="bi bi-send"></i> Submit Booking</button>
                            <a href="{{ route('vehicules.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('bookingForm').addEventListener('submit', e => {
    const requiredFields = e.target.querySelectorAll('input[required]');
    let valid = true;
    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            valid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    if (!valid) e.preventDefault();
});
</script>
@endsection

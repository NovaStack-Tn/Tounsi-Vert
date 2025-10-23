@extends('layouts.public')

@section('title', 'Quick Match Vehicle')

@section('content')
<div class="bg-success text-white py-4">
    <div class="container">
        <h1><i class="bi bi-search"></i> Quick Match</h1>
        <p class="mb-0">Describe your need and find the best vehicle quickly</p>
    </div>
</div>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-clipboard-check"></i> Quick Match Form</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('bookings.quickMatch') }}" id="quickMatchForm">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-bold">Describe your need</label>
                            <input type="text" name="prompt" class="form-control" placeholder="E.g., Vehicle for elderly people this weekend" required>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg"><i class="bi bi-search"></i> Quick Match</button>
                            <a href="{{ route('vehicules.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
@if(isset($emailSent) && $emailSent)
    <div class="alert alert-success mt-4">
        ✅ A successful match was found and an email was sent to the owner: <strong>{{ $match->owner->email }}</strong>
    </div>
@endif


        </div>
    </div>
</div>

<script>
document.getElementById('quickMatchForm').addEventListener('submit', async e => {
    e.preventDefault();

    const form = e.target;
    const container = form.closest('.card-body');

    form.style.display = 'none';

    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'text-center py-4';
    loadingDiv.innerHTML = `
        <div class="spinner-border text-success mb-3" role="status"></div>
        <p class="fw-bold">Finding the best match for you...</p>
    `;
    container.appendChild(loadingDiv);

    await new Promise(r => setTimeout(r, 2000));

    form.submit();
});
</script>
@endsection

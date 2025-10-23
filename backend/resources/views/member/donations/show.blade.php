@extends('layouts.public')

@section('title', 'Donation Details')

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

            <!-- Success/Error Messages -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Donation Details Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="bi bi-receipt"></i> Donation #{{ $donation->id }}</h4>
                    @php
                        $statusClasses = [
                            'pending' => 'warning',
                            'succeeded' => 'success',
                            'failed' => 'danger',
                            'refunded' => 'secondary'
                        ];
                    @endphp
                    <span class="badge bg-{{ $statusClasses[$donation->status] ?? 'secondary' }} fs-6">
                        {{ ucfirst($donation->status) }}
                    </span>
                </div>
                <div class="card-body">
                    <!-- Amount -->
                    <div class="row mb-4">
                        <div class="col-12 text-center">
                            <h2 class="display-3 text-success">{{ number_format($donation->amount, 2) }} TND</h2>
                            <p class="text-muted">Donation Amount</p>
                        </div>
                    </div>

                    <hr>

                    <!-- Donation Information -->
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Organization</h6>
                            @if($donation->organization)
                                <p class="mb-0">
                                    <a href="{{ route('organizations.show', $donation->organization) }}" class="text-decoration-none">
                                        <strong>{{ $donation->organization->name }}</strong>
                                    </a>
                                </p>
                                @if($donation->organization->is_verified)
                                    <span class="badge bg-success mt-1">
                                        <i class="bi bi-check-circle"></i> Verified
                                    </span>
                                @endif
                            @else
                                <p class="text-muted">N/A</p>
                            @endif
                        </div>

                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Event</h6>
                            @if($donation->event)
                                <p class="mb-0">
                                    <a href="{{ route('events.show', $donation->event) }}" class="text-decoration-none">
                                        <strong>{{ $donation->event->title }}</strong>
                                    </a>
                                </p>
                            @else
                                <p class="text-muted">General Donation</p>
                            @endif
                        </div>

                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Payment Reference</h6>
                            <p>
                                @if($donation->payment_ref)
                                    <code>{{ $donation->payment_ref }}</code>
                                @else
                                    <em class="text-muted">Not available</em>
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Paid At</h6>
                            <p>
                                @if($donation->paid_at)
                                    {{ $donation->paid_at->format('M d, Y h:i A') }}
                                @else
                                    <em class="text-muted">Not paid yet</em>
                                @endif
                            </p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Created At</h6>
                            <p>{{ $donation->created_at->format('M d, Y h:i A') }}</p>
                        </div>

                        <div class="col-md-6 mb-3">
                            <h6 class="text-muted">Last Updated</h6>
                            <p>{{ $donation->updated_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex gap-2 mt-4">
                        @if($donation->status === 'pending')
                            <a href="{{ route('donations.edit', $donation) }}" class="btn btn-warning">
                                <i class="bi bi-pencil"></i> Edit Donation
                            </a>
                        @endif

                        @if(in_array($donation->status, ['pending', 'failed']))
                            <form action="{{ route('donations.destroy', $donation) }}" method="POST" 
                                  onsubmit="return confirm('Are you sure you want to delete this donation?')" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash"></i> Delete
                                </button>
                            </form>
                        @endif

                        @if($donation->organization)
                            <a href="{{ route('organizations.show', $donation->organization) }}" class="btn btn-outline-primary">
                                <i class="bi bi-building"></i> View Organization
                            </a>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Additional Info Card -->
            @if($donation->status === 'succeeded')
                <div class="card mt-3 border-success">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-check-circle-fill text-success me-3" style="font-size: 2rem;"></i>
                            <div>
                                <h5 class="mb-1">Thank you for your donation!</h5>
                                <p class="mb-0 text-muted">Your contribution helps make a difference in our community.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($donation->status === 'pending')
                <div class="card mt-3 border-warning">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-clock-fill text-warning me-3" style="font-size: 2rem;"></i>
                            <div>
                                <h5 class="mb-1">Payment Pending</h5>
                                <p class="mb-0 text-muted">This donation is awaiting payment confirmation.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @elseif($donation->status === 'failed')
                <div class="card mt-3 border-danger">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-x-circle-fill text-danger me-3" style="font-size: 2rem;"></i>
                            <div>
                                <h5 class="mb-1">Payment Failed</h5>
                                <p class="mb-0 text-muted">This donation payment was unsuccessful. You may try again.</p>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

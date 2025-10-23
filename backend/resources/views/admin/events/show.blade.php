@extends('layouts.admin')

@section('title', $event->title . ' - Event Details')

@section('content')
<style>
    .detail-card {
        border-left: 4px solid #28a745;
    }
    .user-avatar {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
    }
</style>

<div class="container-fluid">
    <!-- Header -->
    <div class="mb-4">
        <a href="{{ route('admin.events.index') }}" class="btn btn-outline-secondary mb-3">
            <i class="bi bi-arrow-left me-2"></i>Back to Events
        </a>
        <h2><i class="bi bi-calendar-event-fill text-success me-2"></i>{{ $event->title }}</h2>
        <p class="text-muted">Complete event details and statistics</p>
    </div>

    <!-- Event Header -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-3">
                    @if($event->image_path)
                        <img src="{{ Storage::url($event->image_path) }}" alt="{{ $event->title }}" class="img-fluid rounded">
                    @else
                        <div class="bg-success text-white rounded d-flex align-items-center justify-content-center" style="width: 100%; height: 200px;">
                            <i class="bi bi-calendar-event" style="font-size: 5rem;"></i>
                        </div>
                    @endif
                </div>
                <div class="col-md-9">
                    <div class="mb-3">
                        @if($event->is_published)
                            <span class="badge bg-success px-3 py-2"><i class="bi bi-check-circle me-1"></i>Published</span>
                        @else
                            <span class="badge bg-secondary px-3 py-2"><i class="bi bi-pencil me-1"></i>Draft</span>
                        @endif
                        <span class="badge bg-primary px-3 py-2 ms-2"><i class="bi bi-tag me-1"></i>{{ $event->category->name }}</span>
                    </div>
                    <h3>{{ $event->title }}</h3>
                    <p class="text-muted">{{ $event->description }}</p>
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <p class="mb-2"><i class="bi bi-building text-primary me-2"></i><strong>Organization:</strong> {{ $event->organization->name }}</p>
                            <p class="mb-2"><i class="bi bi-geo-alt text-danger me-2"></i><strong>Location:</strong> {{ $event->location }}</p>
                            <p class="mb-2"><i class="bi bi-calendar text-success me-2"></i><strong>Start:</strong> {{ $event->start_at->format('M d, Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-2"><i class="bi bi-calendar-check text-warning me-2"></i><strong>End:</strong> {{ $event->end_at->format('M d, Y H:i') }}</p>
                            <p class="mb-2"><i class="bi bi-people text-info me-2"></i><strong>Capacity:</strong> {{ $event->max_attendees ?? 'Unlimited' }}</p>
                            <p class="mb-2"><i class="bi bi-clock text-secondary me-2"></i><strong>Created:</strong> {{ $event->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <h3 class="mb-0">{{ $stats['total_attendees'] }}</h3>
                    <small>Total Attendees</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);">
                <div class="card-body text-white">
                    <h3 class="mb-0">{{ number_format($stats['average_rating'], 1) }}</h3>
                    <small>Average Rating ({{ $stats['total_reviews'] }} reviews)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <div class="card-body text-white">
                    <h3 class="mb-0">${{ number_format($stats['total_donations'], 2) }}</h3>
                    <small>Total Donations</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center" style="background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);">
                <div class="card-body text-white">
                    <h3 class="mb-0">{{ $stats['total_donors'] }}</h3>
                    <small>Total Donors</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Reviews -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0"><i class="bi bi-star-fill me-2"></i>Reviews ({{ $event->reviews->count() }})</h5>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    @forelse($event->reviews as $review)
                        <div class="detail-card bg-light p-3 rounded mb-3">
                            <div class="d-flex align-items-start">
                                <div class="user-avatar me-3">
                                    {{ strtoupper(substr($review->user->first_name, 0, 1)) }}{{ strtoupper(substr($review->user->last_name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $review->user->full_name }}</h6>
                                    <div class="mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star{{ $i <= $review->rate ? '-fill' : '' }} text-warning"></i>
                                        @endfor
                                        <small class="text-muted ms-2">{{ $review->created_at->diffForHumans() }}</small>
                                    </div>
                                    @if($review->comment)
                                        <p class="mb-0 text-muted small">{{ $review->comment }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">No reviews yet</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Attendees -->
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Attendees ({{ $event->participations->count() }})</h5>
                </div>
                <div class="card-body" style="max-height: 500px; overflow-y: auto;">
                    @forelse($event->participations as $participation)
                        <div class="detail-card bg-light p-3 rounded mb-3">
                            <div class="d-flex align-items-center">
                                <div class="user-avatar me-3">
                                    {{ strtoupper(substr($participation->user->first_name, 0, 1)) }}{{ strtoupper(substr($participation->user->last_name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $participation->user->full_name }}</h6>
                                    <p class="mb-0 text-muted small">
                                        <i class="bi bi-envelope me-1"></i>{{ $participation->user->email }}
                                    </p>
                                </div>
                                <div>
                                    <span class="badge bg-info">
                                        <i class="bi bi-star-fill me-1"></i>{{ $participation->user->score }} pts
                                    </span>
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">No attendees yet</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Donations -->
        <div class="col-md-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-cash-stack me-2"></i>Donations ({{ $event->donations->count() }}) - Total: ${{ number_format($stats['total_donations'], 2) }}</h5>
                </div>
                <div class="card-body">
                    @forelse($event->donations as $donation)
                        <div class="detail-card bg-light p-3 rounded mb-3">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <div class="user-avatar me-3">
                                            {{ strtoupper(substr($donation->participation->user->first_name, 0, 1)) }}{{ strtoupper(substr($donation->participation->user->last_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $donation->participation->user->full_name }}</h6>
                                            <p class="mb-0 text-muted small">
                                                <i class="bi bi-envelope me-1"></i>{{ $donation->participation->user->email }}
                                            </p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <h4 class="text-success mb-0">${{ number_format($donation->amount, 2) }}</h4>
                                    <small class="text-muted">{{ $donation->paid_at ? $donation->paid_at->format('M d, Y') : $donation->created_at->format('M d, Y') }}</small>
                                </div>
                                <div class="col-md-3 text-end">
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="bi bi-check-circle me-1"></i>Succeeded
                                    </span>
                                    @if($donation->payment_ref)
                                        <small class="d-block text-muted mt-1">Ref: {{ $donation->payment_ref }}</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">No donations yet</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

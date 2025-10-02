@extends('layouts.public')

@section('title', $event->title)

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mb-4">
            <!-- Event Details -->
            <div class="card shadow-sm mb-4">
                @if($event->poster_path)
                    <img src="{{ Storage::url($event->poster_path) }}" class="card-img-top" alt="{{ $event->title }}" style="max-height: 400px; object-fit: cover;">
                @endif
                <div class="card-body">
                    <div class="mb-3">
                        <span class="badge bg-primary">{{ $event->category->name }}</span>
                        <span class="badge bg-secondary">{{ ucfirst($event->type) }}</span>
                        @if($event->is_published)
                            <span class="badge bg-success">Published</span>
                        @endif
                    </div>
                    <h1 class="card-title">{{ $event->title }}</h1>
                    <p class="text-muted">
                        <i class="bi bi-building"></i> By <a href="{{ route('organizations.show', $event->organization) }}">{{ $event->organization->name }}</a>
                    </p>
                    
                    <hr>
                    
                    <h5>Description</h5>
                    <p>{{ $event->description }}</p>
                    
                    <hr>
                    
                    <h5>Event Details</h5>
                    <ul class="list-unstyled">
                        <li><strong><i class="bi bi-calendar"></i> Start Date:</strong> {{ $event->start_at->format('F d, Y - H:i') }}</li>
                        @if($event->end_at)
                            <li><strong><i class="bi bi-calendar-check"></i> End Date:</strong> {{ $event->end_at->format('F d, Y - H:i') }}</li>
                        @endif
                        @if($event->type != 'online')
                            <li><strong><i class="bi bi-geo-alt"></i> Location:</strong> {{ $event->address }}, {{ $event->city }}, {{ $event->region }}</li>
                        @endif
                        @if($event->meeting_url)
                            <li><strong><i class="bi bi-camera-video"></i> Meeting URL:</strong> <a href="{{ $event->meeting_url }}" target="_blank">Join Online</a></li>
                        @endif
                        @if($event->max_participants)
                            <li><strong><i class="bi bi-people"></i> Max Participants:</strong> {{ $event->max_participants }} ({{ $attendeesCount }} attending)</li>
                        @endif
                    </ul>
                </div>
            </div>

            <!-- Reviews -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Reviews @if($averageRating) ({{ number_format($averageRating, 1) }} ⭐) @endif</h5>
                </div>
                <div class="card-body">
                    @auth
                        <form method="POST" action="{{ route('reviews.store', $event) }}" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Rating</label>
                                <select name="rate" class="form-select" required>
                                    <option value="">Select rating</option>
                                    <option value="5">5 - Excellent</option>
                                    <option value="4">4 - Good</option>
                                    <option value="3">3 - Average</option>
                                    <option value="2">2 - Poor</option>
                                    <option value="1">1 - Very Poor</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Comment</label>
                                <textarea name="comment" class="form-control" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit Review</button>
                        </form>
                    @endauth

                    @forelse($event->reviews as $review)
                        <div class="border-top pt-3 mt-3">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $review->user->full_name }}</strong>
                                <span class="text-warning">{{ str_repeat('⭐', $review->rate) }}</span>
                            </div>
                            <p class="text-muted small mb-1">{{ $review->created_at->diffForHumans() }}</p>
                            @if($review->comment)
                                <p class="mb-0">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @empty
                        <p class="text-muted">No reviews yet. Be the first to review!</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Action Buttons -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary-custom text-white">
                    <h5 class="mb-0">Take Action</h5>
                </div>
                <div class="card-body">
                    @auth
                        <form method="POST" action="{{ route('events.attend', $event) }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-success w-100 mb-2" @if($event->isFull()) disabled @endif>
                                <i class="bi bi-check-circle"></i> Join Event
                            </button>
                        </form>
                        
                        <form method="POST" action="{{ route('events.follow', $event) }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-outline-primary w-100 mb-2">
                                <i class="bi bi-bookmark"></i> Follow Event
                            </button>
                        </form>
                        
                        <a href="{{ route('donations.create', $event) }}" class="btn btn-warning w-100 mb-2">
                            <i class="bi bi-heart"></i> Donate
                        </a>
                        
                        <form method="POST" action="{{ route('events.share', $event) }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100">
                                <i class="bi bi-share"></i> Share Event
                            </button>
                        </form>
                    @else
                        <p class="text-muted">Please <a href="{{ route('login') }}">login</a> to participate in this event.</p>
                    @endauth
                </div>
            </div>

            <!-- Organization Card -->
            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0">Organized By</h5>
                </div>
                <div class="card-body text-center">
                    @if($event->organization->logo_path)
                        <img src="{{ Storage::url($event->organization->logo_path) }}" class="img-fluid mb-3" style="max-height: 150px;">
                    @endif
                    <h6>{{ $event->organization->name }}</h6>
                    @if($event->organization->is_verified)
                        <span class="badge bg-success"><i class="bi bi-check-circle"></i> Verified</span>
                    @endif
                    <p class="text-muted small mt-2">{{ Str::limit($event->organization->description, 100) }}</p>
                    <a href="{{ route('organizations.show', $event->organization) }}" class="btn btn-outline-primary btn-sm">View Profile</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

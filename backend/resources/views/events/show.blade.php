@extends('layouts.public')

@section('title', $event->title)

@section('content')
<style>
    .event-hero {
        position: relative;
        height: 400px;
        background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
        overflow: hidden;
    }
    .event-hero-bg {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.3;
    }
    .event-hero-content {
        position: relative;
        z-index: 2;
        color: white;
        padding: 60px 0;
    }
    .star-rating {
        display: inline-flex;
        flex-direction: row-reverse;
        gap: 5px;
    }
    .star-rating input {
        display: none;
    }
    .star-rating label {
        cursor: pointer;
        font-size: 2rem;
        color: #ddd;
        transition: all 0.2s ease;
    }
    .star-rating label:hover,
    .star-rating label:hover ~ label,
    .star-rating input:checked ~ label {
        color: #ffc107;
    }
    .review-card {
        border-left: 4px solid #2d6a4f;
        transition: all 0.3s ease;
    }
    .review-card:hover {
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        transform: translateX(5px);
    }
    .action-btn {
        transition: all 0.3s ease;
    }
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
</style>

<div class="event-hero">
    @if($event->poster_path)
        <img src="{{ Storage::url($event->poster_path) }}" class="event-hero-bg" alt="{{ $event->title }}">
    @endif
    <div class="event-hero-content">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="mb-3">
                        <span class="badge bg-light text-dark px-3 py-2">{{ $event->category->name }}</span>
                        <span class="badge bg-warning text-dark px-3 py-2">{{ ucfirst($event->type) }}</span>
                    </div>
                    <h1 class="display-4 fw-bold mb-3">{{ $event->title }}</h1>
                    <p class="lead mb-0">
                        <i class="bi bi-building me-2"></i>
                        By <a href="{{ route('organizations.show', $event->organization) }}" class="text-white text-decoration-underline">{{ $event->organization->name }}</a>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="bg-white text-dark rounded p-3 d-inline-block">
                        <div class="text-warning mb-1" style="font-size: 2rem;">
                            <i class="bi bi-star-fill"></i> {{ number_format($averageRating, 1) }}
                        </div>
                        <small class="text-muted">Average Rating</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mb-4">
            <!-- Event Details -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h3 class="mb-3"><i class="bi bi-info-circle-fill text-primary me-2"></i>About This Event</h3>
                    <p class="lead">{{ $event->description ?: 'No description provided.' }}</p>
                </div>
            </div>

            <!-- Event Information -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h3 class="mb-4"><i class="bi bi-calendar-event-fill text-primary me-2"></i>Event Details</h3>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-calendar3 text-primary me-3" style="font-size: 1.5rem;"></i>
                                <div>
                                    <strong>Start Date</strong><br>
                                    <span>{{ $event->start_at->format('F d, Y - H:i') }}</span>
                                </div>
                            </div>
                        </div>
                        @if($event->end_at)
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-calendar-check text-success me-3" style="font-size: 1.5rem;"></i>
                                <div>
                                    <strong>End Date</strong><br>
                                    <span>{{ $event->end_at->format('F d, Y - H:i') }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($event->type != 'online')
                        <div class="col-md-12">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-geo-alt text-danger me-3" style="font-size: 1.5rem;"></i>
                                <div>
                                    <strong>Location</strong><br>
                                    <span>{{ $event->address }}, {{ $event->city }}, {{ $event->region }}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($event->meeting_url)
                        <div class="col-md-12">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-camera-video text-info me-3" style="font-size: 1.5rem;"></i>
                                <div>
                                    <strong>Meeting URL</strong><br>
                                    <a href="{{ $event->meeting_url }}" target="_blank" class="btn btn-sm btn-outline-primary mt-1">
                                        <i class="bi bi-box-arrow-up-right"></i> Join Online Meeting
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endif
                        @if($event->max_participants)
                        <div class="col-md-6">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-people text-warning me-3" style="font-size: 1.5rem;"></i>
                                <div>
                                    <strong>Capacity</strong><br>
                                    <span>{{ $attendeesCount }} / {{ $event->max_participants }} joined</span>
                                    @if($event->isFull())
                                        <span class="badge bg-danger ms-2">Full</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Reviews -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 p-4">
                    <h3 class="mb-0">
                        <i class="bi bi-star-fill text-warning me-2"></i>
                        Reviews 
                        @if($averageRating)
                            <span class="text-warning">({{ number_format($averageRating, 1) }} ★)</span>
                        @endif
                    </h3>
                </div>
                <div class="card-body p-4">
                    @auth
                        @if(!$hasReviewed)
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-2"></i>
                                Share your experience! Write a review for this event.
                            </div>
                            <form method="POST" action="{{ route('reviews.store', $event) }}" class="mb-4 p-4 bg-light rounded">
                                @csrf
                                <div class="mb-4">
                                    <label class="form-label fw-bold">Your Rating</label>
                                    <div class="star-rating">
                                        <input type="radio" id="star5" name="rate" value="5" required />
                                        <label for="star5" title="5 stars"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" id="star4" name="rate" value="4" />
                                        <label for="star4" title="4 stars"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" id="star3" name="rate" value="3" />
                                        <label for="star3" title="3 stars"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" id="star2" name="rate" value="2" />
                                        <label for="star2" title="2 stars"><i class="bi bi-star-fill"></i></label>
                                        <input type="radio" id="star1" name="rate" value="1" />
                                        <label for="star1" title="1 star"><i class="bi bi-star-fill"></i></label>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">Your Comment</label>
                                    <textarea name="comment" class="form-control" rows="4" placeholder="Share your thoughts about this event..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-send me-2"></i>Submit Review
                                </button>
                            </form>
                        @else
                            <div class="alert alert-success">
                                <i class="bi bi-check-circle me-2"></i>
                                You have already reviewed this event. Thank you!
                            </div>
                        @endif
                    @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Please <a href="{{ route('login') }}" class="alert-link">login</a> to write a review.
                        </div>
                    @endauth

                    <div class="mt-4">
                        <h5 class="mb-3">All Reviews ({{ $reviews->total() }})</h5>
                        @forelse($reviews as $review)
                            <div class="review-card bg-light p-3 rounded mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($review->user->first_name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <strong>{{ $review->user->full_name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="text-warning">
                                            @for($i = 0; $i < $review->rate; $i++)
                                                <i class="bi bi-star-fill"></i>
                                            @endfor
                                            @for($i = $review->rate; $i < 5; $i++)
                                                <i class="bi bi-star"></i>
                                            @endfor
                                        </span>
                                        @auth
                                            @if($review->user_id === auth()->id())
                                                <form action="{{ route('reviews.destroy', $review) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this review?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Review">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        @endauth
                                    </div>
                                </div>
                                @if($review->comment)
                                    <p class="mb-0 ms-5">{{ $review->comment }}</p>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-4">
                                <i class="bi bi-chat-quote" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="text-muted mt-2">No reviews yet. Be the first to review!</p>
                            </div>
                        @endforelse

                        @if($reviews->hasPages())
                            <div class="mt-3">
                                {{ $reviews->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Action Buttons -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning-fill me-2"></i>Take Action</h5>
                </div>
                <div class="card-body p-3">
                    @auth
                        @if($hasJoined)
                            <form method="POST" action="{{ route('events.unjoin', $event) }}" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-outline-danger w-100 mb-2 action-btn">
                                    <i class="bi bi-x-circle me-2"></i>Leave Event
                                </button>
                            </form>
                            <div class="alert alert-success py-2 px-3 mb-3">
                                <i class="bi bi-check-circle me-2"></i>
                                <small>You have joined this event!</small>
                            </div>
                        @else
                            <form method="POST" action="{{ route('events.attend', $event) }}" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success w-100 mb-2 action-btn" @if($event->isFull()) disabled @endif>
                                    <i class="bi bi-check-circle me-2"></i>
                                    @if($event->isFull())
                                        Event Full
                                    @else
                                        Join Event
                                    @endif
                                </button>
                            </form>
                        @endif
                        
                        @if($hasDonated)
                            <div class="card bg-warning bg-opacity-10 border-warning mb-3">
                                <div class="card-body p-3">
                                    <div class="text-center mb-2">
                                        <i class="bi bi-heart-fill text-warning" style="font-size: 2rem;"></i>
                                    </div>
                                    <h6 class="text-center mb-2">Thank You for Your Generosity! 🙏</h6>
                                    <p class="text-center mb-2 small">
                                        Your total contribution to this event:
                                    </p>
                                    <div class="text-center mb-3">
                                        <span class="badge bg-warning text-dark px-3 py-2" style="font-size: 1.1rem;">
                                            ${{ number_format($totalDonated, 2) }}
                                        </span>
                                    </div>
                                    <p class="text-center text-muted small mb-2">
                                        Your support makes a real difference!
                                    </p>
                                    <a href="{{ route('donations.create', $event) }}" class="btn btn-warning w-100 action-btn">
                                        <i class="bi bi-heart-fill me-2"></i>Donate More
                                    </a>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('donations.create', $event) }}" class="btn btn-warning w-100 mb-2 action-btn">
                                <i class="bi bi-heart-fill me-2"></i>Donate
                            </a>
                        @endif
                        
                        <form method="POST" action="{{ route('events.share', $event) }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-outline-secondary w-100 action-btn">
                                <i class="bi bi-share-fill me-2"></i>Share Event
                            </button>
                        </form>
                    @else
                        <div class="alert alert-warning py-3">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <small>Please <a href="{{ route('login') }}" class="alert-link">login</a> to participate in this event.</small>
                        </div>
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

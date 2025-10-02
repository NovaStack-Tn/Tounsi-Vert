@extends('layouts.public')

@section('title', $event->title . ' - Management')

@section('content')
<div class="bg-primary-custom text-white py-4">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('organizer.events.index') }}" class="text-white">My Events</a></li>
                <li class="breadcrumb-item active text-white">{{ Str::limit($event->title, 50) }}</li>
            </ol>
        </nav>
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h1><i class="bi bi-calendar-event"></i> {{ $event->title }}</h1>
                @if($event->is_published)
                    <span class="badge bg-success">Published</span>
                @else
                    <span class="badge bg-warning">Draft</span>
                @endif
                @if($event->start_at < now())
                    <span class="badge bg-dark">Past Event</span>
                @else
                    <span class="badge bg-info">Upcoming</span>
                @endif
            </div>
            <div>
                <a href="{{ route('organizer.events.edit', $event) }}" class="btn btn-light me-2">
                    <i class="bi bi-pencil"></i> Edit
                </a>
                <a href="{{ route('events.show', $event) }}" target="_blank" class="btn btn-outline-light">
                    <i class="bi bi-box-arrow-up-right"></i> Public View
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container py-5">
    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm border-primary">
                <div class="card-body">
                    <i class="bi bi-people text-primary" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-2">{{ $attendees->count() }}</h3>
                    <p class="text-muted mb-0">Attendees</p>
                    @if($event->max_participants)
                        <small class="text-muted">of {{ $event->max_participants }} max</small>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm border-success">
                <div class="card-body">
                    <i class="bi bi-currency-dollar text-success" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-2">{{ $donations->sum('amount') }} TND</h3>
                    <p class="text-muted mb-0">Total Donations</p>
                    <small class="text-muted">{{ $donations->count() }} donors</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm border-warning">
                <div class="card-body">
                    <i class="bi bi-star text-warning" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-2">{{ $event->averageRating() ?? 'N/A' }}</h3>
                    <p class="text-muted mb-0">Avg Rating</p>
                    <small class="text-muted">{{ $event->reviews->count() }} reviews</small>
                </div>
            </div>
        </div>
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm border-info">
                <div class="card-body">
                    <i class="bi bi-bookmark text-info" style="font-size: 2.5rem;"></i>
                    <h3 class="mt-2">{{ $event->participations->where('type', 'follow')->count() }}</h3>
                    <p class="text-muted mb-0">Followers</p>
                    <small class="text-muted">{{ $event->participations->where('type', 'share')->count() }} shares</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Event Details -->
        <div class="col-md-8 mb-4">
            <!-- Attendees List -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-people"></i> Attendees ({{ $attendees->count() }})</h5>
                    <button class="btn btn-light btn-sm" onclick="window.print()">
                        <i class="bi bi-printer"></i> Print List
                    </button>
                </div>
                <div class="card-body p-0">
                    @if($attendees->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Location</th>
                                        <th>Registered</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($attendees as $index => $participation)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>
                                                <strong>{{ $participation->user->full_name }}</strong>
                                                <br><small class="text-muted">Score: {{ $participation->user->score }}</small>
                                            </td>
                                            <td>{{ $participation->user->email }}</td>
                                            <td>{{ $participation->user->city }}, {{ $participation->user->region }}</td>
                                            <td><small>{{ $participation->created_at->format('M d, Y') }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-person-x" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="text-muted mt-2">No attendees yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Donations List -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-currency-dollar"></i> Donations ({{ $donations->count() }})</h5>
                </div>
                <div class="card-body p-0">
                    @if($donations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Donor</th>
                                        <th>Amount</th>
                                        <th>Status</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($donations as $donation)
                                        <tr>
                                            <td>{{ $donation->participation->user->full_name }}</td>
                                            <td><strong class="text-success">{{ $donation->amount }} TND</strong></td>
                                            <td>
                                                <span class="badge bg-{{ $donation->status == 'succeeded' ? 'success' : 'warning' }}">
                                                    {{ ucfirst($donation->status) }}
                                                </span>
                                            </td>
                                            <td><small>{{ $donation->created_at->format('M d, Y H:i') }}</small></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <th>Total:</th>
                                        <th colspan="3"><strong class="text-success">{{ $donations->sum('amount') }} TND</strong></th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="bi bi-cash-stack" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="text-muted mt-2">No donations yet</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Reviews -->
            <div class="card shadow-sm">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="bi bi-star"></i> Reviews ({{ $event->reviews->count() }})</h5>
                </div>
                <div class="card-body">
                    @forelse($event->reviews as $review)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $review->user->full_name }}</strong>
                                <span class="text-warning">{{ str_repeat('⭐', $review->rate) }}</span>
                            </div>
                            <p class="text-muted small mb-1">{{ $review->created_at->format('M d, Y') }}</p>
                            @if($review->comment)
                                <p class="mb-0">{{ $review->comment }}</p>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-3">
                            <i class="bi bi-star" style="font-size: 3rem; opacity: 0.3;"></i>
                            <p class="text-muted mt-2">No reviews yet</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Event Info Sidebar -->
        <div class="col-md-4 mb-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="bi bi-info-circle"></i> Event Information</h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <th>Category:</th>
                            <td><span class="badge bg-info">{{ $event->category->name }}</span></td>
                        </tr>
                        <tr>
                            <th>Type:</th>
                            <td><span class="badge bg-secondary">{{ ucfirst($event->type) }}</span></td>
                        </tr>
                        <tr>
                            <th>Start:</th>
                            <td>{{ $event->start_at->format('M d, Y H:i') }}</td>
                        </tr>
                        @if($event->end_at)
                            <tr>
                                <th>End:</th>
                                <td>{{ $event->end_at->format('M d, Y H:i') }}</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Location:</th>
                            <td>{{ $event->city }}, {{ $event->region }}</td>
                        </tr>
                        @if($event->max_participants)
                            <tr>
                                <th>Capacity:</th>
                                <td>{{ $event->max_participants }} participants</td>
                            </tr>
                        @endif
                        <tr>
                            <th>Status:</th>
                            <td>
                                @if($event->is_published)
                                    <span class="badge bg-success">Published</span>
                                @else
                                    <span class="badge bg-warning">Draft</span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h6 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h6>
                </div>
                <div class="card-body d-grid gap-2">
                    <a href="{{ route('organizer.events.edit', $event) }}" class="btn btn-outline-primary btn-sm">
                        <i class="bi bi-pencil"></i> Edit Event
                    </a>
                    <a href="{{ route('events.show', $event) }}" target="_blank" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-box-arrow-up-right"></i> Public View
                    </a>
                    <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                        <i class="bi bi-printer"></i> Print Report
                    </button>
                    <form method="POST" action="{{ route('organizer.events.destroy', $event) }}">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm w-100" onclick="return confirm('Delete this event? This cannot be undone!')">
                            <i class="bi bi-trash"></i> Delete Event
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<div class="col-md-6 col-lg-4 mb-4">
    <div class="card h-100 shadow-sm {{ $event->start_at < now() ? 'opacity-75' : '' }}">
        @if($event->poster_path)
            <img src="{{ Storage::url($event->poster_path) }}" class="card-img-top" style="height: 180px; object-fit: cover;" alt="{{ $event->title }}">
        @else
            <div class="card-img-top bg-{{ $event->is_published ? 'primary' : 'secondary' }} bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 180px;">
                <i class="bi bi-calendar-event text-{{ $event->is_published ? 'primary' : 'secondary' }}" style="font-size: 4rem;"></i>
            </div>
        @endif
        <div class="card-body d-flex flex-column">
            <div class="mb-2">
                <span class="badge bg-info">{{ $event->category->name }}</span>
                <span class="badge bg-secondary">{{ ucfirst($event->type) }}</span>
                @if($event->is_published)
                    <span class="badge bg-success">Published</span>
                @else
                    <span class="badge bg-warning">Draft</span>
                @endif
                @if($event->start_at < now())
                    <span class="badge bg-dark">Past</span>
                @endif
            </div>
            
            <h5 class="card-title">{{ Str::limit($event->title, 50) }}</h5>
            <p class="card-text text-muted small flex-grow-1">
                <i class="bi bi-building"></i> {{ $event->organization->name }}<br>
                <i class="bi bi-calendar"></i> {{ $event->start_at->format('M d, Y - H:i') }}<br>
                <i class="bi bi-geo-alt"></i> {{ $event->city }}, {{ $event->region }}
            </p>

            <!-- Stats -->
            <div class="row text-center mb-3">
                <div class="col-4">
                    <div class="border rounded p-2">
                        <h6 class="mb-0 text-primary">{{ $event->participations->where('type', 'attend')->count() }}</h6>
                        <small class="text-muted">Attendees</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border rounded p-2">
                        <h6 class="mb-0 text-success">{{ $event->participations->where('type', 'donation')->count() }}</h6>
                        <small class="text-muted">Donations</small>
                    </div>
                </div>
                <div class="col-4">
                    <div class="border rounded p-2">
                        <h6 class="mb-0 text-info">{{ $event->reviews->count() }}</h6>
                        <small class="text-muted">Reviews</small>
                    </div>
                </div>
            </div>

            <div class="d-grid gap-2">
                <a href="{{ route('organizer.events.show', $event) }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-eye"></i> View Details
                </a>
                <div class="btn-group" role="group">
                    <a href="{{ route('organizer.events.edit', $event) }}" class="btn btn-outline-secondary btn-sm">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <a href="{{ route('events.show', $event) }}" target="_blank" class="btn btn-outline-info btn-sm">
                        <i class="bi bi-box-arrow-up-right"></i> Public
                    </a>
                    <form method="POST" action="{{ route('organizer.events.destroy', $event) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this event?')">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@extends('layouts.app')

@section('title', 'Événements')

@section('content')
<div class="container my-5">
    <h1 class="mb-4"><i class="bi bi-calendar-event text-success"></i> Tous les événements</h1>
    
    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('events.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Catégorie</label>
                        <select name="category" class="form-select">
                            <option value="">Toutes les catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Type</label>
                        <select name="type" class="form-select">
                            <option value="">Tous types</option>
                            <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>En ligne</option>
                            <option value="onsite" {{ request('type') == 'onsite' ? 'selected' : '' }}>Sur place</option>
                            <option value="hybrid" {{ request('type') == 'hybrid' ? 'selected' : '' }}>Hybride</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Région</label>
                        <input type="text" name="region" class="form-control" placeholder="Région" value="{{ request('region') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Ville</label>
                        <input type="text" name="city" class="form-control" placeholder="Ville" value="{{ request('city') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-search"></i> Rechercher
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Events Grid -->
    <div class="row">
        @forelse($events as $event)
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    @if($event->poster_path)
                        <img src="{{ asset('storage/' . $event->poster_path) }}" class="card-img-top" alt="{{ $event->title }}" style="height: 200px; object-fit: cover;">
                    @else
                        <div class="bg-success text-white d-flex align-items-center justify-content-center" style="height: 200px;">
                            <i class="bi bi-calendar-event" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    <div class="card-body">
                        <span class="badge bg-primary mb-2">{{ $event->category->name }}</span>
                        <span class="badge bg-secondary mb-2">{{ ucfirst($event->type) }}</span>
                        <h5 class="card-title">{{ $event->title }}</h5>
                        <p class="card-text text-muted small">
                            <i class="bi bi-building"></i> {{ $event->organization->name }}<br>
                            <i class="bi bi-calendar"></i> {{ $event->start_at->format('d/m/Y H:i') }}<br>
                            <i class="bi bi-geo-alt"></i> {{ $event->city ?? 'En ligne' }}
                        </p>
                        <a href="{{ route('events.show', $event) }}" class="btn btn-success btn-sm w-100">Voir détails</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Aucun événement trouvé avec ces critères.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $events->links() }}
    </div>
</div>
@endsection

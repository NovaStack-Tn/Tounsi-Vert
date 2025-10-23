@extends('layouts.app')

@section('title', 'Recommandations IA')

@section('content')
<div class="container py-5">
    <div class="mb-5 text-center">
        <h1><i class="bi bi-robot text-primary me-2"></i>Recommandations Personnalisées</h1>
        <p class="text-muted">Événements sélectionnés pour vous par notre Intelligence Artificielle</p>
    </div>

    @if(count($recommendations) > 0)
        <div class="row g-4">
            @foreach($recommendations as $rec)
                @php
                    $event = $rec['event'];
                @endphp
                <div class="col-md-6 col-lg-4">
                    <div class="card border-0 shadow-sm h-100 hover-card">
                        <!-- Badge de Score IA -->
                        <div class="position-absolute top-0 end-0 m-3">
                            <span class="badge bg-primary px-3 py-2">
                                <i class="bi bi-robot me-1"></i>Match: {{ $rec['confidence'] }}%
                            </span>
                        </div>

                        <!-- Image de l'événement -->
                        <div class="card-img-top bg-gradient text-white p-4" style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <div class="d-flex flex-column justify-content-end h-100">
                                <h5 class="card-title text-white mb-0">{{ $event->title }}</h5>
                                <small><i class="bi bi-calendar me-1"></i>{{ $event->start_at->format('d/m/Y H:i') }}</small>
                            </div>
                        </div>

                        <div class="card-body">
                            <!-- Raisons de la recommandation -->
                            <div class="mb-3">
                                <h6 class="text-muted small mb-2">Pourquoi cet événement?</h6>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($rec['reasons'] as $reason)
                                        <span class="badge bg-light text-dark border">
                                            <i class="bi bi-check-circle text-success me-1"></i>{{ $reason }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Informations de l'événement -->
                            <p class="card-text small text-muted mb-3">
                                {{ Str::limit($event->description, 100) }}
                            </p>

                            <div class="mb-3">
                                <small class="text-muted">
                                    <i class="bi bi-building me-1"></i>{{ $event->organization->name }}
                                </small><br>
                                <small class="text-muted">
                                    <i class="bi bi-geo-alt me-1"></i>{{ $event->organization->city }}, {{ $event->organization->region }}
                                </small>
                            </div>

                            <!-- Score de recommandation -->
                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <small class="text-muted">Score IA</small>
                                    <small><strong>{{ $rec['score'] }}/100</strong></small>
                                </div>
                                <div class="progress" style="height: 8px;">
                                    <div class="progress-bar bg-primary" style="width: {{ $rec['score'] }}%"></div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-grid gap-2">
                                <a href="{{ route('events.show', $event->id) }}" class="btn btn-primary">
                                    <i class="bi bi-eye me-1"></i>Voir Détails
                                </a>
                                <form action="{{ route('member.participations.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="event_id" value="{{ $event->id }}">
                                    <button type="submit" class="btn btn-success w-100">
                                        <i class="bi bi-check-circle me-1"></i>Participer
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="bi bi-robot text-muted" style="font-size: 4rem;"></i>
                <h4 class="mt-3">Aucune Recommandation Disponible</h4>
                <p class="text-muted">Participez à des événements pour que l'IA apprenne vos préférences!</p>
                <a href="{{ route('events.index') }}" class="btn btn-primary mt-3">
                    <i class="bi bi-calendar-event me-1"></i>Explorer les Événements
                </a>
            </div>
        </div>
    @endif
</div>

<style>
.hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}
.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}
</style>
@endsection

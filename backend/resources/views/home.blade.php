@extends('layouts.app')

@section('title', 'Accueil')

@section('content')
<!-- Hero Section -->
<div class="bg-success text-white py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <h1 class="display-4 fw-bold">Bienvenue sur TounsiVert</h1>
                <p class="lead">La plateforme communautaire tunisienne pour l'impact social et environnemental</p>
                <p class="mb-4">Découvrez, rejoignez et soutenez des initiatives locales : aide alimentaire, projets environnementaux, éducation, santé et plus encore.</p>
                <a href="{{ route('events.index') }}" class="btn btn-light btn-lg me-2">
                    <i class="bi bi-calendar-event"></i> Voir les événements
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg">
                    <i class="bi bi-person-plus"></i> Rejoignez-nous
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Upcoming Events -->
<div class="container my-5">
    <h2 class="mb-4"><i class="bi bi-calendar-event text-success"></i> Événements à venir</h2>
    <div class="row">
        @forelse($upcomingEvents as $event)
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
                        <p class="card-text text-muted">
                            <small><i class="bi bi-building"></i> {{ $event->organization->name }}</small><br>
                            <small><i class="bi bi-calendar"></i> {{ $event->start_at->format('d/m/Y H:i') }}</small><br>
                            <small><i class="bi bi-geo-alt"></i> {{ $event->city ?? 'En ligne' }}</small>
                        </p>
                        <a href="{{ route('events.show', $event) }}" class="btn btn-success btn-sm">Voir détails</a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <p class="text-muted">Aucun événement à venir pour le moment.</p>
            </div>
        @endforelse
    </div>
    <div class="text-center mt-4">
        <a href="{{ route('events.index') }}" class="btn btn-outline-success">Voir tous les événements</a>
    </div>
</div>

<!-- Featured Organizations -->
<div class="bg-light py-5">
    <div class="container">
        <h2 class="mb-4"><i class="bi bi-building text-success"></i> Organisations vérifiées</h2>
        <div class="row">
            @forelse($featuredOrganizations as $org)
                <div class="col-md-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body text-center">
                            @if($org->logo_path)
                                <img src="{{ asset('storage/' . $org->logo_path) }}" class="rounded-circle mb-3" alt="{{ $org->name }}" style="width: 100px; height: 100px; object-fit: cover;">
                            @else
                                <div class="bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 100px; height: 100px;">
                                    <i class="bi bi-building" style="font-size: 2rem;"></i>
                                </div>
                            @endif
                            <h5 class="card-title">{{ $org->name }}</h5>
                            <span class="badge bg-info mb-2">{{ $org->category->name }}</span>
                            @if($org->is_verified)
                                <span class="badge bg-success mb-2"><i class="bi bi-check-circle"></i> Vérifié</span>
                            @endif
                            <p class="card-text text-muted small">{{ Str::limit($org->description, 80) }}</p>
                            <a href="{{ route('organizations.show', $org) }}" class="btn btn-outline-success btn-sm">Voir profil</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-muted">Aucune organisation pour le moment.</p>
                </div>
            @endforelse
        </div>
        <div class="text-center mt-4">
            <a href="{{ route('organizations.index') }}" class="btn btn-outline-success">Voir toutes les organisations</a>
        </div>
    </div>
</div>

<!-- Call to Action -->
<div class="container my-5 text-center">
    <h2 class="mb-4">Prêt à faire la différence ?</h2>
    <p class="lead mb-4">Rejoignez notre communauté et participez aux initiatives qui transforment la Tunisie</p>
    <a href="{{ route('register') }}" class="btn btn-success btn-lg">
        <i class="bi bi-person-plus"></i> Créer un compte gratuitement
    </a>
</div>
@endsection

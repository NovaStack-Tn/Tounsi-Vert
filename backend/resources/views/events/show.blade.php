@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Event Image -->
            @if($event->poster_path)
                <img src="{{ asset('storage/' . $event->poster_path) }}" class="img-fluid rounded mb-4" alt="{{ $event->title }}">
            @else
                <div class="bg-success text-white d-flex align-items-center justify-content-center rounded mb-4" style="height: 400px;">
                    <i class="bi bi-calendar-event" style="font-size: 5rem;"></i>
                </div>
            @endif

            <!-- Event Details -->
            <h1 class="mb-3">{{ $event->title }}</h1>
            
            <div class="mb-3">
                <span class="badge bg-primary">{{ $event->category->name }}</span>
                <span class="badge bg-secondary">{{ ucfirst($event->type) }}</span>
                @if($event->is_published)
                    <span class="badge bg-success">Publié</span>
                @endif
            </div>

            <div class="card mb-4">
                <div class="card-body">
                    <h5><i class="bi bi-info-circle text-success"></i> Description</h5>
                    <p>{{ $event->description ?? 'Aucune description disponible.' }}</p>
                </div>
            </div>

            <!-- Reviews -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-star text-warning"></i> Avis ({{ $event->reviews->count() }})</h5>
                    @if($averageRating)
                        <div class="text-muted">
                            Note moyenne: {{ number_format($averageRating, 1) }}/5
                        </div>
                    @endif
                </div>
                <div class="card-body">
                    @auth
                        <form action="{{ route('reviews.store', $event) }}" method="POST" class="mb-4">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Votre note</label>
                                <select name="rate" class="form-select" required>
                                    <option value="">Sélectionner...</option>
                                    <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                                    <option value="4">⭐⭐⭐⭐ Très bien</option>
                                    <option value="3">⭐⭐⭐ Bien</option>
                                    <option value="2">⭐⭐ Moyen</option>
                                    <option value="1">⭐ Mauvais</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Commentaire</label>
                                <textarea name="comment" class="form-control" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success">Publier l'avis</button>
                        </form>
                    @endauth

                    @foreach($event->reviews as $review)
                        <div class="border-bottom pb-3 mb-3">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $review->user->full_name }}</strong>
                                <span class="text-warning">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $review->rate)
                                            ⭐
                                        @else
                                            ☆
                                        @endif
                                    @endfor
                                </span>
                            </div>
                            <p class="text-muted small mb-1">{{ $review->created_at->diffForHumans() }}</p>
                            <p>{{ $review->comment }}</p>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Action Card -->
            <div class="card mb-4 shadow">
                <div class="card-body">
                    <h5 class="card-title">Informations</h5>
                    <p class="mb-2">
                        <i class="bi bi-calendar"></i> <strong>Date:</strong><br>
                        {{ $event->start_at->format('d/m/Y à H:i') }}
                    </p>
                    @if($event->end_at)
                        <p class="mb-2">
                            <i class="bi bi-calendar-check"></i> <strong>Fin:</strong><br>
                            {{ $event->end_at->format('d/m/Y à H:i') }}
                        </p>
                    @endif
                    
                    @if($event->type == 'online' || $event->type == 'hybrid')
                        <p class="mb-2">
                            <i class="bi bi-camera-video"></i> <strong>Lien:</strong><br>
                            <a href="{{ $event->meeting_url }}" target="_blank" class="text-break">Rejoindre en ligne</a>
                        </p>
                    @endif
                    
                    @if($event->type == 'onsite' || $event->type == 'hybrid')
                        <p class="mb-2">
                            <i class="bi bi-geo-alt"></i> <strong>Lieu:</strong><br>
                            {{ $event->address }}<br>
                            {{ $event->city }}, {{ $event->region }}
                        </p>
                    @endif
                    
                    <p class="mb-2">
                        <i class="bi bi-people"></i> <strong>Participants:</strong><br>
                        {{ $attendeesCount }} 
                        @if($event->max_participants)
                            / {{ $event->max_participants }}
                        @endif
                    </p>
                    
                    <p class="mb-3">
                        <i class="bi bi-heart"></i> <strong>Followers:</strong> {{ $followersCount }}
                    </p>

                    @auth
                        <form action="{{ route('participations.store', $event) }}" method="POST" class="mb-2">
                            @csrf
                            <input type="hidden" name="type" value="attend">
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="bi bi-check-circle"></i> Participer (+10 pts)
                            </button>
                        </form>
                        
                        <form action="{{ route('participations.store', $event) }}" method="POST" class="mb-2">
                            @csrf
                            <input type="hidden" name="type" value="follow">
                            <button type="submit" class="btn btn-outline-success w-100 mb-2">
                                <i class="bi bi-heart"></i> Suivre (+1 pt)
                            </button>
                        </form>
                        
                        <form action="{{ route('participations.store', $event) }}" method="POST" class="mb-3">
                            @csrf
                            <input type="hidden" name="type" value="share">
                            <button type="submit" class="btn btn-outline-info w-100">
                                <i class="bi bi-share"></i> Partager (+2 pts)
                            </button>
                        </form>

                        <hr>
                        
                        <h6>Faire un don</h6>
                        <form action="{{ route('donations.store', $event) }}" method="POST">
                            @csrf
                            <div class="input-group mb-2">
                                <input type="number" name="amount" class="form-control" placeholder="Montant" min="1" required>
                                <span class="input-group-text">TND</span>
                            </div>
                            <button type="submit" class="btn btn-warning w-100">
                                <i class="bi bi-gift"></i> Donner
                            </button>
                            <small class="text-muted">+1 point par TND</small>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-success w-100 mb-2">
                            Connectez-vous pour participer
                        </a>
                    @endauth
                </div>
            </div>

            <!-- Organization Card -->
            <div class="card mb-4">
                <div class="card-body">
                    <h5 class="card-title">Organisé par</h5>
                    <div class="text-center">
                        @if($event->organization->logo_path)
                            <img src="{{ asset('storage/' . $event->organization->logo_path) }}" class="rounded-circle mb-3" style="width: 80px; height: 80px; object-fit: cover;" alt="{{ $event->organization->name }}">
                        @endif
                        <h6>{{ $event->organization->name }}</h6>
                        @if($event->organization->is_verified)
                            <span class="badge bg-success mb-2"><i class="bi bi-check-circle"></i> Vérifié</span>
                        @endif
                        <p class="small text-muted">{{ $event->organization->category->name }}</p>
                        <a href="{{ route('organizations.show', $event->organization) }}" class="btn btn-outline-success btn-sm">
                            Voir le profil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

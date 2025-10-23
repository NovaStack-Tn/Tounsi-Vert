@extends('layouts.public')

@section('title', 'Browse Vehicules')

@section('content')
<style>
    .events-hero {
        background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
        color: white;
        padding: 80px 0 60px;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
    }
    .events-hero::before {
        content: '🚗 🚙 🏎️ 🚐 ✨';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        font-size: 2.5rem;
        text-align: center;
        opacity: 0.2;
        letter-spacing: 30px;
        animation: float 4s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-15px); }
    }
    .event-card {
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.4s ease;
        border: none;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        
    }
    .event-card:hover {
        transform: translateY(-15px) scale(1.02);
        box-shadow: 0 20px 40px rgba(45, 106, 79, 0.3);
    }
    .event-img {
        height: 220px;
        object-fit: cover;
        transition: transform 0.4s ease;
    }
    .event-card:hover .event-img {
        transform: scale(1.1);
    }
    .event-badge {
        border-radius: 20px;
        padding: 5px 15px;
        font-size: 0.85rem;
        font-weight: 600;
    }
    .tabs {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-bottom: 30px;
    }
    .tab {
        cursor: pointer;
        font-weight: bold;
        font-size: 1.2rem;
        padding: 10px 20px;
        border-radius: 30px;
        transition: all 0.3s ease;
        background: #f1f1f1;
    }
    .tab.active {
        background: #2d6a4f;
        color: white;
    }
    .tab-content {
        display: none;
     
    }
    .tab-content.active {
        display: block;
    }
    .quick-match-btn {
        margin-bottom: 30px;
        text-align: center;
    }
</style>

<div class="events-hero">
    <div class="container text-center">
        <i class="bi bi-car-front-fill mb-3" style="font-size: 4rem;"></i>
        <h1 class="display-3 fw-bold mb-3">Discover Vehicules</h1>
        <p class="lead mb-0">Browse all available vehicules in the system</p>
    </div>
</div>

<div class="container py-5">

 <!-- Quick Match Button -->
<div class="quick-match-btn">
    <a href="{{ route('bookings.quickForm') }}" class="btn btn-primary btn-lg w-100" style="max-width: 400px;">
        <i class="bi bi-lightning-charge me-2"></i>Quick Match
    </a>
</div>


    <!-- Tabs -->
    <div class="tabs">
        <div class="tab active" data-tab="all">All Vehicules</div>
        <div class="tab" data-tab="mine">My Vehicules</div>
    </div>

    <!-- All Vehicules Tab -->
    <div id="all" class="tab-content active">
        <div class="row">
            @forelse($vehicules as $vehicule)
                <div class="col-md-4 mb-4">
                    <div class="event-card card h-100">
                        <div style="overflow: hidden; border-radius: 20px 20px 0 0;">
                            @if($vehicule->image_path)
<img src="{{ $vehicule->image_path ? asset('storage/' . $vehicule->image_path) : asset('images/default-car.png') }}" class="event-img w-100" alt="{{ $vehicule->type }}">
                            @else
                                <div class="event-img bg-gradient d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);">
                                    <i class="bi bi-car-front text-white" style="font-size: 4rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column p-4">
                            <h5 class="card-title fw-bold mb-2">{{ Str::limit($vehicule->type, 50) }}</h5>
                            <p class="card-text mb-1"><strong>Description:</strong> {{ $vehicule->description ?? 'N/A' }}</p>
                            <p class="card-text mb-1"><strong>Capacity:</strong> {{ $vehicule->capacity ?? '-' }}</p>
                            <p class="card-text mb-1"><strong>Availability:</strong> {{ $vehicule->availability_start ?? '-' }} → {{ $vehicule->availability_end ?? '-' }}</p>
                            <p class="card-text mb-1"><strong>Location:</strong> {{ $vehicule->location ?? '-' }}</p>
                            <p class="card-text mb-1"><strong>Status:</strong> 
                                <span class="badge {{ $vehicule->status === 'available' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($vehicule->status) ?? 'Unknown' }}
                                </span>
                            </p>
                            <p class="card-text mb-1"><strong>Owner:</strong> {{ $vehicule->owner->name ?? '-' }} ({{ $vehicule->owner->email ?? '-' }})</p>
                            <div class="mt-auto">
                              
                                <a href="{{ route('bookings.create', ['vehicule' => $vehicule->id]) }}" class="btn btn-success mt-2">
                                    <i class="bi bi-calendar-check"></i> Book Vehicle
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center p-5">
                    <h5>No vehicules found</h5>
                </div>
            @endforelse

            <div class="col-12 d-flex justify-content-center mt-4">
                {{ $vehicules->links() }}
            </div>
        </div>
    </div>

    <!-- My Vehicules Tab -->
    <div id="mine" class="tab-content">
        <div class="row">
            @forelse($myVehicules as $vehicule)
                <div class="col-md-4 mb-4">
                    <div class="event-card card h-100">
                        <div style="overflow: hidden; border-radius: 20px 20px 0 0;">
                            @if($vehicule->image_path)
<img src="{{ $vehicule->image_path ? asset('storage/' . $vehicule->image_path) : asset('images/default-car.png') }}" class="event-img w-100" alt="{{ $vehicule->type }}">
                            @else
                                <div class="event-img bg-gradient d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);">
                                    <i class="bi bi-car-front text-white" style="font-size: 4rem;"></i>
                                </div>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column p-4">
                            <h5 class="card-title fw-bold mb-2">{{ Str::limit($vehicule->type, 50) }}</h5>
                            <p class="card-text mb-1"><strong>Description:</strong> {{ $vehicule->description ?? 'N/A' }}</p>
                            <p class="card-text mb-1"><strong>Capacity:</strong> {{ $vehicule->capacity ?? '-' }}</p>
                            <p class="card-text mb-1"><strong>Availability:</strong> {{ $vehicule->availability_start ?? '-' }} → {{ $vehicule->availability_end ?? '-' }}</p>
                            <p class="card-text mb-1"><strong>Location:</strong> {{ $vehicule->location ?? '-' }}</p>
                            <p class="card-text mb-1"><strong>Status:</strong> 
                                <span class="badge {{ $vehicule->status === 'available' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($vehicule->status) ?? 'Unknown' }}
                                </span>
                            </p>
                            <div class="mt-auto">
                                <a href="{{ route('vehicules.show', $vehicule) }}" class="btn btn-success w-100 search-btn">
                                    <i class="bi bi-arrow-right-circle me-2"></i>View Details
                                </a>
                                <a href="{{ route('bookings.create', ['vehicule' => $vehicule->id]) }}" class="btn btn-success mt-2">
                                    <i class="bi bi-calendar-check"></i> Book Vehicle
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center p-5">
                    <h5>You have no vehicles</h5>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Call to Action -->
    <div class="card border-0 shadow-lg mt-5" style="background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);">
        <div class="card-body text-white text-center p-5">
            <i class="bi bi-car-front-fill mb-3" style="font-size: 4rem; opacity: 0.8;"></i>
            <h3 class="mb-3">Want to Add a Vehicule?</h3>
            <p class="lead mb-4">Register a new vehicule in the system to keep the database updated!</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('vehicules.create') }}" class="btn btn-light btn-lg">
                    <i class="bi bi-plus-circle me-2"></i>Add Vehicule
                </a>
            </div>
        </div>
    </div>

</div>

<script>
    const tabs = document.querySelectorAll('.tab');
    const tabContents = document.querySelectorAll('.tab-content');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');

            const target = tab.getAttribute('data-tab');
            tabContents.forEach(tc => {
                tc.classList.remove('active');
                if(tc.id === target) tc.classList.add('active');
            });
        });
    });
</script>
@endsection

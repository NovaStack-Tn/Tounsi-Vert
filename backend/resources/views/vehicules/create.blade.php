@extends('layouts.public')

@section('title', 'Add a Vehicle')

@section('content')
<style>
    /* Hero Section */
    .events-hero {
        background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
        color: white;
        padding: 80px 0 60px;
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
        border-radius: 20px;
        text-align: center;
    }
    .events-hero::before {
        content: '🚗 🚙 🏎️ 🚐 ✨';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        font-size: 2.5rem;
        text-align: center;
        opacity: 0.15;
        letter-spacing: 30px;
        animation: float 4s ease-in-out infinite;
    }
    @keyframes float {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-15px); }
    }
    .events-hero h1 {
        font-size: 3rem;
        font-weight: 700;
    }
    .events-hero p {
        font-size: 1.2rem;
    }

    /* Form card styles */
    .form-card {
        background-color: #fff;
        padding: 30px;
        border-radius: 20px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-bottom: 40px;
    }
    .form-card label {
        font-weight: 600;
    }
    .form-card .btn {
        font-size: 1rem;
        padding: 10px 20px;
    }
    .form-card h2 {
        margin-bottom: 20px;
        color: #2d6a4f;
    }
</style>

<!-- Discover Vehicles Hero Section -->
<div class="events-hero">
    <i class="bi bi-car-front-fill mb-3" style="font-size: 4rem;"></i>
    <h1 class="fw-bold mb-3">Discover Vehicles</h1>
    <p class="lead mb-0">Browse and add new vehicles in the system</p>
</div>

<div class="container py-5">
    <!-- Vehicle Creation Form -->
    <div class="form-card">
        <h2>Add a Vehicle</h2>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                       <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Add enctype for file upload -->
        <form action="{{ route('vehicules.store') }}" method="POST" class="row g-3" enctype="multipart/form-data">
            @csrf

            <div class="col-md-6">
                <label>Type</label>
                <input type="text" name="type" class="form-control" value="{{ old('type') }}" placeholder="Ex: Car" required>
            </div>

            <div class="col-md-6">
                <label>Capacity</label>
                <input type="number" name="capacity" class="form-control" value="{{ old('capacity') }}" placeholder="Ex: 5" required>
            </div>

            <div class="col-12">
                <label>Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Ex: Family car">{{ old('description') }}</textarea>
            </div>

            <div class="col-md-6">
    <label>Availability Start</label>
    <input type="datetime-local" name="availability_start" class="form-control" value="{{ old('availability_start') }}" required>
</div>

<div class="col-md-6">
    <label>Availability End</label>
    <input type="datetime-local" name="availability_end" class="form-control" value="{{ old('availability_end') }}" required>
</div>


            <div class="col-md-6">
                <label>Location</label>
                <input type="text" name="location" class="form-control" value="{{ old('location') }}" placeholder="Ex: Sousse" required>
            </div>

            <div class="col-md-6">
                <label>Status</label>
                <select name="status" class="form-select">
                    <option value="active" {{ old('status')=='active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ old('status')=='inactive' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>

            <!-- File input for vehicle image -->
            <div class="col-12">
                <label>Image</label>
                <input type="file" name="image" class="form-control">
            </div>

            <div class="col-12 d-flex gap-2 mt-3">
                <button type="submit" class="btn btn-success flex-grow-1">
                    <i class="bi bi-plus-circle me-2"></i>Add
                </button>
                <a href="{{ route('vehicules.index') }}" class="btn btn-secondary flex-grow-1">
                    <i class="bi bi-arrow-left-circle me-2"></i>View List
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@extends('layouts.public')

@section('title', 'Leaderboard - TounsiVert')

@section('content')
<style>
    .leaderboard-hero {
        background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
        color: white;
        padding: 80px 0 60px;
        margin-bottom: 50px;
        position: relative;
        overflow: hidden;
    }
    .leaderboard-hero::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='80' height='80' viewBox='0 0 80 80' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.15'%3E%3Cpath d='M50 50c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10c0 5.523-4.477 10-10 10s-10-4.477-10-10 4.477-10 10-10zM10 10c0-5.523 4.477-10 10-10s10 4.477 10 10-4.477 10-10 10c0 5.523-4.477 10-10 10S0 25.523 0 20s4.477-10 10-10zm10 8c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8zm40 40c4.418 0 8-3.582 8-8s-3.582-8-8-8-8 3.582-8 8 3.582 8 8 8z' /%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    }
    .leaderboard-hero::after {
        content: '🎉 🏆 ⭐ 💚 🎊 🌟 💎 🎯 🏅 ✨';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        font-size: 2rem;
        text-align: center;
        opacity: 0.3;
        letter-spacing: 20px;
        animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    
    .trophy-icon {
        font-size: 5rem;
        animation: bounce 2s infinite;
    }
    
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-20px); }
    }
    
    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .leaderboard-card {
        animation: fadeInUp 0.6s ease-out;
        transition: all 0.3s ease;
    }
    
    .leaderboard-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.2);
    }
    
    .rank-badge {
        width: 55px;
        height: 55px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 800;
        position: relative;
        flex-shrink: 0;
    }
    
    .rank-1 { 
        background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%);
        color: #000;
        box-shadow: 0 8px 25px rgba(255, 215, 0, 0.6);
        border: 3px solid #FFE55C;
    }
    .rank-2 { 
        background: linear-gradient(135deg, #E8E8E8 0%, #C0C0C0 100%);
        color: #000;
        box-shadow: 0 8px 25px rgba(192, 192, 192, 0.6);
        border: 3px solid #F5F5F5;
    }
    .rank-3 { 
        background: linear-gradient(135deg, #CD7F32 0%, #A0522D 100%);
        color: #fff;
        box-shadow: 0 8px 25px rgba(205, 127, 50, 0.6);
        border: 3px solid #DDA76A;
    }
    .rank-other { 
        background: linear-gradient(135deg, #52b788 0%, #2d6a4f 100%);
        color: white;
        border: 3px solid #95d5b2;
    }
    
    .user-avatar-lg {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: bold;
        font-size: 1.2rem;
    }
    
    .section-title {
        position: relative;
        display: inline-block;
        padding-bottom: 15px;
    }
    
    .section-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 80px;
        height: 4px;
        background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
        border-radius: 2px;
    }
    
    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .crown-icon {
        position: absolute;
        top: -25px;
        left: -25px;
        font-size: 2rem;
        color: #FFD700;
        filter: drop-shadow(0 4px 8px rgba(255, 215, 0, 0.8));
        animation: crownPulse 2s ease-in-out infinite;
        z-index: 1;
    }
    
    @keyframes crownPulse {
        0%, 100% { 
            transform: scale(1) rotate(-25deg);
            opacity: 1;
        }
        50% { 
            transform: scale(1.15) rotate(-25deg);
            opacity: 0.9;
        }
    }
    
    .leaderboard-card {
        border-radius: 15px;
        overflow: hidden;
    }
    
    .leaderboard-card .card-body {
        border-radius: 15px;
    }
</style>

<!-- Hero Section -->
<div class="leaderboard-hero">
    <div class="container text-center position-relative">
        <i class="bi bi-trophy-fill trophy-icon"></i>
        <h1 class="display-3 fw-bold mb-3">Leaderboard</h1>
        <p class="lead mb-0">Celebrating Our Top Contributors & Champions</p>
    </div>
</div>

<div class="container mb-5">
    <div class="row g-4">
        <!-- Top Donators -->
        <div class="col-lg-4">
            <div class="text-center mb-4">
                <h3 class="section-title">
                    <i class="bi bi-cash-stack text-success me-2"></i>Top Donators
                </h3>
            </div>
            <div class="leaderboard-section">
                @forelse($topDonators as $index => $donator)
                    <div class="leaderboard-card card border-0 shadow-sm mb-3" style="animation-delay: {{ $index * 0.1 }}s;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center position-relative">
                                @if($index === 0)
                                    <i class="bi bi-award-fill crown-icon"></i>
                                @endif
                                <div class="rank-badge {{ $index < 3 ? 'rank-' . ($index + 1) : 'rank-other' }} me-3">
                                    {{ $index + 1 }}
                                </div>
                                <div class="user-avatar-lg me-3">
                                    {{ strtoupper(substr($donator->first_name, 0, 1)) }}{{ strtoupper(substr($donator->last_name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $donator->full_name }}</h6>
                                    <p class="mb-0 text-muted small">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $donator->city }}, {{ $donator->region }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <div class="stat-value">${{ number_format($donator->total_donated, 2) }}</div>
                                    <small class="text-muted">Donated</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-cash-stack text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        <p class="text-muted mt-3">No donations yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Top Users by Score -->
        <div class="col-lg-4">
            <div class="text-center mb-4">
                <h3 class="section-title">
                    <i class="bi bi-star-fill text-warning me-2"></i>Top Impact Leaders
                </h3>
            </div>
            <div class="leaderboard-section">
                @forelse($topUsers as $index => $user)
                    <div class="leaderboard-card card border-0 shadow-sm mb-3" style="animation-delay: {{ $index * 0.1 }}s;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center position-relative">
                                @if($index === 0)
                                    <i class="bi bi-award-fill crown-icon"></i>
                                @endif
                                <div class="rank-badge {{ $index < 3 ? 'rank-' . ($index + 1) : 'rank-other' }} me-3">
                                    {{ $index + 1 }}
                                </div>
                                <div class="user-avatar-lg me-3">
                                    {{ strtoupper(substr($user->first_name, 0, 1)) }}{{ strtoupper(substr($user->last_name, 0, 1)) }}
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $user->full_name }}</h6>
                                    <p class="mb-0 text-muted small">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $user->city }}, {{ $user->region }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <div class="stat-value">{{ number_format($user->score) }}</div>
                                    <small class="text-muted">Points</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-star text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        <p class="text-muted mt-3">No users yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Top Organizations by Followers -->
        <div class="col-lg-4">
            <div class="text-center mb-4">
                <h3 class="section-title">
                    <i class="bi bi-building text-primary me-2"></i>Top Organizations
                </h3>
            </div>
            <div class="leaderboard-section">
                @forelse($topOrganizations as $index => $org)
                    <div class="leaderboard-card card border-0 shadow-sm mb-3" style="animation-delay: {{ $index * 0.1 }}s;">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center position-relative">
                                @if($index === 0)
                                    <i class="bi bi-award-fill crown-icon"></i>
                                @endif
                                <div class="rank-badge {{ $index < 3 ? 'rank-' . ($index + 1) : 'rank-other' }} me-3">
                                    {{ $index + 1 }}
                                </div>
                                @if($org->logo_path)
                                    <img src="{{ Storage::url($org->logo_path) }}" alt="{{ $org->name }}" class="rounded me-3" style="width: 50px; height: 50px; object-fit: cover;">
                                @else
                                    <div class="user-avatar-lg me-3">
                                        <i class="bi bi-building"></i>
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ Str::limit($org->name, 20) }}</h6>
                                    <p class="mb-0 text-muted small">
                                        <i class="bi bi-geo-alt me-1"></i>{{ $org->city }}, {{ $org->region }}
                                    </p>
                                </div>
                                <div class="text-end">
                                    <div class="stat-value">{{ number_format($org->followers_count) }}</div>
                                    <small class="text-muted">Followers</small>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <i class="bi bi-building text-muted" style="font-size: 4rem; opacity: 0.3;"></i>
                        <p class="text-muted mt-3">No organizations yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Call to Action -->
    <div class="card border-0 shadow-lg mt-5" style="background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);">
        <div class="card-body text-white text-center p-5">
            <i class="bi bi-trophy-fill mb-3" style="font-size: 4rem; opacity: 0.8;"></i>
            <h3 class="mb-3">Want to Join the Leaderboard?</h3>
            <p class="lead mb-4">Participate in events, donate to causes, and make a real impact in your community!</p>
            <div class="d-flex justify-content-center gap-3 flex-wrap">
                <a href="{{ route('events.index') }}" class="btn btn-light btn-lg">
                    <i class="bi bi-calendar-event me-2"></i>Browse Events
                </a>
                <a href="{{ route('organizations.index') }}" class="btn btn-outline-light btn-lg">
                    <i class="bi bi-building me-2"></i>Explore Organizations
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

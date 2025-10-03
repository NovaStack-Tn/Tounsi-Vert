<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'TounsiVert - Platform for Impact Events in Tunisia')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        .bg-primary-custom {
            background-color: #2d6a4f;
        }
        .text-primary-custom {
            color: #2d6a4f;
        }
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }
        .navbar-brand:hover {
            transform: scale(1.05);
        }
        .navbar-brand img {
            height: 45px;
            width: auto;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }
        .navbar {
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
        }
        .nav-link {
            position: relative;
            transition: all 0.3s ease;
            border-radius: 8px;
        }
        .nav-link:hover {
            background: rgba(255,255,255,0.15);
            transform: translateY(-2px);
        }
        .nav-link.active {
            background: rgba(255,255,255,0.2);
            border-bottom: 3px solid #95d5b2;
        }
        .dropdown-menu {
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.15);
            border-radius: 12px;
            animation: slideDown 0.3s ease;
        }
        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #52b788 0%, #2d6a4f 100%);
            border: 2px solid #95d5b2;
            transition: all 0.3s ease;
        }
        .user-avatar:hover {
            transform: rotate(5deg) scale(1.1);
        }
        .badge-score {
            background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
            color: #2d6a4f;
            font-weight: 700;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
            box-shadow: 0 2px 8px rgba(255,215,0,0.3);
        }
        .hero-section {
            background: linear-gradient(135deg, #2d6a4f 0%, #52b788 100%);
            color: white;
            padding: 100px 0;
        }
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        footer {
            background: linear-gradient(135deg, #1b4332 0%, #2d6a4f 100%);
        }
        .footer-logo {
            height: 60px;
            width: auto;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
        }
    </style>
</head>
<body>
    <!-- Enhanced Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary-custom">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <img src="{{ asset('logo.png') }}" alt="TounsiVert Logo" class="me-2">
                <span style="font-size: 1.5rem; font-weight: 700; letter-spacing: 0.5px;">TounsiVert</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto ms-4">
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('home') ? 'active fw-bold' : '' }}" href="{{ route('home') }}">
                            <i class="bi bi-house-door me-1"></i>Home
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('events.*') ? 'active fw-bold' : '' }}" href="{{ route('events.index') }}">
                            <i class="bi bi-calendar-event me-1"></i>Events
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('organizations.*') ? 'active fw-bold' : '' }}" href="{{ route('organizations.index') }}">
                            <i class="bi bi-building me-1"></i>Organizations
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link px-3 {{ request()->routeIs('about') ? 'active fw-bold' : '' }}" href="{{ route('about') }}">
                            <i class="bi bi-info-circle me-1"></i>About
                        </a>
                </ul>
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="user-avatar text-white rounded-circle d-flex align-items-center justify-content-center me-2">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <span class="fw-semibold">{{ auth()->user()->full_name }}</span>
                                <span class="badge-score ms-2"><i class="bi bi-star-fill"></i> {{ auth()->user()->score }} pts</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li class="dropdown-header">
                                    <small class="text-muted">{{ auth()->user()->email }}</small>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="bi bi-person-circle me-2"></i>My Profile
                                    </a>
                                </li>
                                @if(auth()->user()->isMember())
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item text-primary" href="{{ route('organization-request.create') }}">
                                            <i class="bi bi-building-add me-2"></i>Request Organization
                                        </a>
                                    </li>
                                @endif
                                @if(auth()->user()->isOrganizer() && !auth()->user()->isAdmin())
                                    <li><hr class="dropdown-divider"></li>
                                    <li class="dropdown-header">Organizer</li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('organizer.dashboard') }}">
                                            <i class="bi bi-building me-2"></i>My Organization
                                        </a>
                                    </li>
                                @endif
                                @if(auth()->user()->isAdmin())
                                    <li><hr class="dropdown-divider"></li>
                                    <li class="dropdown-header">Administrator</li>
                                    <li>
                                        <a class="dropdown-item text-danger" href="{{ route('admin.dashboard') }}">
                                            <i class="bi bi-shield-check me-2"></i>Admin Panel
                                        </a>
                                    </li>
                                @endif
                                @if(!auth()->user()->isAdmin())
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('member.reports.index') }}">
                                            <i class="bi bi-flag me-2"></i>My Reports
                                        </a>
                                    </li>
                                @endif
                                <li>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link px-3" href="{{ route('login') }}">
                                <i class="bi bi-box-arrow-in-right me-1"></i>Login
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-light text-success ms-2" href="{{ route('register') }}">
                                <i class="bi bi-person-plus me-1"></i>Register
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show m-0" role="alert">
            <div class="container">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show m-0" role="alert">
            <div class="container">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    <!-- Main Content -->
    @yield('content')

    <!-- Footer -->
    <footer class="text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ asset('logo.png') }}" alt="TounsiVert Logo" class="footer-logo me-2">
                        <h5 class="mb-0" style="font-size: 1.5rem; font-weight: 700;">TounsiVert</h5>
                    </div>
                    <p class="text-white-50">Empowering Tunisians to make a positive impact in their communities through sustainable events and initiatives.</p>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Quick Links</h5>
                    <ul class="list-unstyled">
                        <li><a href="{{ route('events.index') }}" class="text-white-50">Browse Events</a></li>
                        <li><a href="{{ route('organizations.index') }}" class="text-white-50">Organizations</a></li>
                        <li><a href="{{ route('about') }}" class="text-white-50">About Us</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-3">
                    <h5>Contact</h5>
                    <p class="text-white-50">Email: contact@tounsivert.tn<br>
                    Phone: +216 XX XXX XXX</p>
                </div>
            </div>
            <hr class="bg-white">
            <div class="text-center">
                <p class="mb-0">&copy; {{ date('Y') }} TounsiVert. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

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
    </style>
</head>
<body>
    <!-- Enhanced Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary-custom shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('home') }}">
                <i class="bi bi-tree-fill me-2" style="font-size: 1.8rem;"></i>
                <span style="font-size: 1.5rem; font-weight: 700;">TounsiVert</span>
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
                    </li>
                </ul>
                <ul class="navbar-nav">
                    @auth
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown">
                                <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 35px; height: 35px;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <span>{{ auth()->user()->full_name }}</span>
                                <span class="badge bg-warning text-dark ms-2">{{ auth()->user()->score }} pts</span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end shadow">
                                <li class="dropdown-header">
                                    <small class="text-muted">{{ auth()->user()->email }}</small>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('dashboard') }}">
                                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
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
                                @if(auth()->user()->isOrganizer() || auth()->user()->isAdmin())
                                    <li><hr class="dropdown-divider"></li>
                                    <li class="dropdown-header">Organizer</li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('organizer.organizations.index') }}">
                                            <i class="bi bi-building me-2"></i>My Organizations
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="{{ route('organizer.events.index') }}">
                                            <i class="bi bi-calendar-check me-2"></i>My Events
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
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                        <i class="bi bi-gear me-2"></i>Settings
                                    </a>
                                </li>
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
    <footer class="bg-dark text-white py-5 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3">
                    <h5><i class="bi bi-tree-fill"></i> TounsiVert</h5>
                    <p>Empowering Tunisians to make a positive impact in their communities.</p>
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

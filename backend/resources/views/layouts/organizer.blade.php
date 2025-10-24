<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Organizer Panel - TounsiVert')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .organizer-sidebar {
            background: linear-gradient(180deg, #2d6a4f 0%, #1b4332 100%);
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 260px;
            padding-top: 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .organizer-content {
            margin-left: 260px;
            min-height: 100vh;
        }
        .sidebar-brand {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 700;
            padding: 15px 20px;
            text-decoration: none;
            display: flex;
            align-items: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .sidebar-menu li a {
            color: rgba(255,255,255,0.8);
            padding: 12px 20px;
            display: flex;
            align-items: center;
            text-decoration: none;
            transition: all 0.3s;
        }
        .sidebar-menu li a i {
            margin-right: 10px;
            font-size: 1.2rem;
            width: 25px;
        }
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }
        .sidebar-menu li a.active {
            border-left: 4px solid #52b788;
            padding-left: 16px;
        }
        .organizer-header {
            background: #fff;
            padding: 20px 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #2d6a4f;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        .stat-card {
            transition: all 0.3s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
        }
        @media (max-width: 768px) {
            .organizer-sidebar {
                width: 0;
                overflow: hidden;
            }
            .organizer-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <aside class="organizer-sidebar">
            <a href="{{ route('organizer.dashboard') }}" class="sidebar-brand">
                <img src="{{ asset('logo.png') }}" alt="TounsiVert Logo" style="height: 80px; width: auto; margin-right: 50px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
            </a>
            
            <ul class="sidebar-menu">
                @php
                    $organization = auth()->user()->organizationsOwned()->first();
                    $isBlocked = $organization && $organization->is_blocked;
                @endphp
                
                @if($isBlocked)
                    <li class="px-3 py-2">
                        <div class="alert alert-danger mb-0 text-center p-2">
                            <i class="bi bi-ban"></i>
                            <small class="d-block"><strong>BLOCKED</strong></small>
                            <small class="d-block">Contact Admin</small>
                        </div>
                    </li>
                @endif
                
                <li>
                    <a href="{{ route('organizer.dashboard') }}" class="{{ request()->routeIs('organizer.dashboard') ? 'active' : '' }} {{ $isBlocked ? 'disabled' : '' }}" {!! $isBlocked ? 'onclick="return false;" style="opacity: 0.5; cursor: not-allowed;"' : '' !!}>
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('organizer.organizations.index') }}" class="{{ request()->routeIs('organizer.organizations.*') ? 'active' : '' }} {{ $isBlocked ? 'disabled' : '' }}" {!! $isBlocked ? 'onclick="return false;" style="opacity: 0.5; cursor: not-allowed;"' : '' !!}>
                        <i class="bi bi-building"></i>
                        <span>My Organization</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('organizer.events.index') }}" class="{{ request()->routeIs('organizer.events.*') ? 'active' : '' }} {{ $isBlocked ? 'disabled' : '' }}" {!! $isBlocked ? 'onclick="return false;" style="opacity: 0.5; cursor: not-allowed;"' : '' !!}>
                        <i class="bi bi-calendar-event"></i>
                        <span>My Events</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('organizer.blogs.index') }}" class="{{ request()->routeIs('organizer.blogs.*') ? 'active' : '' }} {{ $isBlocked ? 'disabled' : '' }}" {!! $isBlocked ? 'onclick="return false;" style="opacity: 0.5; cursor: not-allowed;"' : '' !!}>
                        <i class="bi bi-newspaper"></i>
                        <span>My Blogs</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('organizer.community') }}" class="{{ request()->routeIs('organizer.community') ? 'active' : '' }} {{ $isBlocked ? 'disabled' : '' }}" {!! $isBlocked ? 'onclick="return false;" style="opacity: 0.5; cursor: not-allowed;"' : '' !!}>
                        <i class="bi bi-people-fill"></i>
                        <span>Community</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('organizer.donations') }}" class="{{ request()->routeIs('organizer.donations') ? 'active' : '' }} {{ $isBlocked ? 'disabled' : '' }}" {!! $isBlocked ? 'onclick="return false;" style="opacity: 0.5; cursor: not-allowed;"' : '' !!}>
                        <i class="bi bi-cash-stack"></i>
                        <span>Donations</span>
                    </a>
                </li>
            </ul>

            <hr style="border-color: rgba(255,255,255,0.1); margin: 20px;">

            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('home') }}">
                        <i class="bi bi-arrow-left-circle"></i>
                        <span>Return To TounsiVert</span>
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                        @csrf
                        <button type="submit" class="btn btn-link text-decoration-none w-100 text-start" style="color: rgba(255,255,255,0.8); padding: 12px 20px;">
                            <i class="bi bi-box-arrow-right me-2" style="width: 25px;"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="organizer-content w-100">
            <!-- Header -->
            <div class="organizer-header">
                <div>
                    <h4 class="mb-0">@yield('page-title', 'Organizer Dashboard')</h4>
                    <small class="text-muted">@yield('page-subtitle', 'Manage your organizations and events')</small>
                </div>
                <div class="user-info">
                    <div>
                        <div class="text-end">
                            <strong>{{ auth()->user()->full_name }}</strong>
                        </div>
                        <small class="text-muted">Organizer · {{ auth()->user()->score }} pts</small>
                    </div>
                    <div class="user-avatar">
                        {{ strtoupper(substr(auth()->user()->first_name, 0, 1)) }}
                    </div>
                </div>
            </div>

            <!-- Alerts -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('info'))
                <div class="alert alert-info alert-dismissible fade show m-3" role="alert">
                    <i class="bi bi-info-circle me-2"></i>{{ session('info') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <!-- Page Content -->
            <div class="p-4">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>
</html>

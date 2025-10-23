<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel - TounsiVert')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .admin-sidebar {
            background: #212529;
            min-height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            width: 250px;
            padding-top: 20px;
            box-shadow: 2px 0 10px rgba(0,0,0,0.1);
        }
        .admin-content {
            margin-left: 250px;
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
        }
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 20px 0;
        }
        .sidebar-menu li a {
            color: rgba(255,255,255,0.7);
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
            background: rgba(255,255,255,0.1);
            color: #fff;
        }
        .sidebar-menu li a.active {
            border-left: 3px solid #52b788;
        }
        .admin-header {
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
        @media (max-width: 768px) {
            .admin-sidebar {
                width: 0;
                overflow: hidden;
            }
            .admin-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-brand">
                <img src="{{ asset('logo.png') }}" alt="TounsiVert Logo" style="height: 35px; width: auto; margin-right: 10px; filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));">
                <span>Admin Panel</span>
            </a>
            
            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2"></i>
                        <span>Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.organization-requests.index') }}" class="{{ request()->routeIs('admin.organization-requests.*') ? 'active' : '' }}">
                        <i class="bi bi-clipboard-check"></i>
                        <span>Org Requests</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.organizations.index') }}" class="{{ request()->routeIs('admin.organizations.*') ? 'active' : '' }}">
                        <i class="bi bi-building"></i>
                        <span>Organizations</span>
                    </a>
                </li>

                <li>
    <a href="{{ route('admin.vehicules.index') }}" class="{{ request()->routeIs('admin.vehicules.*') ? 'active' : '' }}">
        <i class="bi bi-truck"></i>
        <span>Vehicules</span>
    </a>
</li>



                <li>
                    <a href="{{ route('admin.events.index') }}" class="{{ request()->routeIs('admin.events.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event"></i>
                        <span>All Events</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.reports.index') }}" class="{{ request()->routeIs('admin.reports.*') ? 'active' : '' }}">
                        <i class="bi bi-flag"></i>
                        <span>Reports</span>
                    </a>
                </li>
                <li>
                    <a href="{{ route('admin.ai.dashboard') }}" class="{{ request()->routeIs('admin.ai.*') ? 'active' : '' }}" style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);">
                        <i class="bi bi-robot"></i>
                        <span>Intelligence IA</span>
                        <span class="badge bg-primary ms-auto" style="font-size: 0.7rem;">NEW</span>
                    </a>
                </li>
            </ul>

            <hr style="border-color: rgba(255,255,255,0.1); margin: 20px;">

            <ul class="sidebar-menu">
                <li>
                    <a href="{{ route('home') }}">
                        <i class="bi bi-house-door"></i>
                        <span>Back to Site</span>
                    </a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
                        @csrf
                        <button type="submit" class="btn btn-link text-decoration-none w-100 text-start" style="color: rgba(255,255,255,0.7); padding: 12px 20px;">
                            <i class="bi bi-box-arrow-right me-2" style="width: 25px;"></i>
                            <span>Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <div class="admin-content w-100">
            <!-- Header -->
            <div class="admin-header">
                <div>
                    <h4 class="mb-0">@yield('page-title', 'Admin Dashboard')</h4>
                    <small class="text-muted">@yield('page-subtitle', 'Manage your platform')</small>
                </div>
                <div class="user-info">
                    <div>
                        <div class="text-end">
                            <strong>{{ auth()->user()->full_name }}</strong>
                        </div>
                        <small class="text-muted">Administrator</small>
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

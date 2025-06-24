<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page-title', 'Admin Dashboard') - PawMatch</title>
    
    <!-- Styles -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/admin_dashboard.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @stack('styles')
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="sidebar">
            <a href="{{ route('admin.dashboard') }}" class="logo">
                <img src="{{ asset('images/logo.png') }}" alt="PawMatch Logo" class="h-8 w-auto">
                <span>PawMatch</span>
            </a>

            <nav>
                <ul class="nav-menu">
                    <li class="nav-item">
                        <a href="{{ route('admin.dashboard') }}" 
                           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                            <i class="fas fa-chart-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.users') }}" 
                           class="nav-link {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                            <i class="fas fa-users"></i>
                            <span>User Management</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.verifications') }}" 
                           class="nav-link {{ request()->routeIs('admin.verifications') ? 'active' : '' }}">
                            <i class="fas fa-check-circle"></i>
                            <span>Verifications</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.stray-reports') }}" 
                           class="nav-link {{ request()->routeIs('admin.stray-reports') ? 'active' : '' }}">
                            <i class="fas fa-paw"></i>
                            <span>Stray Reports</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('admin.settings') }}" 
                           class="nav-link {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                            <i class="fas fa-cog"></i>
                            <span>Settings</span>
                        </a>
                    </li>
                </ul>
            </nav>

            <div class="mt-auto">
                <form method="POST" action="{{ route('logout') }}" class="nav-menu">
                    @csrf
                    <li class="nav-item">
                        <button type="submit" class="nav-link w-full text-left">
                            <i class="fas fa-sign-out-alt"></i>
                            <span>Logout</span>
                        </button>
                    </li>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="admin-content">
    <!-- Top Navigation Bar -->
    @if (!Request::routeIs('admin.stray-reports'))
    <div class="top-bar">
        <div class="welcome-section">
            @if (Request::routeIs('admin.dashboard'))
                <h1 class="page-title">@yield('page-title', 'Dashboard')</h1>
                <p class="page-subtitle">@yield('page-subtitle', 'Welcome back, Admin')</p>
            @else
                <h1 class="page-title">@yield('page-title')</h1>
                <p class="page-subtitle">@yield('page-subtitle')</p>
            @endif
        </div>
        <div class="profile-section">
            @if(auth()->user()->profile_photo_path)
                <img src="{{ asset(auth()->user()->profile_photo_path) }}" alt="Profile Picture" class="profile-img">
            @else
                <div class="profile-img-placeholder">
                    {{ substr(auth()->user()->name, 0, 2) }}
                </div>
            @endif
            <div class="profile-info">
                <strong>{{ auth()->user()->name }}</strong>
                <span class="role-badge">Admin</span>
            </div>
        </div>
    </div>
    @endif

            <!-- Page Content -->
            <div class="content-wrapper">
                @yield('content')
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html> 
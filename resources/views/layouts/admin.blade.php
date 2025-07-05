<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('page-title', 'Admin Dashboard') - {{ $site_name ?? 'PawMatch' }}</title>

        <!-- Styles -->
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('css/admin/admin_dashboard.css') }}">
        <link rel="stylesheet" href="{{ asset('css/admin/admin_sidebar.css') }}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        @stack('styles')
    </head>
    @yield('scripts')

    <body>
        <div class="admin-layout">
            <!-- Sidebar (adopter-style for admin) -->
            <aside class="admin-sidebar" id="adminSidebar">
                <div class="sidebar-header">
                    <a href="{{ route('admin.dashboard') }}">
                        <img src="{{ asset('images/logo.png') }}" alt="PawMatch Logo"
                            style="height: 40px; display: block; margin: 0 auto;" />
                    </a>
                </div>
                <nav class="sidebar-nav">
                    <a href="{{ route('admin.dashboard') }}"
                        class="nav-item {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-chart-line"></i>
                        <span>Dashboard</span>
                    </a>
                    <a href="{{ route('admin.users') }}"
                        class="nav-item {{ request()->routeIs('admin.users') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>User Management</span>
                    </a>
                    <a href="{{ route('admin.verifications') }}"
                        class="nav-item {{ request()->routeIs('admin.verifications') ? 'active' : '' }}">
                        <i class="fas fa-check-circle"></i>
                        <span>Verifications</span>
                    </a>
                    <a href="{{ route('admin.stray-reports') }}"
                        class="nav-item {{ request()->routeIs('admin.stray-reports') ? 'active' : '' }}">
                        <i class="fas fa-paw"></i>
                        <span>Stray Reports</span>
                    </a>
                    <a href="{{ route('admin.settings') }}"
                        class="nav-item {{ request()->routeIs('admin.settings') ? 'active' : '' }}">
                        <i class="fas fa-cog"></i>
                        <span>Settings</span>
                    </a>
                </nav>
                <form method="POST" action="{{ route('logout') }}" style="margin-top:2rem;">
                    @csrf
                    <button type="submit" class="nav-item" style="width:100%;justify-content:left;">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Logout</span>
                    </button>
                </form>
            </aside>

            <!-- Main Content Area -->
            <div class="admin-content">
                <div class="content-wrapper">
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

                                <!-- Notification Bell -->
                                <div class="notification-bell-wrapper position-relative me-3">
                                    <i id="notification-bell" class="fas fa-bell fa-lg text-primary"
                                        style="cursor: pointer;"></i>

                                    @if (isset($pendingCount) && $pendingCount > 0)
                                        <span class="notification-bell">
                                            {{ $pendingCount }}
                                        </span>
                                    @endif
                                    <div id="notification-dropdown" class="notification-dropdown">
                                        @forelse ($pendingVerifications as $verification)
                                            <div class="notification-item">
                                                <strong>{{ $verification->shelter->shelter_name ?? 'Unknown Shelter' }}</strong>
                                                applied for verification.
                                                <small
                                                    class="text-muted">{{ $verification->created_at->diffForHumans() }}</small>
                                            </div>
                                        @empty
                                            <div class="notification-item text-muted">No new notifications</div>
                                        @endforelse
                                    </div>
                                </div>
                                <!-- Profile Picture and Name -->
                                @if (auth()->user()->profile_photo_path)
                                    <img src="{{ asset(auth()->user()->profile_photo_path) }}" alt="Profile Picture"
                                        class="profile-img">
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
                    @yield('content')
                </div>
            </div>
        </div>

        <!-- Scripts -->
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const bell = document.getElementById('notification-bell');
                const dropdown = document.getElementById('notification-dropdown');

                bell.addEventListener('click', () => {
                    dropdown.style.display = dropdown.style.display === 'block' ? 'none' : 'block';
                });

                document.addEventListener('click', (e) => {
                    if (!bell.contains(e.target) && !dropdown.contains(e.target)) {
                        dropdown.style.display = 'none';
                    }
                });
            });
        </script>
        <!-- <script src="{{ asset('js/app.js') }}"></script> -->
        @stack('scripts')
    </body>

</html>

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
        <!-- Hamburger Button and Overlay -->
        <button id="adminSidebarToggle" class="sidebar-hamburger" aria-label="Open navigation menu"
            style="display:none;position:fixed;top:24px;left:24px;z-index:1001;background:#fff;border:none;border-radius:8px;padding:8px 12px;box-shadow:0 2px 8px rgba(0,0,0,0.08);">
            <i class="fas fa-bars"></i>
        </button>
        <div id="adminSidebarOverlay" class="sidebar-overlay"
            style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.2);z-index:1000;opacity:0;transition:opacity 0.3s;">
        </div>

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
        <script>
            function toggleAdminSidebar(show) {
                const sidebar = document.getElementById('adminSidebar');
                const overlay = document.getElementById('adminSidebarOverlay');
                const mainContent = document.querySelector('.admin-content, .content-wrapper');
                if (show) {
                    sidebar.style.display = 'block';
                    setTimeout(() => {
                        sidebar.style.transform = 'translateX(0)';
                        sidebar.style.opacity = '1';
                        overlay.style.display = 'block';
                        overlay.style.opacity = '1';
                    }, 10);
                    document.body.style.overflow = 'hidden';
                    if (mainContent) {
                        mainContent.classList.add('dashboard-shifted');
                        mainContent.style.pointerEvents = 'none';
                    }
                } else {
                    sidebar.style.transform = 'translateX(-100%)';
                    sidebar.style.opacity = '0';
                    overlay.style.opacity = '0';
                    setTimeout(() => {
                        sidebar.style.display = '';
                        overlay.style.display = 'none';
                    }, 300);
                    document.body.style.overflow = '';
                    if (mainContent) {
                        mainContent.classList.remove('dashboard-shifted');
                        mainContent.style.pointerEvents = '';
                    }
                }
            }
            const sidebarToggle = document.getElementById('adminSidebarToggle');
            const sidebarOverlay = document.getElementById('adminSidebarOverlay');
            if (sidebarToggle && sidebarOverlay) {
                sidebarToggle.addEventListener('click', () => toggleAdminSidebar(true));
                sidebarOverlay.addEventListener('click', () => toggleAdminSidebar(false));
            }

            function handleAdminSidebarResize() {
                const sidebar = document.getElementById('adminSidebar');
                const sidebarToggle = document.getElementById('adminSidebarToggle');
                const mainContent = document.querySelector('.admin-content, .content-wrapper');
                const overlay = document.getElementById('adminSidebarOverlay');
                if (!sidebar || !sidebarToggle || !overlay) return;
                if (window.innerWidth <= 900) {
                    sidebar.style.display = 'none';
                    sidebar.style.transform = 'translateX(-100%)';
                    sidebar.style.opacity = '0';
                    sidebarToggle.style.display = 'block';
                    if (mainContent) {
                        mainContent.classList.remove('dashboard-shifted');
                        mainContent.style.pointerEvents = '';
                    }
                } else {
                    sidebar.style.display = '';
                    sidebar.style.transform = '';
                    sidebar.style.opacity = '';
                    sidebarToggle.style.display = 'none';
                    overlay.style.display = 'none';
                    overlay.style.opacity = '0';
                    document.body.style.overflow = '';
                    if (mainContent) {
                        mainContent.classList.remove('dashboard-shifted');
                        mainContent.style.pointerEvents = '';
                    }
                }
            }
            window.addEventListener('resize', handleAdminSidebarResize);
            document.addEventListener('DOMContentLoaded', handleAdminSidebarResize);
        </script>
        @stack('scripts')
    </body>

</html>

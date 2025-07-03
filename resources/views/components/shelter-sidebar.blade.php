<button id="sidebarToggle" class="sidebar-hamburger" aria-label="Open navigation menu">
    <i class="fas fa-bars"></i>
</button>
<div id="sidebarOverlay" class="sidebar-overlay"></div>
<aside class="shelter-sidebar" id="shelterSidebar">
    <div class="sidebar-header">
        <a href="{{ route('adopter.dashboard') }}">
            <img src="{{ asset('images/logo.png') }}" alt="PawMatch Logo"
                style="height: 40px; display: block; margin: 0 auto;" />
        </a>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('shelter.dashboard') }}"
            class="nav-item {{ request()->routeIs('shelter.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('shelter.pets') }}" class="nav-item {{ request()->routeIs('shelter.pets') ? 'active' : '' }}">
            <i class="fas fa-paw"></i>
            <span>Pet Management</span>
        </a>
        <a href="{{ route('shelter.pet_applications') }}"
            class="nav-item {{ request()->routeIs('shelter.pet_applications') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i>
            <span>Applications</span>
        </a>
        <a href="{{ route('shelter.stray-reports') }}"
            class="nav-item {{ request()->routeIs('shelter.stray-reports') ? 'active' : '' }}">
            <i class="fas fa-search"></i>
            <span>Stray Reports</span>
            @php
                $unreadCount = \DB::table('stray_report_notifications')
                    ->where('shelter_id', auth()->user()->shelter->shelter_id ?? 0)
                    ->where('is_read', false)
                    ->count();
            @endphp
            @if ($unreadCount > 0)
                <span class="notification-badge">{{ $unreadCount }}</span>
            @endif
        </a>
        <a href="{{ route('shelter.messages') }}"
            class="nav-item {{ request()->routeIs('shelter.messages') ? 'active' : '' }}">
            <i class="fas fa-envelope"></i>
            <span>Messages</span>
        </a>
        <a href="{{ route('shelter.profile') }}"
            class="nav-item {{ request()->routeIs('shelter.profile') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>Profile</span>
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
<script>
    function toggleSidebar(show) {
        const sidebar = document.getElementById('shelterSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const mainContent = document.querySelector('.main-content, .shelter-content, .content-wrapper');
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
            }, 300); // match CSS transition duration
            document.body.style.overflow = '';
            if (mainContent) {
                mainContent.classList.remove('dashboard-shifted');
                mainContent.style.pointerEvents = '';
            }
        }
    }
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    if (sidebarToggle && sidebarOverlay) {
        sidebarToggle.addEventListener('click', () => toggleSidebar(true));
        sidebarOverlay.addEventListener('click', () => toggleSidebar(false));
    }

    function handleResize() {
        const sidebar = document.getElementById('shelterSidebar');
        const sidebarToggle = document.getElementById('sidebarToggle');
        const mainContent = document.querySelector('.main-content, .shelter-content, .content-wrapper');
        const overlay = document.getElementById('sidebarOverlay');
        if (!sidebar || !sidebarToggle || !overlay) return; // Prevent errors if any element is missing
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
    window.addEventListener('resize', handleResize);
    document.addEventListener('DOMContentLoaded', handleResize);
</script>

<aside class="shelter-sidebar" id="shelterSidebar">
    <div class="shelter-sidebar-header">
        <h2 class="sidebar-title">Shelter Dashboard</h2>
    </div>
    <nav class="shelter-sidebar-nav">
        <a href="{{ route('shelter.dashboard') }}" class="shelter-nav-item {{ request()->routeIs('shelter.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span class="nav-label">Dashboard</span>
        </a>
        <a href="{{ route('shelter.pets') }}" class="shelter-nav-item {{ request()->routeIs('shelter.pets') ? 'active' : '' }}">
            <i class="fas fa-paw"></i>
            <span class="nav-label">Pet Management</span>
        </a>
        <a href="{{ route('shelter.pet_applications') }}" class="shelter-nav-item {{ request()->routeIs('shelter.pet_applications') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i>
            <span class="nav-label">Applications</span>
        </a>
         <!-- NEWLY ADDED BY ANDREA 1224-->
        <a href="{{ route('shelter.stray-reports') }}" class="shelter-nav-item {{ request()->routeIs('shelter.stray-reports') ? 'active' : '' }}">
                <i class="fas fa-search"></i>
                <span>Stray Reports</span>
                @php
                    $unreadCount = \DB::table('stray_report_notifications')
                        ->where('shelter_id', auth()->user()->shelter->shelter_id ?? 0)
                        ->where('is_read', false)
                        ->count();
                @endphp
                @if($unreadCount > 0)
                    <span class="notification-badge">{{ $unreadCount }}</span>
                @endif
        </a>
        <a href="{{ route('shelter.messages') }}" class="shelter-nav-item {{ request()->routeIs('shelter.messages') ? 'active' : '' }}">
            <i class="fas fa-envelope"></i>
            <span class="nav-label">Messages</span>
        </a>
        <a href="{{ route('shelter.profile') }}" class="shelter-nav-item {{ request()->routeIs('shelter.profile') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span class="nav-label">Profile</span>
        </a>
    </nav>
    <form method="POST" action="{{ route('logout') }}" style="margin-top:2rem;">
        @csrf
        <button type="submit" class="shelter-nav-item" style="width:100%;justify-content:left;">
            <i class="fas fa-sign-out-alt"></i>
            <span class="nav-label">Logout</span>
        </button>
    </form>
</aside>
<div id="sidebarBackdrop" class="sidebar-backdrop hide"></div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('shelterSidebar');
        function isMobile() {
            return window.innerWidth <= 900;
        }
        function updateSidebarState() {
            if (isMobile()) {
                sidebar.classList.add('collapsed');
            } else {
                sidebar.classList.remove('collapsed');
            }
        }
        window.addEventListener('resize', updateSidebarState);
        updateSidebarState(); // Initial check
    });
</script>

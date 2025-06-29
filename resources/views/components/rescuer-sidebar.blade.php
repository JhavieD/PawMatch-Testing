<aside class="rescuer-sidebar" id="rescuerSidebar">
    <div class="rescuer-sidebar-header">
        <h2 class="sidebar-title">Rescuer Dashboard</h2>
    </div>
    <nav class="rescuer-sidebar-nav">
        <a href="{{ route('rescuer.dashboard') }}" class="rescuer-nav-item {{ request()->routeIs('rescuer.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
            <span class="nav-label">Dashboard</span>
        </a>
        <a href="{{ route('rescuer.pet-management') }}" class="rescuer-nav-item {{ request()->routeIs('rescuer.pet-management') ? 'active' : '' }}">
            <i class="fas fa-paw"></i>
            <span class="nav-label">Pet Management</span>
        </a>
        <a href="{{ route('rescuer.pet_applications') }}" class="rescuer-nav-item {{ request()->routeIs('rescuer.pet_applications') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i>
            <span class="nav-label">Applications</span>
        </a>
        <a href="{{ route('rescuer.messages') }}" class="rescuer-nav-item {{ request()->routeIs('rescuer.messages') ? 'active' : '' }}">
            <i class="fas fa-envelope"></i>
            <span class="nav-label">Messages</span>
        </a>
        <a href="{{ route('rescuer.profile') }}" class="rescuer-nav-item {{ request()->routeIs('rescuer.profile') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span class="nav-label">Profile</span>
        </a>
    </nav>
    <form method="POST" action="{{ route('logout') }}" style="margin-top:2rem;">
        @csrf
        <button type="submit" class="rescuer-nav-item" style="width:100%;justify-content:left;">
            <i class="fas fa-sign-out-alt"></i>
            <span class="nav-label">Logout</span>
        </button>
    </form>
</aside>
<div id="sidebarBackdrop" class="sidebar-backdrop hide"></div>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sidebar = document.getElementById('rescuerSidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        function isMobile() {
            return window.innerWidth <= 900;
        }
        toggleBtn.addEventListener('click', function(e) {
            if (isMobile()) {
                sidebar.classList.toggle('expanded');
            }
        });
        window.addEventListener('resize', function() {
            if (!isMobile()) {
                sidebar.classList.remove('expanded');
            }
        });
    });
</script>

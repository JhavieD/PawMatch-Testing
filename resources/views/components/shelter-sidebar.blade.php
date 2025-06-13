<aside class="shelter-sidebar">
    <div class="shelter-sidebar-header">
        <h2>Shelter Dashboard</h2>
    </div>
    <nav class="shelter-sidebar-nav">
        <a href="{{ route('shelter.dashboard') }}" class="shelter-nav-item {{ request()->routeIs('shelter.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('shelter.pets') }}" class="shelter-nav-item {{ request()->routeIs('shelter.pets') ? 'active' : '' }}">
            <i class="fas fa-paw"></i>
            <span>Pet Management</span>
        </a>
        <a href="{{ route('shelter.pet_applications') }}" class="shelter-nav-item {{ request()->routeIs('shelter.pet_applications') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i>
            <span>Applications</span>
        </a>
        <a href="{{ route('shelter.messages') }}" class="shelter-nav-item {{ request()->routeIs('shelter.messages') ? 'active' : '' }}">
            <i class="fas fa-envelope"></i>
            <span>Messages</span>
        </a>
        <a href="{{ route('shelter.profile') }}" class="shelter-nav-item {{ request()->routeIs('shelter.profile') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </nav>
    <form method="POST" action="{{ route('logout') }}" style="margin-top:2rem;">
        @csrf
        <button type="submit" class="shelter-nav-item" style="width:100%;justify-content:left;">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </button>
    </form>
</aside>

<aside class="adopter-sidebar">
    <div class="sidebar-header">
        <h2>Adopter Dashboard</h2>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('adopter.dashboard') }}" class="nav-item {{ request()->routeIs('adopter.dashboard') ? 'active' : '' }}">
            <i class="fas fa-home"></i>
            <span>Dashboard</span>
        </a>
        <a href="{{ route('adopter.pet-listings') }}" class="nav-item {{ request()->routeIs('adopter.pet-listings') ? 'active' : '' }}">
            <i class="fas fa-paw"></i>
            <span>Browse Pets</span>
        </a>
        <a href="{{ route('adopter.pet-personality-quiz') }}" class="nav-item {{ request()->routeIs('adopter.pet-personality-quiz') ? 'active' : '' }}">
            <i class="fas fa-question-circle"></i>
            <span>Personality Quiz</span>
        </a>
        <a href="{{ route('adopter.profile') }}" class="nav-item {{ request()->routeIs('adopter.profile') ? 'active' : '' }}">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
        <a href="{{ route('adopter.application-status') }}" class="nav-item {{ request()->routeIs('adopter.application-status') ? 'active' : '' }}">
            <i class="fas fa-clipboard-list"></i>
            <span>Applications</span>
        </a>
        <a href="{{ route('adopter.messages') }}" class="nav-item {{ request()->routeIs('adopter.messages') ? 'active' : '' }}">
            <i class="fas fa-envelope"></i>
            <span>Messages</span>
        </a>
        <a href="{{ route('adopter.report-stray') }}" class="nav-item {{ request()->routeIs('adopter.report-stray') ? 'active' : '' }}">
    <i class="fas fa-exclamation-triangle"></i>
    <span>Report a Stray</span>
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
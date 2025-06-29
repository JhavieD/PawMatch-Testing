
<aside class="shelter-sidebar">
    <div class="shelter-sidebar-header">
        <h2>Shelter Dashboard</h2>
    </div>
    <nav class="shelter-sidebar-nav">
        <a href="{{ route('shelter.dashboard') }}" class="shelter-nav-item {{ request()->routeIs('shelter.dashboard') ? 'active' : '' }}">
            <i class="fas fa-tachometer-alt"></i>
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
        <!-- NEWLY ADDED BY ANDREA -->
         <a href="{{ route('shelter.stray-reports') }}" class="shelter-nav-item {{ request()->routeIs('shelter.stray-reports') ? 'active' : '' }}">
            <i class="fas fa-search"></i>
            <span>Stray Reports</span>
            @php
                $shelter = auth()->user()->shelter;
                $unreadCount = 0;
                if ($shelter) {
                    $unreadCount = \DB::table('stray_report_notifications')
                        ->where('shelter_id', $shelter->shelter_id)
                        ->where('is_read', false)
                        ->count();
                }
            @endphp
            @if($unreadCount > 0)
                <span class="notification-badge">{{ $unreadCount }}</span>
            @endif
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
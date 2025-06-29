<button id="sidebarToggle" class="sidebar-hamburger" aria-label="Open navigation menu" style="display:none;position:fixed;top:1rem;left:1rem;z-index:1001;background:none;border:none;font-size:2rem;">
    <i class="fas fa-bars"></i>
</button>
<div id="sidebarOverlay" class="sidebar-overlay" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);z-index:1000;"></div>
<aside class="adopter-sidebar" id="adopterSidebar">
    <div class="sidebar-header">
        <a href="{{ route('adopter.dashboard') }}">
            <img src="{{ asset('images/logo.png') }}" alt="PawMatch Logo" style="height: 40px; display: block; margin: 0 auto;" />
        </a>
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
@push('scripts')
<script>
function toggleSidebar(show) {
    const sidebar = document.getElementById('adopterSidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const mainContent = document.querySelector('.main-content, .adopter-content, .content-wrapper');
    if (show) {
        sidebar.style.display = 'block';
        overlay.style.display = 'block';
        document.body.style.overflow = 'hidden';
        if (mainContent) {
            mainContent.classList.add('dashboard-shifted');
            mainContent.style.pointerEvents = 'none';
        }
    } else {
        sidebar.style.display = '';
        overlay.style.display = 'none';
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
    const sidebar = document.getElementById('adopterSidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('.main-content, .adopter-content, .content-wrapper');
    if (window.innerWidth <= 900) {
        sidebar.style.display = 'none';
        sidebarToggle.style.display = 'block';
        if (mainContent) {
            mainContent.classList.remove('dashboard-shifted');
            mainContent.style.pointerEvents = '';
        }
    } else {
        sidebar.style.display = '';
        sidebarToggle.style.display = 'none';
        document.getElementById('sidebarOverlay').style.display = 'none';
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
@endpush 
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">  {{-- pls don't remove yaw q na mag debug ng messages -allainne --}}
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', $site_name . ' - Find Your Perfect Pet Companion')</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
        <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('css/shared/navbar.css') }}">
        <link rel="stylesheet" href="{{ asset('css/app.css') }}">
        <link rel="stylesheet" href="{{ asset('css/marketing.css') }}">
        @yield('styles')
        @stack('styles')
    </head>
    <body>
        <!-- Hamburger Menu Button (only for guests/public pages) -->
        @guest
        <button id="navbarToggle" class="navbar-hamburger" aria-label="Open navigation menu">
            <i class="fas fa-bars"></i>
        </button>
        @endguest
        @section('navbar')
        <!-- Navigation -->
        <nav class="navbar">
            <div class="nav-content">
                <a href="{{ route('home') }}" class="logo">
                    <img src="{{ asset('images/logo.png') }}" alt="PawMatch Logo" style="height: 40px;">
                </a>
                <div class="nav-links" id="navLinks">
                    <a href="{{ route('home') }}" class="nav-link{{ request()->routeIs('home') ? ' active' : '' }}">Home</a>
                    <a href="{{ route('about') }}" class="nav-link{{ request()->routeIs('about') ? ' active' : '' }}">About</a>
                    <a href="{{ route('public.pet-listings') }}" class="nav-link{{ request()->routeIs('public.pet-listings') ? ' active' : '' }}">Find Pets</a>
                    <a href="{{ route('faq') }}" class="nav-link{{ request()->routeIs('faq') ? ' active' : '' }}">FAQ</a>
                    <a href="{{ route('terms') }}" class="nav-link{{ request()->routeIs('terms') ? ' active' : '' }}">Terms</a>
                    @auth
                        <a href="{{ route('dashboard.redirect') }}" class="btn btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary{{ request()->routeIs('login') ? ' active' : '' }}">Login</a>
                    @endauth
                </div>
            </div>
        </nav>
        
        <!-- Mobile Navigation Overlay -->
        <div id="navbarOverlay" class="navbar-overlay"></div>
        
        <!-- Mobile Navigation Menu -->
        <aside id="mobileNav" class="mobile-nav">
            <div class="mobile-nav-header">
                <a href="{{ route('home') }}" class="mobile-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="PawMatch Logo" />
                </a>
                <button id="mobileNavClose" class="mobile-nav-close" aria-label="Close navigation menu">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <nav class="mobile-nav-links">
                <a href="{{ route('home') }}" class="mobile-nav-link{{ request()->routeIs('home') ? ' active' : '' }}">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="{{ route('about') }}" class="mobile-nav-link{{ request()->routeIs('about') ? ' active' : '' }}">
                    <i class="fas fa-info-circle"></i>
                    <span>About</span>
                </a>
                <a href="{{ route('public.pet-listings') }}" class="mobile-nav-link{{ request()->routeIs('public.pet-listings') ? ' active' : '' }}">
                    <i class="fas fa-paw"></i>
                    <span>Find Pets</span>
                </a>
                <a href="{{ route('faq') }}" class="mobile-nav-link{{ request()->routeIs('faq') ? ' active' : '' }}">
                    <i class="fas fa-question-circle"></i>
                    <span>FAQ</span>
                </a>
                <a href="{{ route('terms') }}" class="mobile-nav-link{{ request()->routeIs('terms') ? ' active' : '' }}">
                    <i class="fas fa-file-contract"></i>
                    <span>Terms</span>
                </a>
                @auth
                    <a href="{{ route('dashboard.redirect') }}" class="mobile-nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="mobile-nav-link{{ request()->routeIs('login') ? ' active' : '' }}">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                @endauth
            </nav>
        </aside>
        @show

    <!-- Main Content -->
    @yield('content')

    @stack('scripts')
    
    <!-- Mobile Navigation Script -->
    <script>
        function toggleMobileNav(show) {
            const mobileNav = document.getElementById('mobileNav');
            const overlay = document.getElementById('navbarOverlay');
            const body = document.body;
            
            if (show) {
                mobileNav.style.transform = 'translateX(0)';
                overlay.style.display = 'block';
                body.style.overflow = 'hidden';
                body.classList.add('mobile-nav-open');
            } else {
                mobileNav.style.transform = 'translateX(-100%)';
                overlay.style.display = 'none';
                body.style.overflow = '';
                body.classList.remove('mobile-nav-open');
            }
        }
        
        // Event listeners
        const navbarToggle = document.getElementById('navbarToggle');
        const mobileNavClose = document.getElementById('mobileNavClose');
        const overlay = document.getElementById('navbarOverlay');
        
        if (navbarToggle) {
            navbarToggle.addEventListener('click', () => toggleMobileNav(true));
        }
        
        if (mobileNavClose) {
            mobileNavClose.addEventListener('click', () => toggleMobileNav(false));
        }
        
        if (overlay) {
            overlay.addEventListener('click', () => toggleMobileNav(false));
        }
        
        // Handle window resize
        function handleResize() {
            const mobileNav = document.getElementById('mobileNav');
            const overlay = document.getElementById('navbarOverlay');
            
            if (window.innerWidth > 768) {
                mobileNav.style.transform = 'translateX(-100%)';
                overlay.style.display = 'none';
                document.body.style.overflow = '';
            }
        }
        
        window.addEventListener('resize', handleResize);
        document.addEventListener('DOMContentLoaded', handleResize);
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>@yield('title', 'PawMatch - Find Your Perfect Pet Companion')</title>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ asset('css/navbar.css') }}">
        @yield('styles')
    </head>
    <body>
        @section('navbar')
        <!-- Navigation -->
        <nav class="navbar">
            <div class="nav-content">
                <a href="{{ route('home') }}" class="logo"><img src="{{ asset('images/logo.png') }}" alt="PawMatch Logo" style="height: 40px;"></a>
                <div class="nav-links">
                    <a href="{{ route('about') }}">About</a>
                    <a href="{{ route('pet-listings') }}">Find Pets</a>
                    <a href="{{ route('faq') }}">FAQ</a>
                    <a href="{{ route('terms') }}">Terms</a>
                    @auth
                        <a href="{{ route('dashboard.redirect') }}" class="btn btn-primary">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                    @endauth
                </div>
            </div>
        </nav>
        @show

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        @stack('scripts')
    </body>
</html>

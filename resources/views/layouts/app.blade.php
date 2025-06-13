<!DOCTYPE html>
<<<<<<< feature/reset-password-email
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased">
    <div class="min-h-screen bg-gray-100">
        <livewire:layout.navigation />

        <!-- Page Heading -->
        @if (isset($header))
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            @yield('content')
        </main>
    </div>
</body>
=======
<html lang="en">
    <head>
        <meta charset="UTF-8">
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
>>>>>>> main
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Rescuer Dashboard - PawMatch')</title>
    <link rel="stylesheet" href="{{ asset('css/rescuer.css') }}">
    <link rel="stylesheet" href="{{ asset('css/adopter.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="adopter-sidebar">
        <div class="sidebar-header">
            <h2>Rescuer Dashboard</h2>
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('rescuer.dashboard') }}" class="nav-item {{ request()->routeIs('rescuer.dashboard') ? 'active' : '' }}">
                <i class="fas fa-home"></i>
                Dashboard
            </a>
            <a href="{{ route('rescuer.profile') }}" class="nav-item {{ request()->routeIs('rescuer.profile') ? 'active' : '' }}">
                <i class="fas fa-user"></i>
                Profile
            </a>
            <form method="POST" action="{{ route('logout') }}" style="margin-top: auto;">
                @csrf
                <button type="submit" class="nav-item" style="width: 100%; background: none; border: none; text-align: left;">
                    <i class="fas fa-sign-out-alt"></i>
                    Logout
                </button>
            </form>
        </nav>
    </div>

    @yield('rescuer-content')

    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Adopter Dashboard - PawMatch</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: #f0f2f5;
            min-height: 100vh;
        }

        /* Sidebar Styles */
        .sidebar {
            background: white;
            padding: 2rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            width: 250px;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #4a90e2;
            text-decoration: none;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.8rem;
            color: #4b5563;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s ease;
        }

        .nav-link:hover {
            background: #f3f4f6;
            color: #4a90e2;
        }

        .nav-link.active {
            background: #e3f2fd;
            color: #4a90e2;
        }

        /* Main Content Wrapper */
        .main-content {
            margin-left: 250px;
            padding: 2rem;
        }

        /* Centering Container */
        .content-wrapper {
            max-width: 900px;
            margin: 0 auto;
        }

        .top-bar {
            background: white;
            padding: 1.5rem 2rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .welcome-section h1 {
            color: #1a1a1a;
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .welcome-section p {
            color: #6b7280;
        }

        .profile-section {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #e5e7eb;
            overflow: hidden;
        }

        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 1.5rem;
        }

        .content-card {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .card-header h2 {
            color: #1a1a1a;
            font-size: 1.25rem;
            font-weight: 600;
        }

        /* Pet Grid */
        .pet-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
            gap: 1rem;
        }

        .pet-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            transition: transform 0.2s ease;
        }

        .pet-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .pet-image {
            width: 100%;
            height: 120px;
            object-fit: cover;
        }

        .pet-info {
            padding: 1rem;
        }

        .pet-name {
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 0.25rem;
        }

        .pet-details {
            color: #6b7280;
            font-size: 0.875rem;
        }

        /* Application List */
        .application-list {
            list-style: none;
        }

        .application-item {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .application-item:last-child {
            border-bottom: none;
        }

        .status {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.875rem;
            font-weight: 500;
            margin-top: 0.5rem;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background: #d1fae5;
            color: #065f46;
        }

        /* Message List */
        .message-list {
            list-style: none;
        }

        .message-item {
            padding: 1rem;
            border-bottom: 1px solid #e5e7eb;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .message-item:hover {
            background: #f3f4f6;
        }

        .message-item:last-child {
            border-bottom: none;
        }

        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 0.5rem;
        }

        .message-sender {
            font-weight: 600;
            color: #1a1a1a;
        }

        .message-time {
            color: #6b7280;
            font-size: 0.875rem;
        }

        .message-preview {
            color: #6b7280;
            font-size: 0.875rem;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .btn {
            display: inline-block;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .btn-primary {
            background: #4a90e2;
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: #357abd;
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #4a90e2;
            color: #4a90e2;
        }

        .btn-outline:hover {
            background: #e3f2fd;
        }

        @media (max-width: 768px) {
            .sidebar {
                display: none;
            }

            .main-content {
                margin-left: 0;
                padding: 1rem;
            }

            .content-wrapper {
                max-width: 100%;
            }

            .top-bar {
                flex-direction: column;
                gap: 1rem;
                align-items: flex-start;
            }

            .pet-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="sidebar">
        <a href="{{ route('adopter.dashboard') }}" class="logo">🐾 PawMatch</a>
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="{{ route('adopter.dashboard') }}" class="nav-link active">Dashboard</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('pets.index') }}" class="nav-link">Find Pets</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('applications.index') }}" class="nav-link">My Applications</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('messages.index') }}" class="nav-link">Messages</a>
            </li>
            <li class="nav-item">
                <a href="{{ route('profile.edit') }}" class="nav-link">Profile</a>
            </li>
            <li class="nav-item">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="nav-link" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer;">Logout</button>
                </form>
            </li>
        </ul>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Centering Wrapper -->
        <div class="content-wrapper">
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="welcome-section">
                    <h1>Welcome, {{ auth()->user()->first_name }}!</h1>
                    <p>Here's what's happening with your pet adoption journey</p>
                </div>
                <div class="profile-section">
                    <div class="profile-img">
                        <img src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->first_name }}" style="width: 100%; height: 100%; object-fit: cover;" />
                    </div>
                    <div class="profile-info">
                        <strong>{{ auth()->user()->first_name }} {{ auth()->user()->last_name }}</strong>
                    </div>
                </div>
            </div>

            <div class="content-grid">
                <!-- Favorite Pets -->
                <div class="content-card">
                    <div class="card-header">
                        <h2>Favorite Pets</h2>
                        <a href="{{ route('pets.index') }}" class="btn btn-outline">Find More</a>
                    </div>
                    <div class="pet-grid">
                        @forelse($favoritePets as $pet)
                            <div class="pet-card">
                                <img src="{{ $pet->image_url }}" alt="{{ $pet->name }}" class="pet-image" />
                                <div class="pet-info">
                                    <div class="pet-name">{{ $pet->name }}</div>
                                    <div class="pet-details">{{ $pet->breed }} • {{ $pet->age }} years</div>
                                </div>
                            </div>
                        @empty
                            <p>No favorite pets yet. Start exploring to find your perfect match!</p>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Applications -->
                <div class="content-card">
                    <div class="card-header">
                        <h2>Recent Applications</h2>
                        <a href="{{ route('applications.index') }}" class="btn btn-outline">View All</a>
                    </div>
                    <ul class="application-list">
                        @forelse($recentApplications as $application)
                            <li class="application-item">
                                <div class="pet-name">{{ $application->pet->name }} - {{ $application->pet->breed }}</div>
                                <div class="pet-details">{{ $application->shelter->name }}</div>
                                <span class="status status-{{ strtolower($application->status) }}">{{ $application->status }}</span>
                            </li>
                        @empty
                            <li class="application-item">No applications yet.</li>
                        @endforelse
                    </ul>
                </div>

                
                </div>
            </div>
        </div>
    </main>
</body>
</html>
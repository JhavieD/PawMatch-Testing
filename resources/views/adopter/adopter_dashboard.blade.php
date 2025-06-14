@extends('layouts.adopter')

@section('title', 'Adopter Dashboard - PawMatch')

@section('adopter-content')
<main class="main-content">
    <!-- Centering Wrapper -->
    <div class="content-wrapper">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="welcome-section">
                <h1>Welcome, {{ auth()->user()->name }}!</h1>
                <p>Here's what's happening with your pet adoption journey</p>
            </div>
            <div class="profile-section">
                <div class="profile-img">
                    <img src="{{ auth()->user()->profile_image ?? asset('images/default-profile.png') }}" style="width: 100%; height: 100%; border-radius: 50%;" />
                </div>
                <div class="profile-info">
                    <strong>{{ auth()->user()->name }}</strong>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <!-- Favorite Pets -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Favorite Pets</h2>
                    <a href="{{ route('adopter.pet-listings') }}" class="btn btn-outline">Find More</a>
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
                    <div class="no-pets-message">
                        <p>You haven't saved any pets to your favorites yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Applications -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Recent Applications</h2>
                    <a href="{{ route('adopter.application-status') }}" class="btn btn-outline">View All</a>
                </div>
                <ul class="application-list">
                    @forelse($recentApplications as $application)
                    <li class="application-item">
                        <div class="pet-name">{{ $application->pet->name }} - {{ $application->pet->breed }}</div>
                        <div class="pet-details">{{ $application->pet->shelter->name }}</div>
                        <span class="status status-{{ strtolower($application->status) }}">{{ $application->status }}</span>
                    </li>
                    @empty
                    <li class="application-item">
                        <div class="no-applications-message">
                            <p>You haven't submitted any applications yet.</p>
                        </div>
                    </li>
                    @endforelse
                </ul>
            </div>

            <!-- Recent Messages -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Recent Messages</h2>
                    <a href="{{ route('adopter.messages') }}" class="btn btn-outline">View All</a>
                </div>
                <ul class="message-list">
                    @forelse($recentMessages as $message)
                    <li class="message-item">
                        <div class="message-header">
                            <span class="message-sender">{{ $message->sender->name }}</span>
                            <span class="message-time">{{ $message->created_at->format('g:i A') }}</span>
                        </div>
                        <div class="message-preview">
                            {{ Str::limit($message->content, 100) }}
                        </div>
                    </li>
                    @empty
                    <li class="message-item">
                        <div class="no-messages-message">
                            <p>You don't have any messages yet.</p>
                        </div>
                    </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div><!-- .content-wrapper -->
</main>
@endsection
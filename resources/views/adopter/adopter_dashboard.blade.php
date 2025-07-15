@extends('layouts.adopter')

@section('title', 'Adopter Dashboard - PawMatch')

@section('adopter-content')

    <main class="main-content">
        <!-- Centering Wrapper -->
        <div class="content-wrapper"">
            <!-- Top Bar -->
            <div class="top-bar"
                style=" display: flex; align-items: center; justify-content: space-between; background: #fff; border-radius: 16px; box-shadow: 0 2px 12px rgba(74,144,226,0.07); padding: 1.2rem 2.5rem 1.2rem 2.5rem; min-height: 64px;">
                <div class="welcome-section" style="margin-left: 0.5rem;">
                    <h1>Welcome, {{ $user->first_name ?? 'User' }}!</h1>
                    <p>Here's what's happening with your pet adoption journey</p>
                </div>
                <div class="profile-section"
                    style="display: flex; align-items: center; gap: 2rem; position: relative; z-index: 3000;">
                    <div class="profile-img">
                        <img src="{{ $user->profile_image }}"
                            alt="{{ $user->first_name }} {{ $user->last_name }}'s profile photo"
                            style="width: 100%; height: 100%; border-radius: 50%; font-family: 'Inter', sans-serif;" />
                    </div>
                    @include('components.notification-bell')
                </div>
            </div>

            <div class="content-grid">
                <!-- Favorite Pets -->
                <div class="content-card" id="favoritePetsSection">
                    <div class="card-header">
                        <div class="card-header-row">
                            <h2>Favorite Pets</h2>
                            <a href="{{ route('adopter.pet-listings') }}" class="btn btn-outline card-header-btn"
                                aria-label="Find more pets">Find More</a>
                        </div>
                    </div>
                    <div class="pet-grid">
                        @forelse($favoritePets as $pet)
                            <a href="{{ route('adopter.pet-listings') }}?pet_id={{ $pet->pet_id ?? $pet->id }}"
                                class="pet-card favorite-pet-card" data-pet-id="{{ $pet->pet_id ?? $pet->id }}"
                                style="display: flex; align-items: center; gap: 1rem; padding: 0.5rem 0; border: none; box-shadow: none; cursor: pointer; text-decoration: none;">
                                <img src="{{ $pet->images->first()->image_url ?? asset('images/default-pet.png') }}"
                                    alt="{{ $pet->name }}" class="pet-image"
                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; font-family: 'Inter', sans-serif; margin: 0;" />
                                <div class="pet-info" style="flex: 1;">
                                    <div class="pet-name" style="color:#1a1a1a; font-size: 1rem; font-weight: 500;">
                                        {{ $pet->name }}</div>
                                </div>
                            </a>
                        @empty
                            <div class="no-pets-message">
                                <p>No favorite pets yet.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                <!-- Recent Applications -->
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-header-row">
                            <h2>Recent Applications</h2>
                            <a href="{{ route('adopter.application-status') }}" class="btn btn-outline card-header-btn"
                                aria-label="View all applications">View All</a>
                        </div>
                    </div>
                    <ul class="application-list">
                        @forelse($recentApplications as $application)
                            <li class="application-item">
                                <a href="{{ route('adopter.application-status') }}?application_id={{ $application->id }}"
                                    style="text-decoration: none; color: inherit; display: block;">
                                    <div class="pet-name">{{ $application->pet->name ?? 'Unknown Pet' }} -
                                        {{ $application->pet->breed ?? '' }}</div>
                                    <div class="pet-details">
                                        {{ $application->pet->shelter->shelter_name ?? 'Unknown Shelter' }}</div>
                                    <span class="status status-{{ $application->status }}">
                                        @if ($application->status === 'pending')
                                            Application Pending
                                        @elseif($application->status === 'approved')
                                            Approved - Schedule Visit
                                        @elseif($application->status === 'completed')
                                            Adoption Completed
                                        @else
                                            {{ ucfirst($application->status) }}
                                        @endif
                                    </span>
                                </a>
                            </li>
                        @empty
                            <li class="application-item">
                                <div class="pet-name">No recent applications.</div>
                            </li>
                        @endforelse
                    </ul>
                </div>

                <!-- Recent Messages -->
                <div class="content-card">
                    <div class="card-header">
                        <div class="card-header-row">
                            <h2>Recent Messages</h2>
                            <a href="{{ route('adopter.messages') }}" class="btn btn-outline card-header-btn"
                                aria-label="View all messages">View All</a>
                        </div>
                    </div>
                    <ul class="message-list">
                        @forelse($recentMessages as $message)
                            <li class="message-item">
                                <div class="message-header">
                                    <span class="message-sender">{{ $message->sender->name ?? 'Unknown Sender' }}</span>
                                    <span class="message-time">{{ $message->created_at->format('g:i A') }}</span>
                                </div>
                                <div class="message-preview">
                                    {{ $message->content }}
                                </div>
                            </li>
                        @empty
                            <li class="message-item">
                                <div class="message-header">
                                    <span class="message-sender">No recent messages.</span>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div><!-- .content-wrapper -->
    </main>
@endsection

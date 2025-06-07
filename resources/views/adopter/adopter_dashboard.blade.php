@extends('layouts.adopter')

@section('title', 'Adopter Dashboard - PawMatch')

@section('adopter-content')
    <div class="dashboard-header">
        <h1>Welcome, {{ auth()->user()->name }}!</h1>
        <p>Here's an overview of your pet adoption journey</p>
    </div>

    <div class="dashboard-grid">
        <div class="dashboard-card">
            <h3>Favorite Pets</h3>
            <div class="pet-grid">
                @forelse($favoritePets as $pet)
                    <div class="pet-card">
                        <img src="{{ $pet->image_url }}" alt="{{ $pet->name }}" class="pet-image">
                        <div class="pet-info">
                            <h3>{{ $pet->name }}</h3>
                            <p>{{ $pet->breed }} • {{ $pet->age }}</p>
                        </div>
                    </div>
                @empty
                    <p>No favorite pets yet. Start browsing to find your perfect match!</p>
                @endforelse
            </div>
        </div>

        <div class="dashboard-card">
            <h3>Recent Applications</h3>
            <div class="application-list">
                @forelse($recentApplications as $application)
                    <div class="application-item">
                        <h4>{{ $application->pet->name }}</h4>
                        <p>Status: <span class="status-{{ $application->status }}">{{ $application->status }}</span></p>
                        <p>Submitted: {{ $application->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p>No applications yet. Find a pet you love and submit an application!</p>
                @endforelse
            </div>
        </div>

        <div class="dashboard-card">
            <h3>Recent Messages</h3>
            <div class="message-list">
                @forelse($recentMessages as $message)
                    <div class="message-item">
                        <h4>{{ $message->sender->name }}</h4>
                        <p>{{ Str::limit($message->content, 50) }}</p>
                        <p>{{ $message->created_at->diffForHumans() }}</p>
                    </div>
                @empty
                    <p>No messages yet. Start a conversation with a shelter!</p>
                @endforelse
            </div>
        </div>
    </div>
@endsection 
@extends('layouts.app')

@section('title', 'Pet Details - PawMatch')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/shared/public-pet-listings.css') }}">
@endpush

@section('content')
<div class="public-main-container">
    <div class="pet-details-page">
        <a href="{{ route('public.pet-listings') }}" class="btn btn-outline" style="margin-bottom: 1rem;">Back to Listings</a>
        <div class="pet-details-grid">
            <!-- Pet Images -->
            <div class="pet-images-section">
                @if ($pet->images->isNotEmpty())
                    <div class="pet-gallery">
                        @foreach ($pet->images as $idx => $image)
                            <img src="{{ $image->image_url }}" alt="{{ $pet->name }}" class="pet-detail-image" data-index="{{ $idx }}" style="display: {{ $idx === 0 ? 'block' : 'none' }}; margin-bottom: 1rem;">
                        @endforeach
                        <div class="pet-thumbnails" style="display: flex; gap: 0.5rem; margin-top: 0.5rem;">
                            @foreach ($pet->images as $idx => $image)
                                <img src="{{ $image->image_url }}" alt="{{ $pet->name }}" class="pet-detail-thumbnail" data-index="{{ $idx }}" style="width: 54px; height: 54px; object-fit: cover; border-radius: 6px; cursor: pointer; border: 2px solid #e5e7eb;">
                            @endforeach
                        </div>
                    </div>
                @else
                    <img src="{{ asset('images/default-pet.png') }}" alt="No image available for {{ $pet->name }}" class="pet-detail-image">
                @endif
            </div>
            <!-- Pet Information -->
            <div class="pet-info-section">
                <h1 class="pet-name">{{ $pet->name }}</h1>
                <p class="pet-description">{{ $pet->description }}</p>
                <div class="pet-details-grid">
                    <div class="detail-item"><strong>Age:</strong> {{ $pet->age }} years old</div>
                    <div class="detail-item"><strong>Gender:</strong> {{ $pet->gender }}</div>
                    <div class="detail-item"><strong>Size:</strong> {{ $pet->size }}</div>
                    <div class="detail-item"><strong>Breed:</strong> {{ $pet->breed }}</div>
                    <div class="detail-item"><strong>Status:</strong> <span class="pet-status-value">{{ $pet->adoption_status ?? $pet->status ?? 'available' }}</span></div>
                </div>
                <div class="pet-section">
                    <h3>Shelter Information</h3>
                    @if($pet->shelter)
                        <div class="shelter-info">
                            <p class="shelter-name">{{ $pet->shelter->shelter_name }}</p>
                            <p class="shelter-address">{{ $pet->shelter->location }}</p>
                            <p class="shelter-phone">{{ $pet->shelter->contact_info }}</p>
                        </div>
                    @else
                        <p>No shelter information available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 
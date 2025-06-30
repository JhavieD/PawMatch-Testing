@extends('layouts.pet-details')

@section('title', 'Pet Details - PawMatch')

@section('adopter-content')
<div class="pet-details-page">
    <div class="main-content">
        <div class="content-wrapper">
            <!-- Header -->
            <div class="page-header">
                <div class="header-content">
                    <h1>Pet Details</h1>
                    <a href="{{ route('adopter.pet-listings') }}" class="btn btn-outline">Back to Listings</a>
                </div>
            </div>

            <div class="pet-details-container">
                <div class="pet-details-grid">
                    <!-- Pet Images -->
                    <div class="pet-images-section">
                        <div class="main-image-container">
                            @if ($pet->images->isNotEmpty())
                                <div class="pet-gallery">
                                    @foreach ($pet->images as $image)
                                        <img src="{{ $image->image_url }}" alt="{{ $pet->name }}" class="pet-detail-image">
                                    @endforeach
                                </div>
                            @else
                                <p>No images found for this pet.</p>
                            @endif
                            <div class="pet-status-badge">
                                <span class="status-available">Available for Adoption</span>
                            </div>
                        </div>
                        <div class="thumbnail-grid">
                            @for ($i = 1; $i <= 4; $i++)
                                <img src="https://source.unsplash.com/random/200x200/?pet" alt="Pet thumbnail" class="thumbnail-image">
                            @endfor
                        </div>
                    </div>

                    <!-- Pet Information -->
                    <div class="pet-info-section">
                        <h1 class="pet-name">Max</h1>
                        <p class="pet-description">Friendly and Playful Golden Retriever</p>
                        
                        <div class="pet-details-grid">
                            <div class="detail-item">
                                <h3>Age</h3>
                                <p>2 years old</p>
                            </div>
                            <div class="detail-item">
                                <h3>Gender</h3>
                                <p>Male</p>
                            </div>
                            <div class="detail-item">
                                <h3>Size</h3>
                                <p>Large</p>
                            </div>
                            <div class="detail-item">
                                <h3>Breed</h3>
                                <p>Golden Retriever</p>
                            </div>
                        </div>

                        <div class="pet-section">
                            <h3>About</h3>
                            <p>
                                Max is a friendly and playful Golden Retriever who loves to play fetch and go for long walks. 
                                He's great with children and other dogs, and he's already house-trained. Max would make a 
                                wonderful addition to any active family.
                            </p>
                        </div>

                        <div class="pet-section">
                            <h3>Health Information</h3>
                            <ul class="health-list">
                                <li>Vaccinated</li>
                                <li>Spayed/Neutered</li>
                                <li>Microchipped</li>
                                <li>Regular check-ups</li>
                            </ul>
                        </div>

                        <div class="pet-section">
                            <h3>Shelter Information</h3>
                            <div class="shelter-info">
                                <p class="shelter-name">Happy Paws Shelter</p>
                                <p class="shelter-address">123 Pet Street, City, State</p>
                                <p class="shelter-phone">(555) 123-4567</p>
                            </div>
                        </div>

                        <div class="pet-actions">
                            <a href="{{ route('adopter.adoption-form') }}" class="btn btn-primary">Start Adoption Process</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adopter/pet-details.css') }}">
@endpush 
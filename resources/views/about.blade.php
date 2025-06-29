@extends('layouts.app')

@section('title', 'About PawMatch - Connecting Pets with Loving Homes')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/shared/app.css') }}">
<link rel="stylesheet" href="{{ asset('css/shared/marketing.css') }}">
@push('styles')
<link rel="stylesheet" href="{{ asset('css/shared/public-card.css') }}">
@endpush
@endsection

@section('content')
<div class="hamburger-spacer"></div>
<div class="public-card-container">
    <section class="public-card-header">
        <h1>Connecting Pets with Loving Homes</h1>
        <p>PawMatch is a platform dedicated to making pet adoption simple, efficient, and joyful for everyone involved.</p>
    </section>
    <section class="public-card-section">
        <div class="public-card-section-header">Our Mission</div>
        <div class="public-card-stats-grid">
            <div class="public-card-stat-item">
                <div class="mission-icon-modern">🏠</div>
                <h3>Finding Forever Homes</h3>
                <p>We connect loving families with pets in need, making the adoption process seamless and rewarding.</p>
            </div>
            <div class="public-card-stat-item">
                <div class="mission-icon-modern">💝</div>
                <h3>Supporting Shelters</h3>
                <p>We empower shelters and rescuers with tools to showcase their pets and manage adoptions effectively.</p>
            </div>
            <div class="public-card-stat-item">
                <div class="mission-icon-modern">🤝</div>
                <h3>Building Community</h3>
                <p>We create a supportive community of pet lovers, shelters, and rescuers working together.</p>
            </div>
        </div>
    </section>
    <section class="public-card-section">
        <div class="public-card-section-header">How PawMatch Works</div>
        <div class="public-card-list">
            <ul class="step-list-modern">
                <li class="step-item-modern">
                    <h3>Create Your Profile</h3>
                    <p>Sign up as an adopter, shelter, or rescuer. Complete your profile with relevant information about your preferences or organization.</p>
                </li>
                <li class="step-item-modern">
                    <h3>Browse Available Pets</h3>
                    <p>Search through our database of pets using filters for species, breed, age, and location to find your perfect match.</p>
                </li>
                <li class="step-item-modern">
                    <h3>Submit Applications</h3>
                    <p>When you find a pet you're interested in, submit an adoption application directly through our platform.</p>
                </li>
                <li class="step-item-modern">
                    <h3>Connect and Adopt</h3>
                    <p>Once approved, coordinate with the shelter or rescuer to meet your potential new family member and complete the adoption process.</p>
                </li>
            </ul>
        </div>
    </section>
</div>
@endsection 
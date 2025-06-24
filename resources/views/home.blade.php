@extends('layouts.app')

@section('title', 'PawMatch - Find Your Perfect Pet Companion')

@section('content')
    <!-- Hero Section -->
    <section id="hero">
        <div class="hero-content">
            <h1>Find Your Perfect Pet Companion</h1>
            <p>Connect with local shelters and rescuers to adopt your next furry friend or help animals in need.</p>
            <a href="{{ route('register') }}" id="get-started-btn">Get Started</a>
            <a href="{{ route('pet-listings') }}" id="browse-pets-btn">Browse Pets</a>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features">
        <h2>How PawMatch Works</h2>
        <div class="features-grid">
            <div class="feature-card">
                <h3>Find Pets</h3>
                <p>Browse through our database of adoptable pets from verified shelters and rescuers.</p>
            </div>
            <div class="feature-card">
                <h3>Easy Adoption</h3>
                <p>Simple application process to help you connect with your future pet.</p>
            </div>
            <div class="feature-card">
                <h3>Help Strays</h3>
                <p>Report stray animals and connect them with local rescuers.</p>
            </div>
        </div>
    </section>

    <!-- Call to Action Section -->
    <section id="cta">
        <div class="cta-content">
            <h2>Report a Stray</h2>
            <p>Help us connect stray animals with local rescuers. Report a stray animal in your area.</p>
            <a href="{{ route('adopter.report-stray') }}" id="report-now-btn">Report Now</a>
        </div>
    </section>
    @section('styles')
    <link rel="stylesheet" href="{{ asset('css/shared/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/marketing.css') }}">
    @endsection
@endsection 
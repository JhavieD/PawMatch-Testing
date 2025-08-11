@extends('layouts.app')

@section('title', 'PawMatch - Find Your Perfect Pet Companion')

@section('content')
    <!-- Hero Section -->
    <section id="hero">
        <div class="hero-content">
            <h1>Find Your Perfect Pet Companion</h1>
            <p>Connect with local shelters and rescuers to adopt your next furry friend or help animals in need.</p>
            <a href="{{ route('register') }}" id="get-started-btn">Get Started</a>
            <a href="{{ route('public.pet-listings') }}" id="browse-pets-btn">Browse Pets</a>
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

    <!-- Floating Donate Button -->
    <a href="{{ route('donate') }}" class="floating-donate-btn" aria-label="Donate">Donate</a>
    @section('styles')
    <link rel="stylesheet" href="{{ asset('css/shared/app.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shared/marketing.css') }}">
    <style>
      .floating-donate-btn {
        position: fixed;
        right: 24px;
        bottom: 24px;
        z-index: 1000;
        background: #FFC107;
        color: #1a1a1a;
        border-radius: 9999px;
        padding: 12px 18px;
        font-weight: 600;
        box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        text-decoration: none;
        transition: transform .1s ease, box-shadow .2s ease, background .2s ease;
      }
      .floating-donate-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 10px 24px rgba(0,0,0,0.2);
        background: #ffca2c;
      }
      #donate-cta .cta-content { text-align: center; }
      #donate-now-btn {
        display: inline-block;
        margin-top: 10px;
        background: #FFC107;
        color: #1a1a1a;
        padding: 10px 16px;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
      }
      #donate-now-btn:hover { background: #ffca2c; }
    </style>
    @endsection
@endsection 
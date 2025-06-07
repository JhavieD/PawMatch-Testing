@extends('layouts.app')

@section('title', 'About PawMatch - Connecting Pets with Loving Homes')

@section('content')

    <section id="about-hero">
        <div class="hero-content">
            <h1>Connecting Pets with Loving Homes</h1>
            <p>PawMatch is a platform dedicated to making pet adoption simple, efficient, and joyful for everyone involved.</p>
        </div>
    </section>

    <div class="container">
        <!-- Mission Section -->
        <section id="mission">
            <div class="section-header">
                <h2>Our Mission</h2>
                <p>We're on a mission to help every pet find their forever home</p>
            </div>
            <div class="mission-grid">
                <div class="mission-card">
                    <div class="mission-icon">🏠</div>
                    <h3>Finding Forever Homes</h3>
                    <p>We connect loving families with pets in need, making the adoption process seamless and rewarding.</p>
                </div>
                <div class="mission-card">
                    <div class="mission-icon">💝</div>
                    <h3>Supporting Shelters</h3>
                    <p>We empower shelters and rescuers with tools to showcase their pets and manage adoptions effectively.</p>
                </div>
                <div class="mission-card">
                    <div class="mission-icon">🤝</div>
                    <h3>Building Community</h3>
                    <p>We create a supportive community of pet lovers, shelters, and rescuers working together.</p>
                </div>
            </div>
        </section>

        <!-- How It Works Section -->
        <section id="how-it-works">
            <div class="section-header">
                <h2>How PawMatch Works</h2>
                <p>Simple steps to find your perfect companion</p>
            </div>
            <div class="how-it-works">
                <ul class="step-list">
                    <li class="step-item">
                        <h3>Create Your Profile</h3>
                        <p>Sign up as an adopter, shelter, or rescuer. Complete your profile with relevant information about your preferences or organization.</p>
                    </li>
                    <li class="step-item">
                        <h3>Browse Available Pets</h3>
                        <p>Search through our database of pets using filters for species, breed, age, and location to find your perfect match.</p>
                    </li>
                    <li class="step-item">
                        <h3>Submit Applications</h3>
                        <p>When you find a pet you're interested in, submit an adoption application directly through our platform.</p>
                    </li>
                    <li class="step-item">
                        <h3>Connect and Adopt</h3>
                        <p>Once approved, coordinate with the shelter or rescuer to meet your potential new family member and complete the adoption process.</p>
                    </li>
                </ul>
            </div>
        </section>

        <!-- Impact Stats -->
        <section id="impact">
            <div class="section-header">
                <h2>Our Impact</h2>
                <p>Making a difference in the lives of pets and people</p>
            </div>
            <div class="stats-grid">
                <div class="stat-item">
                    <h3>10,000+</h3>
                    <p>Successful Adoptions</p>
                </div>
                <div class="stat-item">
                    <h3>500+</h3>
                    <p>Partner Shelters</p>
                </div>
                <div class="stat-item">
                    <h3>50,000+</h3>
                    <p>Registered Users</p>
                </div>
                <div class="stat-item">
                    <h3>95%</h3>
                    <p>Success Rate</p>
                </div>
            </div>
        </section>

        <!-- Team Section -->
        <section id="team" class="team-section">
            <div class="section-header">
                <h2>Meet Our Team</h2>
                <p>Dedicated professionals working to make pet adoption better</p>
            </div>
            <div class="team-grid">
                <div class="team-member">
                    <img src="https://placehold.co/200x200" alt="Team Member" class="team-photo" />
                    <h3>Andrea Gabbrielle Raymundo</h3>
                    <p>Founder & CEO</p>
                </div>
                <div class="team-member">
                    <img src="https://placehold.co/200x200" alt="Team Member" class="team-photo" />
                    <h3>Jan Vincent Dominguez</h3>
                    <p>Head of Operations</p>
                </div>
                <div class="team-member">
                    <img src="https://placehold.co/200x200" alt="Team Member" class="team-photo" />
                    <h3>Vince Joseph Rubio</h3>
                    <p>Operations Member</p>
                </div>
                <div class="team-member">
                    <img src="https://placehold.co/200x200" alt="Team Member" class="team-photo" />
                    <h3>Allainne Louisse Villanueva</h3>
                    <p>Operations Member</p>
                </div>
            </div>
        </section>
    </div>
    <link rel="stylesheet" href="{{ asset('css/marketing.css') }}">
@endsection 
@extends('layouts.adoption-form')

@section('title', 'Adoption Application - PawMatch')

@section('adopter-content')
<div class="adoption-form-page">
    <div class="main-content">
        <div class="content-wrapper">
            <!-- Header -->
            <div class="page-header">
                <div class="header-content">
                    <h1>Adoption Application</h1>
                    <a href="{{ route('adopter.pet-details') }}" class="btn btn-outline">Back to Pet Details</a>
                </div>
            </div>

            <div class="application-container">
                <!-- Progress Steps -->
                <div class="progress-steps">
                    <div class="step active">
                        <div class="step-number">1</div>
                        <div class="step-label">Personal Info</div>
                    </div>
                    <div class="step">
                        <div class="step-number">2</div>
                        <div class="step-label">Living Situation</div>
                    </div>
                    <div class="step">
                        <div class="step-number">3</div>
                        <div class="step-label">Experience</div>
                    </div>
                    <div class="step">
                        <div class="step-number">4</div>
                        <div class="step-label">References</div>
                    </div>
                </div>

                <!-- Form -->
                <form class="application-form">
                    <!-- Personal Information -->
                    <div class="form-section">
                        <h3>Personal Information</h3>
                        
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="first_name">First name</label>
                                <input type="text" name="first_name" id="first_name" required>
                            </div>

                            <div class="form-group">
                                <label for="last_name">Last name</label>
                                <input type="text" name="last_name" id="last_name" required>
                            </div>

                            <div class="form-group full-width">
                                <label for="email">Email address</label>
                                <input type="email" name="email" id="email" required>
                            </div>

                            <div class="form-group full-width">
                                <label for="phone">Phone number</label>
                                <input type="tel" name="phone" id="phone" required>
                            </div>

                            <div class="form-group full-width">
                                <label for="address">Street address</label>
                                <input type="text" name="address" id="address" required>
                            </div>

                            <div class="form-group">
                                <label for="city">City</label>
                                <input type="text" name="city" id="city" required>
                            </div>

                            <div class="form-group">
                                <label for="state">State</label>
                                <input type="text" name="state" id="state" required>
                            </div>

                            <div class="form-group">
                                <label for="zip">ZIP code</label>
                                <input type="text" name="zip" id="zip" required>
                            </div>
                        </div>
                    </div>

                    <!-- Navigation Buttons -->
                    <div class="form-actions">
                        <button type="button" class="btn btn-outline">Previous</button>
                        <button type="submit" class="btn btn-primary">Next Step</button>
                    </div>
                </form>

                <!-- Form Tips -->
                <div class="form-tips">
                    <h4>Tips for a successful application</h4>
                    <ul>
                        <li>Please provide accurate and complete information</li>
                        <li>All fields marked with an asterisk (*) are required</li>
                        <li>You can save your progress and return later</li>
                        <li>Be prepared to provide references and proof of residence</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('.application-form');
        const nextButton = form.querySelector('button[type="submit"]');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            // Add form validation and submission logic here
            console.log('Form submitted');
        });
    });
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adopter/adoption-form.css') }}">
@endpush
@endsection 
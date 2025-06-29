@extends('layouts.app')

@section('title', 'Terms & Privacy Policy - PawMatch')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/shared/public-card.css') }}">
@endpush

@section('content')
<div class="hamburger-spacer"></div>
<div class="public-card-container">
    <div class="public-card-header">
        <h1>Terms & Privacy Policy</h1>
        <p class="terms-intro-modern">Please read these terms carefully before using PawMatch</p>
    </div>
    <section class="public-card-section">
        <div class="public-card-section-header">Terms of Service</div>
        <div class="public-card-list">
            <h3>1. User Accounts</h3>
            <ul>
                <li>Provide accurate and complete information</li>
                <li>Maintain the security of your account credentials</li>
                <li>Accept responsibility for all activities under your account</li>
                <li>Notify us immediately of any unauthorized access</li>
            </ul>
            <h3>2. Pet Adoption Process</h3>
            <ul>
                <li>All information about pets must be accurate and truthful</li>
                <li>Adoption fees must be clearly stated and reasonable</li>
                <li>Users must follow all local laws and regulations regarding pet adoption</li>
                <li>PawMatch is not responsible for the outcome of any adoption</li>
            </ul>
            <h3>3. User Conduct</h3>
            <ul>
                <li>Post false or misleading information</li>
                <li>Harass or abuse other users</li>
                <li>Use the platform for commercial breeding purposes</li>
                <li>Attempt to circumvent any platform security measures</li>
            </ul>
        </div>
    </section>
    <section class="public-card-section">
        <div class="public-card-section-header">Privacy Policy</div>
        <div class="public-card-list">
            <h3>1. Information Collection</h3>
            <ul>
                <li>Account information (name, email, phone number)</li>
                <li>Profile information and preferences</li>
                <li>Usage data and interaction with the platform</li>
                <li>Device and browser information</li>
            </ul>
            <h3>2. Information Usage</h3>
            <ul>
                <li>Facilitate pet adoptions and user communication</li>
                <li>Improve our services and user experience</li>
                <li>Send relevant notifications and updates</li>
                <li>Ensure platform security and prevent fraud</li>
            </ul>
            <h3>3. Information Sharing</h3>
            <ul>
                <li>Between adopters and shelters/rescuers with consent</li>
                <li>With service providers who assist our operations</li>
                <li>When required by law or to protect rights</li>
                <li>In the event of a business transfer or merger</li>
            </ul>
            <h3>4. Data Security</h3>
            <ul>
                <li>Encryption of sensitive data</li>
                <li>Regular security assessments</li>
                <li>Limited access to personal information</li>
                <li>Secure data storage and transmission</li>
            </ul>
            <h3>5. User Rights</h3>
            <ul>
                <li>Access your personal information</li>
                <li>Correct inaccurate data</li>
                <li>Request deletion of your data</li>
                <li>Opt-out of marketing communications</li>
            </ul>
        </div>
    </section>
    <section class="public-card-section">
        <div class="public-card-section-header">Contact Information</div>
        <div class="public-card-list">
            <p>For any questions about these terms or your privacy, please contact us at:</p>
            <p>Email: <a href="mailto:privacy@pawmatch.com">privacy@pawmatch.com</a></p>
            <p>Address: Pasig City</p>
        </div>
        <p class="last-updated-modern" style="color:#6b7280;font-size:0.98rem;margin-top:1.5rem;text-align:right;">Last Updated: March 15, 2025</p>
    </section>
</div>
@endsection 
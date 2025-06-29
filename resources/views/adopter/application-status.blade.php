@extends('layouts.adopter-sidebar')
@section('title', 'My Applications')
@section('adopter-content')
<div class="main-content">
    <div class="container">
        <div class="header">
            <h1>My Applications</h1>
            <p>Track the status of your adoption applications</p>
            <div class="tab-navigation">
                <button class="tab-btn active" onclick="switchTab('active')">Active Applications</button>
                <button class="tab-btn" onclick="switchTab('completed')">Completed Adoptions</button>
            </div>
        </div>
        <!-- Active Applications Tab -->
        <div id="activeTab" class="tab-content active">
            @forelse ($applications as $application)
            @if($application->status !== 'completed')
            <div class="application-card">
                <div class="application-header">
                    <div class="pet-info">
                        <img src="{{ $application->pet->image_url ?? '/images/default-pet.png' }}" alt="{{ $application->pet->name }}" class="pet-image" />
                        <div class="pet-details">
                            <h2>{{ $application->pet->name }}</h2>
                            <p>{{ $application->pet->breed }} • {{ $application->pet->shelter->shelter_name ?? '' }}</p>
                        </div>
                    </div>
                    <span class="status status-{{ $application->status }}">{{ ucfirst($application->status) }}</span>
                </div>
                <div class="application-content">
                    <div class="progress-track">
                        <div class="progress-step">
                            <div class="step-icon {{ $application->submitted_at ? 'completed' : '' }}">✓</div>
                            <div class="step-label">Submitted</div>
                        </div>
                        <div class="progress-step">
                            <div class="step-icon {{ $application->reviewed_at ? 'completed' : '' }}">2</div>
                            <div class="step-label">Reviewed</div>
                        </div>
                        <div class="progress-step">
                            <div class="step-icon {{ $application->status == 'approved' ? 'completed' : '' }}">3</div>
                            <div class="step-label">Approved</div>
                        </div>
                        <div class="progress-step">
                            <div class="step-icon">4</div>
                            <div class="step-label">Meet & Greet</div>
                        </div>
                    </div>
                    <ul class="timeline">
                        @if($application->status == 'approved')
                        <li class="timeline-item">
                            <div class="timeline-date">{{ $application->reviewed_at ? (is_string($application->reviewed_at) ? \Carbon\Carbon::parse($application->reviewed_at)->format('M d, Y h:i A') : $application->reviewed_at->format('M d, Y h:i A')) : '' }}</div>
                            <div class="timeline-content">
                                Application approved! Schedule a meet & greet with {{ $application->pet->name }}.
                            </div>
                        </li>
                        @elseif($application->status == 'rejected')
                        <li class="timeline-item">
                            <div class="timeline-date">{{ $application->reviewed_at ? (is_string($application->reviewed_at) ? \Carbon\Carbon::parse($application->reviewed_at)->format('M d, Y h:i A') : $application->reviewed_at->format('M d, Y h:i A')) : '' }}</div>
                            <div class="timeline-content">
                                Application rejected. Reason: {{ $application->rejection_reason }}
                            </div>
                        </li>
                        @elseif($application->reviewed_at)
                        <li class="timeline-item">
                            <div class="timeline-date">{{ $application->reviewed_at ? (is_string($application->reviewed_at) ? \Carbon\Carbon::parse($application->reviewed_at)->format('M d, Y h:i A') : $application->reviewed_at->format('M d, Y h:i A')) : '' }}</div>
                            <div class="timeline-content">
                                Application reviewed by {{ $application->pet->shelter->shelter_name ?? '' }}.
                            </div>
                        </li>
                        @endif
                        <li class="timeline-item">
                            <div class="timeline-date">{{ $application->submitted_at ? (is_string($application->submitted_at) ? \Carbon\Carbon::parse($application->submitted_at)->format('M d, Y h:i A') : $application->submitted_at->format('M d, Y h:i A')) : '' }}</div>
                            <div class="timeline-content">
                                Application submitted successfully.
                            </div>
                        </li>
                    </ul>
                    @if($application->status == 'approved')
                    <div style="margin-top: 1.5rem;">
                        <a href="#" class="btn btn-outline">Schedule Meet & Greet</a>
                    </div>
                    @endif
                </div>
            </div>
            @endif
            @empty
            <div class="no-applications-message">
                <p>You have not submitted any adoption applications yet.</p>
            </div>
            @endforelse
        </div>
        <!-- Completed Adoptions Tab -->
        <div id="completedTab" class="tab-content">
            @foreach ($applications as $application)
            @if($application->status == 'completed')
            <div class="application-card">
                <div class="application-header">
                    <div class="pet-info">
                        <img src="{{ $application->pet->image_url ?? '/images/default-pet.png' }}" alt="{{ $application->pet->name }}" class="pet-image" />
                        <div class="pet-details">
                            <h2>{{ $application->pet->name }}</h2>
                            <p>{{ $application->pet->breed }} • {{ $application->pet->shelter->shelter_name ?? '' }}</p>
                        </div>
                    </div>
                    <span class="status status-completed">Adoption Completed</span>
                </div>
                <div class="application-content">
                    <div class="rating-section">
                        <h3>Rate Your Adoption Experience</h3>
                        <div class="star-rating">
                            <span class="star" data-rating="5">★</span>
                            <span class="star" data-rating="4">★</span>
                            <span class="star" data-rating="3">★</span>
                            <span class="star" data-rating="2">★</span>
                            <span class="star" data-rating="1">★</span>
                        </div>
                        <textarea class="review-input" placeholder="Share your adoption experience..."></textarea>
                        <button class="btn btn-primary submit-review">Submit Review</button>
                    </div>
                </div>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>
<script>
// Tab switching functionality
function switchTab(tabName) {
    const tabs = document.querySelectorAll('.tab-content');
    const buttons = document.querySelectorAll('.tab-btn');
    tabs.forEach(tab => tab.classList.remove('active'));
    buttons.forEach(btn => btn.classList.remove('active'));
    document.getElementById(tabName + 'Tab').classList.add('active');
    event.target.classList.add('active');
}
// Star rating functionality
// (You can keep the JS from the HTML file for star rating)
document.addEventListener('DOMContentLoaded', function() {
    const starContainers = document.querySelectorAll('.star-rating');
    starContainers.forEach(starContainer => {
        const stars = starContainer.querySelectorAll('.star');
        let currentRating = 0;
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                currentRating = rating;
                updateStars(stars, rating);
            });
        });
        function updateStars(stars, rating) {
            stars.forEach(star => {
                const starRating = star.getAttribute('data-rating');
                if (starRating <= rating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        }
        // Submit review functionality
        const submitBtn = starContainer.closest('.rating-section').querySelector('.submit-review');
        submitBtn.addEventListener('click', function() {
            const review = starContainer.closest('.rating-section').querySelector('.review-input').value;
            if (currentRating === 0) {
                alert('Please select a rating');
                return;
            }
            if (!review.trim()) {
                alert('Please write a review');
                return;
            }
            // Here you would typically send this data to your backend
            alert('Thank you for your review!');
        });
    });
});
</script>
@endsection 
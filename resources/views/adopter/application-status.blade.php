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
                    @if ($application->status !== 'completed')
                        <div class="application-card {{ $application->status === 'rejected' ? 'application-rejected' : '' }}"
                            data-application-id="{{ $application->application_id }}"
                            data-pet-name="{{ $application->pet->name }}"
                            data-pet-image="{{ $application->pet->image_url ?? asset('images/default-pet.png') }}"
                            data-shelter-name="{{ $application->pet->shelter->shelter_name ?? '' }}"
                            data-status="{{ ucfirst($application->status) }}" data-status-raw="{{ $application->status }}"
                            data-rejection-reason="{{ $application->rejection_reason }}"
                            data-submitted-at="{{ $application->submitted_at }}"
                            data-reviewed-at="{{ $application->reviewed_at }}"
                            data-approved-at="{{ $application->approved_at }}"
                            data-updated-at="{{ $application->updated_at }}" onclick="openApplicationModal(this)">
                            <div class="application-header">
                                <div class="pet-info">
                                    <img src="{{ $application->pet->image_url ?? '/images/default-pet.png' }}"
                                        alt="{{ $application->pet->name }}" class="pet-image" />
                                    <div class="pet-details">
                                        <h2>{{ $application->pet->name }}</h2>
                                        <p>{{ $application->pet->breed }} •
                                            {{ $application->pet->shelter->shelter_name ?? '' }}</p>
                                    </div>
                                </div>
                                <span
                                    class="status status-{{ $application->status }}">{{ ucfirst($application->status) }}</span>
                            </div>
                            <div class="application-content">
                                <div class="progress-track">
                                    <!-- Progress line that fills based on status -->
                                    <div class="progress-line"
                                        style="left: 32px; width: {{ $application->status === 'rejected'
                                            ? 'calc(62% - 32px)'
                                            : ($application->status === 'approved'
                                                ? 'calc(61% - 32px)'
                                                : ($application->reviewed_at
                                                    ? 'calc(33% - 32px)'
                                                    : '0')) }};">
                                    </div>

                                    <div class="progress-step">
                                        <div
                                            class="step-icon {{ $application->status === 'rejected' ? 'rejected' : 'completed' }}">
                                            ✓</div>
                                        <div class="step-label">Submitted</div>
                                    </div>
                                    <div class="progress-step">
                                        <div
                                            class="step-icon {{ $application->status === 'rejected' ? 'rejected' : ($application->status === 'approved' || $application->reviewed_at ? 'completed' : '') }}">
                                            2</div>
                                        <div class="step-label">Reviewed</div>
                                    </div>
                                    <div class="progress-step">
                                        <div
                                            class="step-icon {{ $application->status === 'rejected' ? 'rejected' : ($application->status === 'approved' ? 'completed' : ($application->reviewed_at && $application->status !== 'rejected' ? 'current' : '')) }}">
                                            {{ $application->status === 'rejected' ? '✕' : '3' }}</div>
                                        <div class="step-label">
                                            {{ $application->status === 'rejected' ? 'Rejected' : 'Approved' }}</div>
                                    </div>
                                    <div class="progress-step">
                                        <div
                                            class="step-icon {{ $application->status === 'completed' ? 'completed' : ($application->status === 'approved' ? 'current' : '') }}">
                                            4</div>
                                        <div class="step-label">Meet & Greet</div>
                                    </div>
                                </div>
                                <ul class="timeline">
                                    @if ($application->status === 'approved')
                                        @if ($application->reviewed_at)
                                            <li class="timeline-item">
                                                <div class="timeline-date">
                                                    {{ \Carbon\Carbon::parse($application->reviewed_at)->format('M d, Y h:i A') }}
                                                </div>
                                                <div class="timeline-content">
                                                    Application reviewed by
                                                    {{ $application->pet->shelter->shelter_name ?? '' }}.
                                                </div>
                                            </li>
                                        @endif
                                        <li class="timeline-item">
                                            <div class="timeline-date">
                                                {{ $application->approved_at ? \Carbon\Carbon::parse($application->approved_at)->format('M d, Y h:i A') : '' }}
                                            </div>
                                            <div class="timeline-content">
                                                Application approved! Schedule a meet & greet with
                                                {{ $application->pet->name }}.
                                            </div>
                                        </li>
                                    @elseif($application->status === 'rejected')
                                        @if ($application->reviewed_at)
                                            <li class="timeline-item">
                                                <div class="timeline-date">
                                                    {{ \Carbon\Carbon::parse($application->reviewed_at)->format('M d, Y h:i A') }}
                                                </div>
                                                <div class="timeline-content">
                                                    Application reviewed by
                                                    {{ $application->pet->shelter->shelter_name ?? '' }}.
                                                </div>
                                            </li>
                                        @endif
                                        <li class="timeline-item">
                                            <div class="timeline-date">
                                                {{ $application->updated_at ? \Carbon\Carbon::parse($application->updated_at)->format('M d, Y h:i A') : '' }}
                                            </div>
                                            <div class="timeline-content">
                                                Application rejected.
                                                <div class="rejection-reason">Reason: {{ $application->rejection_reason }}
                                                </div>
                                            </div>
                                        </li>
                                    @endif
                                    <li class="timeline-item">
                                        <div class="timeline-date">
                                            {{ \Carbon\Carbon::parse($application->submitted_at)->format('M d, Y h:i A') }}
                                        </div>
                                        <div class="timeline-content">
                                            Application submitted successfully.
                                        </div>
                                    </li>
                                </ul>
                                @if ($application->status === 'approved')
                                    <div class="action-buttons" style="margin-top: 2rem;">
                                        <a href="#" class="btn btn-primary schedule-meet-btn"
                                            data-app-id="{{ $application->application_id }}">
                                            Schedule Meet & Greet
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="hidden-details" style="display:none;">
                                <div class="adopter-answers">
                                    <p><strong>Household members:</strong> {{ $application->household_members }}</p>
                                    <p><strong>Allergies:</strong> {{ $application->allergies }}</p>
                                    <p><strong>Other pets:</strong> {{ $application->other_pets_details }}</p>
                                    <p><strong>Can provide vet care:</strong>
                                        {{ $application->can_provide_vet_care ? 'Yes' : 'No' }}</p>
                                </div>
                                <div class="pet-details">
                                    <p><strong>Breed:</strong> {{ $application->pet->breed }}</p>
                                    <p><strong>Age:</strong> {{ $application->pet->age ?? 'N/A' }}</p>
                                    <p><strong>Gender:</strong> {{ $application->pet->gender ?? 'N/A' }}</p>
                                    <p><strong>Description:</strong> {{ $application->pet->description ?? 'N/A' }}</p>
                                </div>
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
                    @if ($application->status == 'completed')
                        <div class="application-card" data-application-id="{{ $application->application_id }}">
                            <div class="application-header">
                                <div class="pet-info">
                                    <img src="{{ $application->pet->image_url ?? '/images/default-pet.png' }}"
                                        alt="{{ $application->pet->name }}" class="pet-image" />
                                    <div class="pet-details">
                                        <h2>{{ $application->pet->name }}</h2>
                                        <p>{{ $application->pet->breed }} •
                                            {{ $application->pet->shelter->shelter_name ?? '' }}</p>
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

    @include('components.application-modal')
    @include('components.schedule-meet-modal')

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
            // Highlight application if application_id is in query
            const appId = new URLSearchParams(window.location.search).get('application_id');
            if (appId) {
                const appCard = document.querySelector(`.application-card[data-application-id='${appId}']`);
                if (appCard) {
                    appCard.scrollIntoView({
                        behavior: 'smooth',
                        block: 'center'
                    });
                    appCard.style.boxShadow = '0 0 0 4px #2563eb';
                    setTimeout(() => {
                        appCard.style.boxShadow = '';
                    }, 2000);
                }
            }
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
                    const review = starContainer.closest('.rating-section').querySelector(
                        '.review-input').value;
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

            const params = new URLSearchParams(window.location.search);
            const openId = params.get('open');
            if (openId) {
                openAppModal(openId);
            }

            // Attach a single click handler for all schedule meet buttons
            document.querySelectorAll('.schedule-meet-btn').forEach(function(btn) {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    var appId = this.getAttribute('data-app-id');
                    console.log('[SCHEDULE MEET BUTTON DEBUG] data-app-id:', appId);
                    if (typeof window.openScheduleMeetModalModal === 'function') {
                        window.openScheduleMeetModalModal(appId);
                    }
                });
            });
        });

        function openApplicationModal(card) {
            // Populate modal fields
            document.getElementById('modal-pet-image').src = card.getAttribute('data-pet-image');
            document.getElementById('modal-pet-name').textContent = card.getAttribute('data-pet-name');
            document.getElementById('modal-shelter-name').textContent = card.getAttribute('data-shelter-name');
            const status = card.getAttribute('data-status');
            const statusRaw = card.getAttribute('data-status-raw');
            const badge = document.getElementById('modal-status-badge');
            badge.textContent = status;
            badge.className = 'status-badge ' + statusRaw;

            // You can expand this to render the progress tracker, timeline, application details, and pet details
            document.getElementById('modal-progress-tracker').innerHTML = card.querySelector('.progress-track')
                ?.outerHTML || '';
            document.getElementById('modal-timeline').innerHTML = card.querySelector('.timeline')?.outerHTML || '';
            document.getElementById('modal-application-details').innerHTML = card.querySelector('.adopter-answers')
                ?.outerHTML || '';

            // Show/hide action button
            const actionBtn = document.getElementById('modal-action-btn');
            if (statusRaw === 'approved') {
                actionBtn.style.display = '';
            } else {
                actionBtn.style.display = 'none';
            }

            // Show modal
            document.getElementById('application-modal-overlay').style.display = 'flex';

            // Ensure red progress line for rejected in modal
            const modalProgressTrack = document.querySelector('#modal-progress-tracker .progress-track');
            if (modalProgressTrack) {
                if (statusRaw === 'rejected') {
                    modalProgressTrack.classList.add('application-rejected');
                } else {
                    modalProgressTrack.classList.remove('application-rejected');
                }
            }
        }

        function closeApplicationModal() {
            document.getElementById('application-modal-overlay').style.display = 'none';
        }

        window.addEventListener('click', function(e) {
            const overlay = document.getElementById('application-modal-overlay');
            if (e.target === overlay) closeApplicationModal();
        });
    </script>
@endsection

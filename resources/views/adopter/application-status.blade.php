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
            <div 
                class="application-card {{ $application->status === 'rejected' ? 'application-rejected' : '' }}"
                data-application-id="{{ $application->id }}"
                data-pet-name="{{ $application->pet->name }}"
                data-pet-image="{{ $application->pet->image_url ?? asset('images/default-pet.png') }}"
                data-shelter-name="{{ $application->pet->shelter->shelter_name ?? '' }}"
                data-status="{{ ucfirst($application->status) }}"
                data-status-raw="{{ $application->status }}"
                data-rejection-reason="{{ $application->rejection_reason }}"
                data-submitted-at="{{ $application->submitted_at }}"
                data-reviewed-at="{{ $application->reviewed_at }}"
                data-approved-at="{{ $application->approved_at }}"
                data-updated-at="{{ $application->updated_at }}"
                onclick="openApplicationModal(this)">
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
                        <!-- Progress line that fills based on status -->
                        <div class="progress-line" style="left: 32px; width: {{
                            $application->status === 'rejected' ? 'calc(62% - 32px)' :
                            ($application->status === 'approved' ? 'calc(61% - 32px)' :
                            ($application->reviewed_at ? 'calc(33% - 32px)' : '0'))
                        }};"></div>
                        
                        <div class="progress-step">
                            <div class="step-icon {{ $application->status === 'rejected' ? 'rejected' : 'completed' }}">✓</div>
                            <div class="step-label">Submitted</div>
                        </div>
                        <div class="progress-step">
                            <div class="step-icon {{ $application->status === 'rejected' ? 'rejected' : (($application->status === 'approved' || $application->reviewed_at) ? 'completed' : '') }}">2</div>
                            <div class="step-label">Reviewed</div>
                        </div>
                        <div class="progress-step">
                            <div class="step-icon {{ $application->status === 'rejected' ? 'rejected' : ($application->status === 'approved' ? 'completed' : ($application->reviewed_at && $application->status !== 'rejected' ? 'current' : '')) }}">{{ $application->status === 'rejected' ? '✕' : '3' }}</div>
                            <div class="step-label">{{ $application->status === 'rejected' ? 'Rejected' : 'Approved' }}</div>
                        </div>
                        <div class="progress-step">
                            <div class="step-icon {{ $application->status === 'completed' ? 'completed' : ($application->status === 'approved' ? 'current' : '') }}">4</div>
                            <div class="step-label">Meet & Greet</div>
                        </div>
                    </div>
                    <ul class="timeline">
                        @if($application->status === 'approved')
                            @if($application->reviewed_at)
                            <li class="timeline-item">
                                <div class="timeline-date">{{ \Carbon\Carbon::parse($application->reviewed_at)->format('M d, Y h:i A') }}</div>
                                <div class="timeline-content">
                                    Application reviewed by {{ $application->pet->shelter->shelter_name ?? '' }}.
                                </div>
                            </li>
                            @endif
                            <li class="timeline-item">
                                <div class="timeline-date">{{ $application->approved_at ? \Carbon\Carbon::parse($application->approved_at)->format('M d, Y h:i A') : '' }}</div>
                                <div class="timeline-content">
                                    Application approved! Schedule a meet & greet with {{ $application->pet->name }}.
                                </div>
                            </li>
                        @elseif($application->status === 'rejected')
                            @if($application->reviewed_at)
                            <li class="timeline-item">
                                <div class="timeline-date">{{ \Carbon\Carbon::parse($application->reviewed_at)->format('M d, Y h:i A') }}</div>
                                <div class="timeline-content">
                                    Application reviewed by {{ $application->pet->shelter->shelter_name ?? '' }}.
                                </div>
                            </li>
                            @endif
                            <li class="timeline-item">
                                <div class="timeline-date">{{ $application->updated_at ? \Carbon\Carbon::parse($application->updated_at)->format('M d, Y h:i A') : '' }}</div>
                                <div class="timeline-content">
                                    Application rejected.
                                    <div class="rejection-reason">Reason: {{ $application->rejection_reason }}</div>
                                </div>
                            </li>
                        @endif
                        <li class="timeline-item">
                            <div class="timeline-date">{{ \Carbon\Carbon::parse($application->submitted_at)->format('M d, Y h:i A') }}</div>
                            <div class="timeline-content">
                                Application submitted successfully.
                            </div>
                        </li>
                    </ul>
                    @if($application->status === 'approved')
                    <div class="action-buttons" style="margin-top: 2rem;">
                        <a href="#" class="btn btn-primary" onclick="event.stopPropagation(); openScheduleMeetModal({{ $application->id }}); return false;">Schedule Meet & Greet</a>
                    </div>
                    @endif
                </div>
                <div class="hidden-details" style="display:none;">
                    <div class="adopter-answers">
                        <p><strong>Household members:</strong> {{ $application->household_members }}</p>
                        <p><strong>Allergies:</strong> {{ $application->allergies }}</p>
                        <p><strong>Other pets:</strong> {{ $application->other_pets_details }}</p>
                        <p><strong>Can provide vet care:</strong> {{ $application->can_provide_vet_care ? 'Yes' : 'No' }}</p>
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
            @if($application->status == 'completed')
            <div class="application-card" data-application-id="{{ $application->application_id }}">
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
                    <div class="review-section">
                        <h3>Rate Your Adoption Experience</h3>
                        <form class="reviewForm" data-application-id="{{ $application->application_id }}">
                            @csrf
                            <input type="hidden" name="application_id" value="{{ $application->application_id }}">
                            <input type="hidden" name="rating" class="rating-input" value="0">
                            
                            <div class="rating-stars">
                                <span class="rating-star" data-rating="1">☆</span>
                                <span class="rating-star" data-rating="2">☆</span>
                                <span class="rating-star" data-rating="3">☆</span>
                                <span class="rating-star" data-rating="4">☆</span>
                                <span class="rating-star" data-rating="5">☆</span>
                            </div>
                            <textarea name="review" class="form-control mt-3" rows="4" placeholder="Share your adoption experience..."></textarea>
                            <button type="submit" class="btn btn-primary mt-3">Submit Review</button>
                        </form>
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
function switchTab(tabName, buttonElement) {
    const tabs = document.querySelectorAll('.tab-content');
    const buttons = document.querySelectorAll('.tab-btn');

    tabs.forEach(tab => tab.classList.remove('active'));
    buttons.forEach(btn => btn.classList.remove('active'));

    document.getElementById(tabName + 'Tab').classList.add('active');
    event.target.classList.add('active');
}

document.addEventListener('DOMContentLoaded', function() {
    // Highlight application if application_id is in query
    const appId = new URLSearchParams(window.location.search).get('application_id');
    if (appId) {
        const appCard = document.querySelector(`.application-card[data-application-id='${appId}']`);
        if (appCard) {
            appCard.scrollIntoView({ behavior: 'smooth', block: 'center' });
            appCard.style.boxShadow = '0 0 0 4px #2563eb';
            setTimeout(() => {
                appCard.style.boxShadow = '';
            }, 2000);
        }
    }
});

    document.querySelectorAll('.reviewForm').forEach(form => {
        const ratingStars = form.querySelectorAll('.rating-star');
        const ratingInput = form.querySelector('.rating-input');
        
        ratingStars.forEach(star => {
            star.addEventListener('click', function() {
                const rating = parseInt(this.dataset.rating);
                ratingInput.value = rating;
                
                // Update star appearance within this form
                ratingStars.forEach((s, index) => {
                    if (index < rating) {
                        s.classList.add('active');
                        s.innerHTML = '★';
                    } else {
                        s.classList.remove('active');
                        s.innerHTML = '☆';
                    }
                });
            });
            
        star.addEventListener('mouseenter', function() {
            const rating = parseInt(this.dataset.rating);
            ratingStars.forEach((s, index) => {
                if (index < rating) {
                    s.innerHTML = '★';
                    } else {
                    s.innerHTML = '☆';
                }
            });
        });

        star.addEventListener('mouseleave', function() {
            // Reset stars to current rating
            const currentRating = parseInt(ratingInput.value);
            ratingStars.forEach((s, index) => {
                if (index < currentRating) {
                    s.innerHTML = '★';
                } else {
                    s.innerHTML = '☆';
                }
            });
        });

        const ratingStarsContainer = form.querySelector('.rating-stars');
        ratingStarsContainer.addEventListener('mouseleave', function() {
            const currentRating = parseInt(ratingInput.value);
            ratingStars.forEach((s, index) => {
                if (index < currentRating) {
                    s.innerHTML = '★';
                } else {
                    s.innerHTML = '☆';
                }
            });
        });
        
        // Handle review submission for this form
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitButton = this.querySelector('button[type="submit"]');
            const applicationId = this.dataset.applicationId;
            
            if (formData.get('rating') === '0') {
                alert('Please select a rating');
                return;
            }
            
            submitButton.disabled = true;
            submitButton.textContent = 'Submitting...';
            
            fetch('{{ route("adopter.review.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hide the review form and show success message
                    this.closest('.review-section').innerHTML = `
                        <div class="success-message">
                            <p>✓ Review submitted successfully!</p>
                        </div>
                    `;
                } else {
                    alert(data.message || 'Error submitting review');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error submitting review');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = 'Submit Review';
            });
        });
    });
    
    // Check for existing reviews when page loads
    const completedApplications = document.querySelectorAll('#completedTab [data-application-id]');
    completedApplications.forEach(app => {
        const applicationId = app.dataset.applicationId;
        if (app.querySelector('.review-section')) {
            checkExistingReview(applicationId);
        }
    });

    const params = new URLSearchParams(window.location.search);
    const openId = params.get('open');
    if (openId) {
        openAppModal(openId);
    }
});

function checkExistingReview(applicationId) {
    fetch(`{{ route("adopter.review.check") }}?application_id=${applicationId}`)
        .then(response => response.json())
        .then(data => {
            if (data.hasReview) {
                // Show existing review instead of form
                const reviewSection = document.querySelector(`[data-application-id="${applicationId}"] .review-section`);
                if (reviewSection) {
                    reviewSection.innerHTML = `
                        <div class="existing-review">
                            <h4>Your Review</h4>
                            <div class="rating-display">
                                ${'★'.repeat(data.review.rating)}${'☆'.repeat(5 - data.review.rating)}
                            </div>
                            <p>${data.review.review}</p>
                            <small>Submitted on ${data.review.created_at}</small>
                        </div>
                    `;
                }
            }
        });
}

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
    document.getElementById('modal-progress-tracker').innerHTML = card.querySelector('.progress-track')?.outerHTML || '';
    document.getElementById('modal-timeline').innerHTML = card.querySelector('.timeline')?.outerHTML || '';
    document.getElementById('modal-application-details').innerHTML = card.querySelector('.adopter-answers')?.outerHTML || '';

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

function openScheduleMeetModal(applicationId) {
    document.getElementById('schedule-meet-modal-overlay').style.display = 'flex';
}
function closeScheduleMeetModal() {
    document.getElementById('schedule-meet-modal-overlay').style.display = 'none';
}
// Attach to modal button
const scheduleBtns = document.querySelectorAll('.btn.btn-primary, #modal-action-btn');
scheduleBtns.forEach(btn => {
    if (btn.textContent.includes('Schedule Meet & Greet')) {
        btn.onclick = function(e) {
            e.preventDefault();
            e.stopPropagation(); // Prevents application modal from opening
            openScheduleMeetModal(this.getAttribute('data-application-id'));
        };
    }
});
function submitScheduleMeet(e) {
    e.preventDefault();
    // You can add AJAX or form submission logic here
    alert('Meet & Greet request submitted!');
    closeScheduleMeetModal();
}
</script>
<style>
.rating-star {
    font-size: 24px;
    color: #ddd;
    cursor: pointer;
    transition: color 0.2s;
}

.rating-star:hover,
.rating-star.active {
    color: #ffd700;
}

.success-message {
    background: #d4edda;
    border: 1px solid #c3e6cb;
    color: #155724;
    padding: 10px;
    border-radius: 4px;
    margin-top: 10px;
}

.existing-review {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 4px;
    margin-top: 10px;
}

.rating-display {
    font-size: 18px;
    color: #ffd700;
    margin: 5px 0;
}
</style>
@endsection 
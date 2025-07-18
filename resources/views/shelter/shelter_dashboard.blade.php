@extends('layouts.shelter')

@section('title', 'Shelter Dashboard - PawMatch')

@section('shelter-content')
    <!-- Main Content -->
    <main class="shelter-main-content">
        <div class="shelter-content-wrapper">
            <!-- Profile and Add Pet Button at Top Right -->
            <div class="dashboard-profile-actions">
                <a href="{{ route('shelter.profile') }}">
                    <img src="{{ auth()->user()->profile_image ?? asset('images/default-profile.png') }}"
                        alt="Profile Picture" class="profile-img" />
                </a>
            </div>
            <!-- Top Row: Welcome (left) and Available Pets (right) -->
            <div class="dashboard-top-row">
                <div class="dashboard-welcome-card">
                    <div class="flex items-center space-x-2">
                        <h1 class="text-2xl font-bold flex items-center">
                            Hi, {{ $shelter->shelter_name ?? 'Shelter' }}!
                            {{-- 3-state verification system: Gray (not submitted) → Yellow (pending) → Blue (verified) --}}
                            @if ($verification && $verification->status === 'approved')
                                {{-- BLUE: Verified/Approved --}}
                                <span class="verification-badge verified flex items-center ml-2" title="Verified shelter"
                                    style="background: #3b82f6; border-radius: 9999px; padding: 0.25rem;">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"
                                        style="display: block;">
                                        <path fill-rule="evenodd"
                                            d="M16.707 6.293a1 1 0 00-1.414 0L9 12.586 6.707 10.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l7-7a1 1 0 000-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @elseif ($verification && $verification->status === 'pending')
                                {{-- YELLOW: Submitted but pending verification --}}
                                <span class="verification-badge pending flex items-center ml-2" title="Verification pending"
                                    style="background: #fbbf24; border-radius: 9999px; padding: 0.25rem;">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"
                                        style="display: block;">
                                        <path fill-rule="evenodd"
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </span>
                            @elseif ($verification && $verification->status === 'rejected')
                                {{-- RED: Rejected --}}
                                <a href="{{ route('shelter.profile') }}"
                                    class="verification-badge rejected flex items-center ml-2"
                                    title="Verification rejected"
                                    style="background: #ef4444; border-radius: 9999px; padding: 0.25rem; text-decoration: none;">
                                    <svg class="w-4 h-4" fill="white" viewBox="0 0 20 20" style="display: block;">
                                        <circle cx="10" cy="10" r="10" fill="#ef4444"/>
                                        <line x1="6" y1="6" x2="14" y2="14" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                                        <line x1="14" y1="6" x2="6" y2="14" stroke="white" stroke-width="2.5" stroke-linecap="round"/>
                                    </svg>
                                </a>
                            @else
                                {{-- GRAY: Not submitted yet --}}
                                <a href="{{ route('shelter.profile') }}"
                                    class="verification-badge not-submitted flex items-center ml-2"
                                    title="Click to submit verification documents"
                                    style="background: #9ca3af; border-radius: 9999px; padding: 0.25rem; text-decoration: none;">
                                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"
                                        style="display: block;">
                                        <path fill-rule="evenodd"
                                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </a>
                            @endif
                        </h1>
                    </div>
                    <p>Welcome back! Here's what's happening at your shelter</p>
                </div>
                <div class="available-pets-card">
                    <div class="stat-number">{{ $availablePets }}</div>
                    <div class="stat-label">Available Pets</div>
                    <div class="stat-icon" style="color: #f472b6;"><i class="fa-solid fa-paw"></i></div>
                </div>
            </div>
            <!-- Stats Grid (other stats) -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="color: #3b82f6;"><i class="fa-solid fa-pen-to-square"></i></div>
                    </div>
                    <div class="stat-number">{{ $pendingApplications }}</div>
                    <div class="stat-label">Pending Applications</div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="color: #10b981;"><i class="fa-solid fa-thumbs-up"></i></div>
                    </div>
                    <div class="stat-number">{{ $successfulAdoptions }}</div>
                    <div class="stat-label">Successful Adoptions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="color: #6366f1;"><i class="fa-solid fa-message"></i></div>
                    </div>
                    <div class="stat-number">{{ $newMessages }}</div>
                    <div class="stat-label">New Messages</div>
                </div>
                <div class="stat-card rating-card">
                    <div class="stat-header">
                        <div class="stat-icon" style="color: #fbbf24;"><i class="fa-solid fa-star"></i></div>
                    </div>
                    <div class="rating-display">
                        <div class="rating-number">
                            @if ($averageRating == 0)
                                0
                            @else
                                {{ number_format($averageRating, 1) }}
                            @endif
                        </div>
                        <div class="star-display">
                            @for ($i = 0; $i < 5; $i++)
                                <span style="color: #fbbf24;">
                                    {{ $i < round($averageRating) ? '★' : '☆' }}
                                </span>
                            @endfor
                        </div>
                        <div class="total-reviews">{{ $totalReviews }}</div>
                    </div>
                </div>
            </div>

            <div class="content-grid">
                <!-- Pet Management -->
                <div class="content-card">
                    <div class="card-header">
                        <h2>Recent Pets</h2>
                        <span class="spacer"></span>
                        <a href="{{ route('shelter.pets') }}" class="btn btn-outline">View All</a>
                    </div>
                    <ul class="pet-list">
                        @forelse ($recentPets as $pet)
                            <li class="pet-item">
                                <img src="{{ $pet->image_url ?? 'https://placehold.co/60x60' }}" alt="{{ $pet->name }}"
                                    class="pet-image-small">
                                <div class="pet-info">
                                    <div class="pet-name">{{ $pet->name }}</div>
                                    <div class="pet-details">{{ $pet->breed }} • {{ $pet->age }} years</div>
                                    <span class="status status-{{ $pet->adoption_status }}">
                                        {{ ucfirst($pet->adoption_status) }}
                                    </span>
                                </div>
                            </li>
                        @empty
                            <li style="color: rgb(123, 123, 123);">No Recent Pets.</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Recent Applications -->
                <div class="content-card">
                    <div class="card-header">
                        <h2>Recent Applications</h2>
                        <span class="spacer"></span>
                        <a href="{{ route('shelter.pet_applications') }}" class="btn btn-outline">View All</a>
                    </div>
                    <ul class="application-list">
                        @forelse($recentApplications as $app)
                            <li class="application-item">
                                <div class="applicant-info">
                                    <strong>{{ $app->adopter->user->first_name ?? 'Applicant' }}</strong> applied to
                                    adopt <strong>{{ $app->pet->name ?? 'Pet' }}</strong>
                                </div>
                                <div class="pet-details">{{ $app->created_at->diffForHumans() }}</div>
                                <div class="btn-group" style="margin-top: 0.5rem;">
                                    <button class="btn btn-primary review-application-btn"
                                        data-app-id="{{ $app->application_id }}">Review Application</button>
                                    <button
                                        onclick="window.location.href= '{{ route('shelter.messages', ['receiver_id' => $app->adopter->user->user_id]) }}'"
                                        class="reply-btn">Message</button>
                                </div>
                            </li>
                        @empty
                            <li style="color: rgb(123, 123, 123);">No recent Applications</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Recent Messages -->
                <div class="content-card">
                    <div class="card-header">
                        <h2>Recent Messages</h2>
                        <span class="spacer"></span>
                        <a href="{{ route('shelter.messages') }}" class="btn btn-outline card-header-btn"
                            aria-label="View all messages">View All</a>
                    </div>
                    <ul class="message-list">
                        @forelse($recentMessages->take(2) as $msg)
                            <a href="{{ route('shelter.messages', ['receiver_id' => $msg->sender->user_id ?? $msg->sender_id]) }}"
                                style="text-decoration: none; color: inherit;">
                                <li class="message-item">
                                    <div class="message-header"
                                        style="display: flex; align-items: center; justify-content: space-between;">
                                        <span class="message-sender"
                                            style="font-weight: 600;">{{ $msg->sender->name ?? 'User' }}</span>
                                        <span class="message-time" style="font-size: 0.95em; color: #888;">
                                            {{ $msg->sent_at ? \Carbon\Carbon::parse($msg->sent_at)->format('g:i A') : '' }}
                                        </span>
                                    </div>
                                    <div class="message-preview" style="color: #444; margin-top: 0.2rem;">
                                        {{ $msg->content ?? ($msg->message_content ?? '') }}
                                    </div>
                                </li>
                            </a>
                            @if (!$loop->last)
                                <hr style="margin: 0.2rem 0; border: none; border-top: 1px solid #e5e7eb;" />
                            @endif
                        @empty
                            <li class="message-item">
                                <div class="message-header">
                                    <span class="message-sender">No recent messages.</span>
                                </div>
                            </li>
                        @endforelse
                    </ul>
                </div>
            </div><!-- .content-grid -->

            <div class="content-card" style="margin-top: 2rem;">
                <div class="card-header">
                    <h2>Recent Reviews</h2>
                </div>
                <div class="reviews-list">
                    @forelse($recentReviews as $review)
                        <div class="review-card">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    @if ($review->adopter && $review->adopter->user && $review->adopter->user->profile_image)
                                        <img src="{{ $review->adopter->user->profile_image }}"
                                            alt="{{ $review->adopter->user->first_name }}" class="reviewer-image">
                                    @else
                                        <div class="reviewer-image"
                                            style="background: #e5e7eb; display: flex; align-items: center; justify-content: center; color: #6b7280; font-weight: 600; width: 40px; height: 40px; border-radius: 50%;">
                                            {{ $review->adopter && $review->adopter->user ? strtoupper(substr($review->adopter->user->first_name, 0, 1)) : 'U' }}
                                        </div>
                                    @endif
                                    <div>
                                        <div class="reviewer-name">{{ $review->adopter->user->first_name ?? 'User' }}
                                        </div>
                                        <div class="review-date">{{ $review->created_at->format('F d, Y') }}</div>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    @for ($i = 1; $i <= 5; $i++)
                                        @if ($i <= $review->rating)
                                            <span class="star-gold">★</span>
                                        @else
                                            <span class="star-empty">★</span>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        </div>

                        <div class="review-content">
                            "{{ $review->review }}"
                        </div>
                </div>
            @empty
                <div style="color: rgb(123, 123, 123);">No Recent Reviews</div>
            </div>
            @endforelse
        </div>
    </main>

    <!-- Add Pet Modal -->
    <div id="addPetModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Pet</h2>
                <button class="close-btn" type="button">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addPetForm" method="POST" action="{{ route('shelter.pets.create') }}"
                    enctype="multipart/form-data">
                    @csrf
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name">Pet Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="species">Species</label>
                            <select id="species" name="species" required>
                                <option value="">Select Species</option>
                                <option value="dog">Dog</option>
                                <option value="cat">Cat</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="breed">Breed</label>
                            <input type="text" id="breed" name="breed" required>
                        </div>
                        <div class="form-group">
                            <label for="age">Age</label>
                            <input type="number" id="age" name="age" required>
                        </div>
                        <div class="form-group">
                            <label for="gender">Gender</label>
                            <select id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="male">Male</option>
                                <option value="female">Female</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="size">Size</label>
                            <select id="size" name="size" required>
                                <option value="">Select Size</option>
                                <option value="small">Small</option>
                                <option value="medium">Medium</option>
                                <option value="large">Large</option>
                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" rows="4" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="behavior">Behavior</label>
                        <select name="behavior" id="behavior" required>
                            <option value="">Select Behavior</option>
                            <option value="Calm and Relaxed">Calm and Relaxed</option>
                            <option value="Playful and Energetic">Playful and Energetic</option>
                            <option value="Independent">Independent</option>
                            <option value="Protective">Protective</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="daily_activity">Daily Activity</label>
                        <select name="daily_activity" id="daily_activity" required>
                            <option value="">Select Activity Level</option>
                            <option value="Low">Low</option>
                            <option value="Moderate">Moderate</option>
                            <option value="High">High</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="special_needs">Special Needs</label>
                        <select name="special_needs" id="special_needs" required>
                            <option value="">Select Option</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="compatibility">Compatibility</label>
                        <select name="compatibility" id="compatibility">
                            <option value="">Select Option</option>
                            <option value="Yes">Yes</option>
                            <option value="No">No</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="edit-eating_habits">Eating Habits</label>
                        <select name="eating_habits" id="edit-eating_habits" required>
                            <option value="">Select Eating Habits</option>
                            <option value="Balanced Diet">Balanced Diet</option>
                            <option value="Portion Control">Portion Control</option>
                            <option value="Consistent Feeding Schedule">Consistent Feeding Schedule</option>
                        </select>
                    </div>

                    <div class="image-upload">
                        <h3>Pet Images</h3>
                        <div class="image-grid">
                            <label class="upload-box">
                                <input type="file" name="images[]" accept="image/*" multiple>
                                <span>+ Add Photos</span>
                            </label>
                        </div>
                    </div>
                    <div class="modal-actions">
                        <button type="submit" class="btn btn-primary">Add Pet</button>
                        <button type="button" class="btn btn-outline" onclick="closeModal(addPetModal)">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>



    <!-- Add this modal HTML at the end of the body -->
    <div id="shelterModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Shelter Information</h2>
                <span class="close">&times;</span>
            </div>
            <div class="shelter-info">
                <div class="shelter-header">
                    <img src="shelter-logo.jpg" alt="Shelter Logo" class="shelter-logo">
                    <div class="shelter-details">
                        <h3 class="shelter-name">Shelter Name</h3>
                        <div class="shelter-rating">
                            <span class="rating-stars">★★★★★</span>
                            <span class="rating-number">4.8</span>
                            <span class="total-reviews">(45 reviews)</span>
                        </div>
                        <p class="shelter-location">Los Angeles, CA</p>
                    </div>
                </div>
                <div class="shelter-stats">
                    <div class="stat-item">
                        <span class="stat-number">120+</span>
                        <span class="stat-label">Pets Adopted</span>
                    <div class="reviews-list">
                        <!-- Review items will be populated dynamically -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Application Review Modal -->
    <div id="applicationModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Application Details</h2>
                <button class="close-btn" type="button">&times;</button>
            </div>
            <div class="modal-body" id="applicationModalBody">

                <!-- Application details will be loaded here via AJAX -->

            </div>
        </div>
    </div>

    <!-- Rejection Reason Modal -->
    <div id="rejectionModal" class="modal" style="display: none;">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Reject Application</h2>
                <button class="close-rejection-btn">&times;</button>
            </div>
            <div class="modal-body">
                <label for="rejectionReason">Please provide a reason for rejection:</label>
                <textarea id="rejectionReason" rows="4" placeholder="Enter reason here..." style="width: 100%;"></textarea>
                <div style="margin-top: 1rem; text-align: right;">
                    <button class="btn btn-outline" id="cancelRejectionBtn">Cancel</button>
                    <button class="btn btn-primary" id="confirmRejectionBtn">Confirm Reject</button>
                </div>
            </div>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Modal functions that must be global ---
            function showApplicationModal(id) {
                fetch(`/shelter/applications/${id}/review`)
                    .then(response => response.text())
                    .then(html => {
                        document.getElementById('applicationModalBody').innerHTML = html;
                        const modal = document.getElementById('applicationModal');
                        modal.style.display = 'block';
                        document.body.style.overflow = 'hidden';
                        attachActionHandlers(id);
                    });
            }
            window.showApplicationModal = showApplicationModal;

            // Select modals
            const addPetModal = document.getElementById('addPetModal');
            const closeBtns = document.querySelectorAll('.close-btn');
            const modal = document.getElementById('applicationModal');
            const rejectionModal = document.getElementById('rejectionModal');
            const closeBtn = document.querySelector('.close-btn');
            const closeRejectionBtn = document.querySelector('.close-rejection-btn');
            const cancelRejectionBtn = document.getElementById('cancelRejectionBtn');
            const confirmRejectionBtn = document.getElementById('confirmRejectionBtn');

            // Function to open a modal
            function openModal(modal) {
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            }

            // Function to close a modal
            function closeModal(modal) {
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
            }
            window.closeModal = closeModal;

            // Open "Add New Pet" modal
            document.querySelectorAll('.add-pet-btn').forEach(btn => {
                btn.addEventListener('click', () => openModal(addPetModal));
            });
            // Close modals when clicking close buttons
            closeBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const modal = btn.closest('.modal');
                    closeModal(modal);
                });
            });

            // Close modal when clicking outside of it
            window.addEventListener('click', (e) => {
                if (e.target.classList.contains('modal')) {
                    closeModal(e.target);
                }
            });

            // Add Pet AJAX submission
            if (document.getElementById('addPetForm')) {
                document.getElementById('addPetForm').addEventListener('submit', (e) => {
                    e.preventDefault();
                    const form = e.target;
                    const formData = new FormData(form);
                    fetch(form.action, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'Accept': 'application/json'
                            }
                        })
                        .then(async response => {
                            if (response.ok) {
                                const data = await response.json();
                                if (data.success) {
                                    window.location.href = "{{ route('shelter.pets') }}";
                                } else {
                                    alert('Error adding pet. Please try again.');
                                }
                            } else if (response.status === 422) {
                                const errorData = await response.json();
                                let messages = [];
                                for (const key in errorData.errors) {
                                    messages.push(errorData.errors[key].join(' '));
                                }
                                alert('Validation error:\n' + messages.join('\n'));
                            } else {
                                alert('An error occurred. Please try again.');
                            }
                        })
                        .catch(error => {
                            alert('An error occurred. Please try again.');
                        });
                    closeModal(addPetModal);
                });
            }

            // Add event listener for review application buttons
            document.querySelectorAll('.review-application-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const appId = this.getAttribute('data-app-id');
                    showApplicationModal(appId);
                });
            });

            // Application modal close
            document.querySelector('#applicationModal .close-btn')?.addEventListener('click', () => {
                closeModal(modal);
            });

            // Rejection modal close
            closeRejectionBtn?.addEventListener('click', () => closeModal(rejectionModal));
            cancelRejectionBtn?.addEventListener('click', () => closeModal(rejectionModal));

            window.addEventListener('click', (e) => {
                if (e.target === modal) closeModal(modal);
                if (e.target === rejectionModal) closeModal(rejectionModal);
            });

            // action handlers for application
            let currentApplicationId = null;

            function attachActionHandlers(id) {
                const approveBtn = document.getElementById('approveBtn');
                const rejectBtn = document.getElementById('rejectBtn');
                const requestInfoBtn = document.getElementById('requestInfoBtn');

                if (approveBtn) {
                    approveBtn.onclick = () => {
                        fetch(`/shelter/applications/${id}/approve`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json'
                                },
                                body: JSON.stringify({})
                            })
                            .then(res => res.json())
                            .then(() => {
                                updateStatusBadge(id, 'approved');
                                closeModal(modal);
                            })
                            .catch(err => alert('Approval failed.'));
                    };
                }

                if (rejectBtn) {
                    rejectBtn.onclick = () => {
                        currentApplicationId = id;
                        rejectionModal.style.display = 'block';
                        document.body.style.overflow = 'hidden';
                    };
                }

                if (requestInfoBtn) {
                    requestInfoBtn.onclick = () => {
                        fetch(`/shelter/applications/${id}/request-info`, {
                                method: 'POST',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(res => res.json())
                            .then(() => {
                                updateStatusBadge(id, 'info-requested');
                                closeModal(modal);
                            });
                    };
                }
            }

            confirmRejectionBtn?.addEventListener('click', () => {
                const reason = document.getElementById('rejectionReason').value.trim();
                if (!reason) return alert("Please enter a reason for rejection.");

                fetch(`/shelter/applications/${currentApplicationId}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        rejection_reason: reason
                    })
                }).then(res => res.json()).then(() => {
                    updateStatusBadge(currentApplicationId, 'rejected');
                    closeModal(rejectionModal);
                    closeModal(modal);
                });
            });

            function updateStatusBadge(id, newStatus) {
                const badge = document.querySelector(`.status-badge[data-id="${id}"]`);
                if (badge) {
                    badge.innerText = newStatus.replace('-', ' ').replace(/\b\w/g, c => c.toUpperCase());
                    badge.className = `status-badge status-${newStatus}`;
                }
            }

            // Shelter modal logic (optional, for completeness)
            function openShelterModal(shelterId) {
                const modal = document.getElementById('shelterModal');
                modal.style.display = 'block';
                fetchShelterData(shelterId);
            }

            function closeShelterModal() {
                const modal = document.getElementById('shelterModal');
                modal.style.display = 'none';
            }
            async function fetchShelterData(shelterId) {
                // Example data structure:
                const shelterData = {
                    name: "Strays Worth Saving",
                    rating: 4.8,
                    totalReviews: 45,
                    location: "Los Angeles, CA",
                    adoptions: 120,
                    experience: "5 years",
                    reviews: [{
                        name: "John Doe",
                        rating: 5,
                        date: "March 15, 2024",
                        content: "Amazing experience adopting from this shelter."
                    }]
                };
                updateModalContent(shelterData);
            }
            document.querySelector('.close')?.addEventListener('click', closeShelterModal);
            window.addEventListener('click', (event) => {
                const modal = document.getElementById('shelterModal');
                if (event.target === modal) {
                    closeShelterModal();
                }
            });
        }); // End of DOMContentLoaded
    </script>
@endsection

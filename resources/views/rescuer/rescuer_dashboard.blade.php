@extends('layouts.rescuer')

@section('title', 'Rescuer Dashboard - PawMatch')

@section('rescuer-content')
    <!-- Main Content -->
    <main class="rescuer-main-content">
        <div class="rescuer-content-wrapper">
            <!-- Profile and Add Pet Button at Top Right -->
            <div class="dashboard-profile-actions">
                <a href="{{ route('rescuer.profile') }}">
                    <img src="{{ auth()->user()->profile_image ?? asset('images/default-profile.png') }}"
                        alt="Profile Picture" class="profile-img" />
                </a>
            </div>

            <div class="dashboard-top-row">
                <div class="dashboard-welcome-card">
                    <h1 class="text-2xl font-bold mb-0" style="display:inline;">
                        Hi, {{ $rescuer->organization_name ?? 'Rescuer' }}!
                        @if ($verification && $verification->status === 'approved')
                            <a href="{{ route('rescuer.profile') }}" class="verification-badge approved"
                                title="Verified rescuer (click to view verification)"
                                style="display:inline-flex;align-items:center;justify-content:center;width:1.2em;height:1.2em;border-radius:50%;background:#d1d5db;margin-left:0.2em;vertical-align:middle;">
                                <svg class="w-3 h-3 align-middle" style="margin-left:0;position:relative;top:1px;"
                                    fill="none" viewBox="0 0 20 20" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 10.5l3 3 5-5"
                                        stroke="#22c55e" />
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('rescuer.profile') }}" class="verification-badge unverified"
                                title="Click to verify your rescuer organization"
                                style="display:inline-flex;align-items:center;justify-content:center;width:1.2em;height:1.2em;border-radius:50%;background:#f1f1f1;margin-left:0.2em;vertical-align:middle;">
                                <svg class="w-3 h-3 text-gray-400 align-middle"
                                    style="margin-left:0;position:relative;top:1px;" fill="currentColor"
                                    viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M16.707 6.293a1 1 0 00-1.414 0L9 12.586 6.707 10.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l7-7a1 1 0 000-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </a>
                        @endif
                    </h1>
                    <p>Welcome back! Here's what's happening at your rescue dashboard.</p>
                </div>
                <div class="available-pets-card">
                    <div class="stat-number">{{ $availablePets }}</div>
                    <div class="stat-label">Available Pets</div>
                    <div class="stat-icon" style="color: #f472b6;"><i class="fa-solid fa-paw"></i></div>
                </div>
            </div>

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
                        <a href="{{ route('rescuer.pet-management') }}" class="btn btn-outline">View All</a>
                    </div>
                    <ul class="pet-list">
                        @forelse ($recentPets->take(2) as $pet)
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
                        <a href="{{ route('rescuer.pet_applications') }}" class="btn btn-outline">View All</a>
                    </div>
                    <ul class="application-list">
                        @forelse($recentApplications->take(2) as $app)
                            <li class="application-item">
                                <div class="applicant-info">
                                    <div class="applicant-info">
                                        <strong>{{ $app->adopter->user->first_name ?? 'Applicant' }}</strong> applied to
                                        adopt <strong>{{ $app->pet->name ?? 'Pet' }}</strong>
                                    </div>
                                </div>
                                <div class="pet-details">{{ $app->created_at->diffForHumans() }}</div>
                                <div class="btn-group" style="margin-top: 0.5rem;">
                                    <button class="btn btn-primary review-application-btn"
                                        data-app-id="{{ $app->application_id }}">Review Application
                                    </button>
                                    <button
                                        onclick="window.location.href= '{{ route('rescuer.messages', ['receiver_id' => $app->adopter->user->user_id]) }}'"
                                        class="btn btn-outline">Message</button>
                                    </button>
                                </div>
                            </li>
                        @empty
                            <li style="color: rgb(123, 123, 123);">No Recent Applications</li>
                        @endforelse
                    </ul>
                </div>

                <!-- Recent Messages -->
                <div class="content-card">
                    <div class="card-header">
                        <h2>Recent Messages</h2>
                        <a href="{{ route('rescuer.messages') }}" class="btn btn-outline">View All</a>
                    </div>
                    <ul class="application-list">
                        @forelse($recentMessages->take(2) as $msg)
                            <li class="application-item">
                                <div class="applicant-info">
                                    <strong>{{ $msg->sender->name ?? 'User' }}</strong>
                                    @if (isset($msg->pet))
                                        <div class="pet-details">Re: {{ $msg->pet->name }} - {{ $msg->pet->breed }}</div>
                                    @endif
                                    <div class="pet-details">
                                        {{ Str::limit($msg->content, 60) }}
                                    </div>
                                    <button
                                        onclick="window.location.href='{{ route('rescuer.messages', ['receiver_id' => $msg->sender->user_id]) }}'"
                                        class="reply-btn">
                                        Reply
                                    </button>
                                </div>
                            </li>
                        @empty
                            <li style="color: rgb(123, 123, 123);">No Recent Messages</li>
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
                                </div>
                                <div class="review-rating">
                                </div>
                            </div>
                        </div>
                        <div class="review-content">
                            "{{ $review->comment }}"
                        </div>
                    @empty
                        <div style="color: rgb(123, 123, 123);">No Recent Reviews</div>
                    @endforelse
                </div>
            </div>
        </div><!-- .rescuer-content-wrapper -->
    </main>

    {{-- add pet modal --}}
    <div id="addPetModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Add New Pet</h2>
                <button class="close-btn" type="button">&times;</button>
            </div>
            <div class="modal-body">
                <form id="addPetForm" method="POST" action="{{ route('rescuer.pets.create') }}"
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


    <div id="rescuerModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Rescuer Information</h2>
                <span class="close">&times;</span>
            </div>
            <div class="rescuer-info">
                <div class="rescuer-header">
                    <img src="rescuer-logo.jpg" alt="Rescuer Logo" class="rescuer-logo">
                    <div class="rescuer-details">
                        <h3 class="rescuer-name">Rescuer Name</h3>
                        <div class="rescuer-rating">
                            <span class="rating-stars">★★★★★</span>
                            <span class="rating-number">4.8</span>
                            <span class="total-reviews">(45 reviews)</span>
                        </div>
                        <p class="rescuer-location">Location</p>
                    </div>
                </div>
                <div class="rescuer-stats">
                    <div class="stat-item">
                        <span class="stat-number">120+</span>
                        <span class="stat-label">Pets Rescued</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">5 years</span>
                        <span class="stat-label">Experience</span>
                    </div>
                </div>
                <div class="rescuer-reviews">
                    <h4>Recent Reviews</h4>
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
            function showApplicationModal(id) {
                fetch(`/rescuer/applications/${id}/review`)
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
                                    window.location.href = "{{ route('rescuer.pet-management') }}";
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
                        fetch(`/rescuer/applications/${id}/approve`, {
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
                        fetch(`/rescuer/applications/${id}/request-info`, {
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

                fetch(`/rescuer/applications/${currentApplicationId}/reject`, {
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

            // Rescuer modal logic (optional, for completeness)
            function openRescuerModal(rescuerId) {
                const modal = document.getElementById('rescuerModal');
                modal.style.display = 'block';
                fetchRescuerData(rescuerId);
            }

            function closeRescuerModal() {
                const modal = document.getElementById('rescuerModal');
                modal.style.display = 'none';
            }
            async function fetchRescuerData(rescuerId) {
                // Example data structure:
                const rescuerData = {
                    name: "Strays Worth Saving",
                    rating: 4.8,
                    totalReviews: 45,
                    location: "Los Angeles, CA",
                    rescues: 120,
                    experience: "5 years",
                    reviews: [{
                        name: "John Doe",
                        rating: 5,
                        date: "March 15, 2024",
                        content: "Amazing experience adopting from this rescuer."
                    }]
                };
                // updateModalContent(rescuerData); // Implement as needed
            }
            document.querySelector('.close')?.addEventListener('click', closeRescuerModal);
            window.addEventListener('click', (event) => {
                const modal = document.getElementById('rescuerModal');
                if (event.target === modal) {
                    closeRescuerModal();
                }
            });
        }); // End of DOMContentLoaded
    </script>
@endsection

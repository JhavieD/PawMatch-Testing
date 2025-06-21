@extends('layouts.shelter')

@section('title', 'Shelter Dashboard - PawMatch')

@section('shelter-content')
<!-- Main Content -->
<main class="shelter-main-content">
    <!-- Centering Wrapper -->
    <div class="shelter-content-wrapper">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="welcome-section">
                <h1>Shelter Name</h1>
                <p>Welcome back! Here's what's happening at your shelter</p>
            </div>
            <div class="profile-section">
                <img src="https://scontent.fmnl17-1.fna.fbcdn.net/v/t39.30808-6/347439792_262689872915779_1734511534281161924_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=6ee11a&_nc_eui2=AeEx3xFl7u5jfTV_5eckNDfEgqlXj1z3avOCqVePXPdq89whJ29W46pl6MVM84KD1wjFepXD-UaW6DDSW4eQHod7&_nc_ohc=m_7I_NE9-K0Q7kNvgFi0lNg&_nc_oc=AdiRt7GPOP7QJ-gxFl1lG4A2UBe1eZ6L8UajEeeXX8PUb4BGMftVOv8-jx1oI9sk0LA&_nc_zt=23&_nc_ht=scontent.fmnl17-1.fna&_nc_gid=ApMVLbMVp0Xh_QdDjSWwWuS&oh=00_AYHRwxYGUVlma7qO1-YvO5im2ZUUEf-Y_wPUtUTpjQBrEg&oe=67D60523" alt="Profile Picture" class="profile-img" />
                <button class="btn btn-primary add-pet-btn">+ Add New Pet</button>
            </div>
        </div>

        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">🐾</div>
                </div>
                <div class="stat-number">{{$availablePets}}</div>
                <div class="stat-label">Available Pets</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">📝</div>
                </div>
                <div class="stat-number">{{ $pendingApplications }}</div>
                <div class="stat-label">Pending Applications</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">✨</div>
                </div>
                <div class="stat-number">{{$successfulAdoptions}}</div>
                <div class="stat-label">Successful Adoptions</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">💌</div>
                </div>
                <div class="stat-number">{{$newMessages}}</div>
                <div class="stat-label">New Messages</div>
            </div>
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-icon">⭐</div>
                </div>
                <div class="stat-number">{{ $averageRating }}</div>
                <div class="stat-label">
                    @for ($i = 0; $i < round($averageRating); $i++)
                        ★
                        @endfor
                        <span style="color: #888;">({{$totalReviews}})</span>
                </div>
            </div>
        </div>



        <div class="content-grid">
            <!-- Pet Management -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Recent Pets</h2>
                    <a href="{{ route('shelter.pets') }}" class="btn btn-outline">View All</a>
                </div>
                <ul class="pet-list">
                    @forelse ($recentPets as $pet)
                    <li class="pet-item">
                        <img src="{{ $pet ->image_url ?? 'https://placehold.co/60x60' }}" alt="{{ $pet->name }}" class="pet-image-small">
                        <div class="pet-info">
                            <div class="pet-name">{{ $pet->name }}</div>
                            <div class="pet-details">{{ $pet->breed }} • {{ $pet->age }} years</div>
                            <span class="status status-{{ $pet->adoption_status }}">
                                {{ ucfirst($pet->adoption_status)}}
                            </span>
                        </div>
                    </li>
                    @empty
                    <li>No Recent Pets.</li>
                    @endforelse
                </ul>
            </div>

            <!-- Recent Applications -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Recent Applications</h2>
                    <a href="{{ route('shelter.pet_applications') }}" class="btn btn-outline">View All</a>
                </div>
                <ul class="application-list">
                    @forelse($recentApplications as $app)
                    <li class="application-item">
                        <div class="applicant-info">
                            <strong>{{ $app->adopter->user->first_name ?? 'Applicant' }}</strong> applied to adopt <strong>{{ $app->pet->name ?? 'Pet' }}</strong>
                        </div>
                        <div class="pet-details">{{ $app->created_at->diffForHumans() }}</div>
                        <div class="btn-group" style="margin-top: 0.5rem;">
                            <button class="btn btn-primary" onclick="showApplicationModal({{ $app->application_id }})">Review Application</button>
                            <button onclick="window.location.href= '{{route('shelter.messages', ['receiver_id' => $app->adopter->user->user_id]) }}'" class="btn btn-outline">Message</button>
                        </div>
                    </li>
                    @empty
                    <li>No recent Applications</li>
                    @endforelse
                </ul>
            </div>

            <!-- Recent Messages -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Recent Messages</h2>
                    <a href="{{ route('shelter.messages') }}" class="btn btn-outline">View All</a>
                </div>
                <ul class="application-list">
                    @forelse($recentMessages as $msg)
                    <li class="application-item">
                        <div class="applicant-info">
                            <strong>{{ $msg->sender->name ?? 'User'}}</strong>
                            @if(isset($msg->pets))
                            <div class="pet-details">Re: {{ $msg->pets->name}} - {{$msg->pet->breed}}</div>
                            @endif
                        </div>
                        <div class="pet-details">
                            {{ Str::limit($msg->content, 60) }}
                        </div>
                        <button onclick="window.location.href='{{ route('shelter.messages', ['receiver_id' => $msg->sender->user_id]) }}'" class="btn btn-outline" style="margin-top: 0.5rem;">
                            Reply
                        </button>
                    </li>
                    @empty
                    <li>No recent Messages</li>
                    @endforelse
                </ul>
            </div>
        </div><!-- .content-grid -->

        <!-- Reviews Section -->
        <div class="content-card" style="margin-top: 2rem;">
            <div class="card-header">
                <h2>Recent Reviews</h2>
                <!-- <div class="rating-breakdown">
                    <div class="rating-bar">
                        <span>5 ★</span>
                        <span>35</span>
                    </div>
                    <div class="rating-bar">
                        <span>4 ★</span>
                        <span>7</span>
                    </div>
                    <div class="rating-bar">
                        <span>3 ★</span>
                        <span>2</span>
                    </div>
                    <div class="rating-bar">
                        <span>2 ★</span>
                        <span>1</span>
                    </div>
                    <div class="rating-bar">
                        <span>1 ★</span>
                        <span>0</span>
                    </div>
                </div> -->
            </div>
            <div class="reviews-list">
                @forelse($recentReviews as $review)
                <div class="review-card">
                    <div class="review-header">
                        <div class="reviewer-info">
                            <img src="{{ $review->user->profile_picture ?? 'https://placehold.co/40x40' }}" alt="Reviewer" class="reviewer-image">
                            <div>
                                <div class="reviewer-name">{{ $review->user->name ?? 'User' }}</div>
                                <div class="review-date">{{ $review->created_at->format('F d, Y') }}</div>
                            </div>
                        </div>
                        <div class="review-rating">
                            @for ($i = 0; $i < $review->rating; $i++)
                                ★
                                @endfor
                        </div>
                    </div>
                </div>

                <div class="review-content">
                    "{{$review->comment}}"
                </div>
            </div>
            @empty
            <div>no recent reviews</div>
        </div>
        @endforelse
    </div><!-- .content-wrapper -->
</main>

<!-- Add New Pet Modal (FROM PET.BLADE.PHP SAME FUNCTIONALITY) -->
<div id="addPetModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New Pet</h2>
            <button class="close-btn" type="button">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addPetForm" method="POST" action="{{ route('shelter.pets.store') }}" enctype="multipart/form-data">
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
                </div>
                <div class="stat-item">
                    <span class="stat-number">5 years</span>
                    <span class="stat-label">Experience</span>
                </div>
            </div>
            <div class="shelter-reviews">
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
            <button class="close-btn">&times;</button>
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
    // Select modals
    const addPetModal = document.getElementById('addPetModal');
    const closeBtns = document.querySelectorAll('.close-btn');

    // Function to open a modal
    function openModal(modal) {
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }

    function viewApplicationDetails(applicationId) {
        // Redirect to the application details page
        window.location.href = `applications-review.html?id=${applicationId}`;
    }

    function messageApplicant(applicantName) {
        // Redirect to messages with the applicant
        window.location.href = `messages.html?applicant=${encodeURIComponent(applicantName)}`;
    }

    // Function to close a modal
    function closeModal(modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

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
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
            closeModal(addPetModal);
        });
    }

    // Function to open shelter modal
    function openShelterModal(shelterId) {
        const modal = document.getElementById('shelterModal');
        modal.style.display = 'block';

        // fetch the shelter's rating and reviews data
        fetchShelterData(shelterId);
    }

    // Function to close shelter modal
    function closeShelterModal() {
        const modal = document.getElementById('shelterModal');
        modal.style.display = 'none';
    }

    // Function to fetch shelter data
    async function fetchShelterData(shelterId) {
        // This would be replaced with actual API call
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
                }
                // More reviews...
            ]
        };

        // Populate modal with data
        updateModalContent(shelterData);
    }

    // Add event listeners
    document.querySelector('.close').addEventListener('click', closeShelterModal);
    window.addEventListener('click', (event) => {
        const modal = document.getElementById('shelterModal');
        if (event.target === modal) {
            closeShelterModal();
        }
    });

    function showApplicationModal(id) {
        fetch(`/shelter/applications/${id}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('applicationModalBody').innerHTML = html;
                const modal = document.getElementById('applicationModal');
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
                attachActionHandlers(id);
            });
    }

    document.querySelector('#applicationModal .close-btn')?.addEventListener('click', () => {
    const modal = document.getElementById('applicationModal');
    closeModal(modal);
    });

    const csrf = '{{ csrf_token() }}';
    let currentApplicationId = null;
const modal = document.getElementById('applicationModal');
const rejectionModal = document.getElementById('rejectionModal');
const closeBtn = document.querySelector('.close-btn');
const closeRejectionBtn = document.querySelector('.close-rejection-btn');
const cancelRejectionBtn = document.getElementById('cancelRejectionBtn');
const confirmRejectionBtn = document.getElementById('confirmRejectionBtn');

closeBtn?.addEventListener('click', () => closeModal(modal));
closeRejectionBtn?.addEventListener('click', () => closeModal(rejectionModal));
cancelRejectionBtn?.addEventListener('click', () => closeModal(rejectionModal));

window.addEventListener('click', (e) => {
    if (e.target === modal) closeModal(modal);
    if (e.target === rejectionModal) closeModal(rejectionModal);
});

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
                    }).then(res => res.json()).then(() => {
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
                body: JSON.stringify({ rejection_reason: reason })
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
</script>
@endsection
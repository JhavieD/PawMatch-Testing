@extends('layouts.pet-listings')    

@section('title', 'Pet-Listings - PawMatch')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adopter/pet-listing.css') }}">
@endpush

@section('adopter-content')
<div class="main-container">
    <!-- Filters Panel -->
    <aside class="filters">
        <form action="{{ route('adopter.pet-listings') }}" method="GET" id="filterForm">
            <div class="filter-group">
                <h3>Pet Type</h3>
                @foreach($petTypes as $type)
                <div class="filter-option">
                    <label>
                        <input type="checkbox" name="type[]" value="{{ $type }}" {{ in_array($type, request('type', [])) ? 'checked' : '' }}>
                        {{ ucfirst($type) }}s
                    </label>
                </div>
                @endforeach
            </div>

            <div class="filter-group">
                <h3>Age</h3>
                @foreach($ageGroups as $age)
                <div class="filter-option">
                    <label>
                        <input type="checkbox" name="age[]" value="{{ $age }}" {{ in_array($age, request('age', [])) ? 'checked' : '' }}>
                        {{ ucfirst($age) }}
                    </label>
                </div>
                @endforeach
            </div>

            <div class="filter-group">
                <h3>Size</h3>
                @foreach($sizes as $size)
                <div class="filter-option">
                    <label>
                        <input type="checkbox" name="size[]" value="{{ $size }}" {{ in_array($size, request('size', [])) ? 'checked' : '' }}>
                        {{ ucfirst($size) }}
                    </label>
                </div>
                @endforeach
            </div>

            <div class="filter-group">
                <h3>Match My Purpose</h3>
                <div class="filter-option" style="display: flex; align-items: flex-start; gap: 0.5em; flex-direction: column;">
                    <label style="display: flex; align-items: center; gap: 0.5em;">
                        <input type="checkbox" name="match_purpose" value="1" {{ request('match_purpose') ? 'checked' : '' }}>
                        <span>Show pets suitable for:</span>
                    </label>
                    <span style="margin-left: 1.8em; font-weight: bold;">{{ auth()->user()->adopter->purpose ?? 'Not specified' }}</span>
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Apply Filters</button>
        </form>
    </aside>

    <!-- Main Content -->
    <main>
        <div class="pet-grid">
        @forelse($pets as $pet)
            @if($pet->status === 'available' || $pet->adoption_status === 'available')
                    <div class="pet-card" data-pet-id="{{ $pet->pet_id }}">
                        @if($pet->images->isNotEmpty())
                            <img src="{{ $pet->images->first()->image_url }}" alt="{{ $pet->name }} the {{ $pet->breed }}, {{ $pet->age }} years old" class="pet-image" style="font-family: 'Inter', sans-serif;">
                        @else
                            <img src="{{ asset('images/default-pet.png') }}" alt="No image available for {{ $pet->name }}" class="pet-image" style="font-family: 'Inter', sans-serif;">
                        @endif
                        <div class="pet-info">
                            <h3 class="pet-name">{{ $pet->name }}</h3>
                            <p class="pet-details">{{ $pet->breed }} • {{ $pet->age }} years old<br>{{ $pet->shelter->city ?? '' }}</p>
                            <span class="pet-status">{{ $pet->status }}</span>
                        </div>
                    </div>
                </a>
            @endif
            @empty
            <div class="no-pets-message">
                <p>No pets found matching your criteria.</p>
            </div>
            @endforelse
        </div>

        {{ $pets->links() }}
    </main>
</div>

<!-- Pet Details Modal -->
<div id="petDetailsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Pet Details</h2>
            <button class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <div class="pet-gallery">
                <img src="" alt="Main pet photo" class="main-pet-image" id="mainImage" style="font-family: 'Inter', sans-serif;">
                <div class="thumbnail-grid" id="thumbnailGrid">
                    <!-- Thumbnails will be populated dynamically -->
                </div>
            </div>

            <h2 id="petName" class="pet-name"></h2>
            <div class="pet-details-grid">
                <div class="detail-item">
                    <label>Breed</label>
                    <p id="petBreed"></p>
                </div>
                <div class="detail-item">
                    <label>Age</label>
                    <p id="petAge"></p>
                </div>
                <div class="detail-item">
                    <label>Gender</label>
                    <p id="petGender"></p>
                </div>
                <div class="detail-item">
                    <label>Size</label>
                    <p id="petSize"></p>
                </div>
                <div class="detail-item">
                    <label>Status</label>
                    <p id="petStatus"></p>
                </div>
            </div>

            <div class="pet-description">
                <h3>About <span id="petNameDesc"></span></h3>
                <p id="petDescription"></p>
            </div>

            <div class="shelter-info">
                <h3>Shelter Information</h3>
                <p id="shelterName"></p>
                <p id="shelterAddress"></p>
                <p id="shelterPhone"></p>
            </div>

            <div class="modal-actions">
                <button id="message-shelter" class="btn-messages"> Message Shelter </button>
                <button id="applyButton" class="btn">Apply for Adoption</button>
                <button id="favoriteButton" class="btn" style="background: #f3f4f6; color: #4b5563;">Save to Favorites</button>
            </div>
        </div>
    </div>
</div>

<!-- Adoption Application Modal -->
<div id="adoptionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Adoption Application</h2>
            <button class="close-btn" id="closeAdoptionModal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="adoptionForm">
                <input type="hidden" name="pet_id" id="adoptionPetId">
                <div class="form-group">
                    <label for="reason_for_adoption">Why do you want to adopt this pet?</label>
                    <textarea name="reason_for_adoption" id="reason_for_adoption" required></textarea>
                </div>
                <div class="form-group">
                    <label for="living_arrangement">Living Arrangement</label>
                    <input type="text" name="living_arrangement" id="living_arrangement" required>
                </div>
                <div class="form-group">
                    <label for="experience_with_pets">Experience with Pets</label>
                    <input type="text" name="experience_with_pets" id="experience_with_pets" required>
                </div>
                <div class="form-group">
                    <label for="household_members">Household Members</label>
                    <input type="number" name="household_members" id="household_members" required>
                </div>
                <div class="form-group">
                    <label for="allergies">Allergies</label>
                    <select name="allergies" id="allergies" required>
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="has_other_pets">Do you have other pets?</label>
                    <select name="has_other_pets" id="has_other_pets" required>
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div class="form-group" id="otherPetsDetailsGroup" style="display:none;">
                    <label for="other_pets_details">Other Pets Details</label>
                    <input type="text" name="other_pets_details" id="other_pets_details">
                </div>
                <div class="form-group">
                    <label for="can_provide_vet_care">Can you provide vet care?</label>
                    <select name="can_provide_vet_care" id="can_provide_vet_care" required>
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Modal functionality
    const modal = document.getElementById('petDetailsModal');
    const closeBtn = document.querySelector('.close-btn');
    const petCards = document.querySelectorAll('.pet-card');
    const mainImage = document.getElementById('mainImage');
    const thumbnailGrid = document.getElementById('thumbnailGrid');
    const applyButton = document.getElementById('applyButton');
    const favoriteButton = document.getElementById('favoriteButton');
    const messageShelterButton = document.getElementById('message-shelter');
    let currentShelterUserId = null;

    function getQueryParam(param) {
        const urlParams = new URLSearchParams(window.location.search);
        return urlParams.get(param);
    }

    async function openPetModalById(petId) {
        // Find the card and simulate click, or fetch and open modal directly
        try {
            // Fetch pet details
            const petDetailsResponse = await fetch(`/api/pets/${petId}`);
            const petDetails = await petDetailsResponse.json();

            // Fetch pet images
            const petImagesResponse = await fetch(`/api/pets/${petId}/images`);
            const petImagesData = await petImagesResponse.json();

            // Update modal content
            document.getElementById('petName').textContent = petDetails.name;
            document.getElementById('petNameDesc').textContent = petDetails.name;
            document.getElementById('petBreed').textContent = petDetails.breed;
            document.getElementById('petAge').textContent = `${petDetails.age} years`;
            document.getElementById('petGender').textContent = petDetails.gender;
            document.getElementById('petSize').textContent = petDetails.size;
            document.getElementById('petStatus').textContent = petDetails.status;
            document.getElementById('petDescription').textContent = petDetails.description;
            document.getElementById('shelterName').textContent = petDetails.shelter.name;
            document.getElementById('shelterAddress').textContent = petDetails.shelter.address;
            document.getElementById('shelterPhone').textContent = petDetails.shelter.phone;

            // Update images
            if (petImagesData.images.length > 0) {
                mainImage.src = petImagesData.images[0].image_url;
                thumbnailGrid.innerHTML = petImagesData.images.map(img => 
                    `<img src="${img.image_url}" alt="Pet photo" class="thumbnail" style="font-family: 'Inter', sans-serif;">`
                ).join('');
            } else {
                mainImage.src = '';
                thumbnailGrid.innerHTML = '<p>No images available.</p>';
            }

            // Update buttons
            applyButton.dataset.petId = petId;
            applyButton.onclick = function() {
                adoptionModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
                adoptionPetId.value = petId;
            };
            favoriteButton.textContent = petDetails.is_favorite ? 'Remove from Favorites' : 'Save to Favorites';
            favoriteButton.dataset.petId = petId;

            // Set the message button handler for this pet
            const messageShelterBtn = document.getElementById('message-shelter');
            messageShelterBtn.onclick = async function () {
                const shelterUserId = petDetails.user_id || (petDetails.shelter && petDetails.shelter.user_id);
                if (shelterUserId) {
                    try {
                        const res = await fetch('/messages', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                receiver_id: shelterUserId,
                                message: `Hi! I'm interested in adopting ${petDetails.name} from your shelter.`
                            })
                        });
                        const data = await res.json();
                        if (!res.ok) {
                            alert('Failed to send message: ' + (data.message || res.status));
                            return;
                        }
                        setTimeout(() => {
                            window.location.href = '/adopter/messages?receiver_id=' + shelterUserId;
                        }, 400);
                    } catch (e) {
                        alert('Error sending message: ' + e);
                    }
                } else {
                    alert('Shelter user ID not found. Please try again later.');
                }
            };

            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        } catch (error) {
            console.error('Error fetching pet details:', error);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const petIdFromQuery = getQueryParam('pet_id');
        if (petIdFromQuery) {
            openPetModalById(petIdFromQuery);
        }
    });

    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    petCards.forEach(card => {
    card.addEventListener('click', async () => {
        const petId = card.dataset.petId;
        console.log('Pet card clicked, petId:', petId);
        try {
            // Fetch pet details
            const petDetailsResponse = await fetch(`/api/pets/${petId}`);
            const petDetails = await petDetailsResponse.json();

            // Fetch pet images
            const petImagesResponse = await fetch(`/api/pets/${petId}/images`);
            const petImagesData = await petImagesResponse.json();

            // Update modal content
            document.getElementById('petName').textContent = petDetails.name;
            document.getElementById('petNameDesc').textContent = petDetails.name;
            document.getElementById('petBreed').textContent = petDetails.breed;
            document.getElementById('petAge').textContent = `${petDetails.age} years`;
            document.getElementById('petGender').textContent = petDetails.gender;
            document.getElementById('petSize').textContent = petDetails.size;
            document.getElementById('petStatus').textContent = petDetails.status;
            document.getElementById('petDescription').textContent = petDetails.description;
            document.getElementById('shelterName').textContent = petDetails.shelter.name;
            document.getElementById('shelterAddress').textContent = petDetails.shelter.address;
            document.getElementById('shelterPhone').textContent = petDetails.shelter.phone;

            // Update images
            if (petImagesData.images.length > 0) {
                mainImage.src = petImagesData.images[0].image_url;
                thumbnailGrid.innerHTML = petImagesData.images.map(img => 
                    `<img src="${img.image_url}" alt="Pet photo" class="thumbnail" style="font-family: 'Inter', sans-serif;">`
                ).join('');
            } else {
                mainImage.src = '';
                thumbnailGrid.innerHTML = '<p>No images available.</p>';
            }

                // Update buttons
                applyButton.dataset.petId = petId;
                applyButton.onclick = function() {
                    adoptionModal.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                    adoptionPetId.value = petId;
                };
                favoriteButton.textContent = petDetails.is_favorite ? 'Remove from Favorites' : 'Save to Favorites';
                favoriteButton.dataset.petId = petId;

                // Set the message button handler for this pet
                const messageShelterBtn = document.getElementById('message-shelter');
                messageShelterBtn.onclick = async function () {
                    const shelterUserId = petDetails.user_id || (petDetails.shelter && petDetails.shelter.user_id);
                    if (shelterUserId) {
                        try {
                            const res = await fetch('/messages', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                },
                                body: JSON.stringify({
                                    receiver_id: shelterUserId,
                                    message: `Hi! I'm interested in adopting ${petDetails.name} from your shelter.`
                                })
                            });
                            const data = await res.json();
                            if (!res.ok) {
                                alert('Failed to send message: ' + (data.message || res.status));
                                return;
                            }
                            // Wait a short moment to ensure the message is saved before redirecting
                            setTimeout(() => {
                                window.location.href = '/adopter/messages?receiver_id=' + shelterUserId;
                            }, 400);
                        } catch (e) {
                            alert('Error sending message: ' + e);
                        }
                    } else {
                        alert('Shelter user ID not found. Please try again later.');
                    }
                };

                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            } catch (error) {
                console.error('Error fetching pet details:', error);
            }
        });
    });

    closeBtn.addEventListener('click', closeModal);

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Gallery functionality
    thumbnailGrid.addEventListener('click', (e) => {
        if (e.target.classList.contains('thumbnail')) {
            mainImage.src = e.target.src;
        }
    });

    // Favorite functionality
    favoriteButton.addEventListener('click', async (e) => {
        e.preventDefault();
        const petId = e.target.dataset.petId;
        try {
            const response = await fetch(`/api/pets/${petId}/favorite`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            const data = await response.json();
            favoriteButton.textContent = data.is_favorite ? 'Remove from Favorites' : 'Save to Favorites';
            if (typeof refreshFavoritePetsSection === 'function') {
                refreshFavoritePetsSection();
            }
        } catch (error) {
            console.error('Error toggling favorite:', error);
        }
    });

    // Auto-open modal if redirected with #pet-{id}
    document.addEventListener('DOMContentLoaded', function() {
        const hash = window.location.hash;
        if (hash && hash.startsWith('#pet-')) {
            const petId = hash.replace('#pet-', '');
            const card = document.querySelector(`.pet-card[data-pet-id="${petId}"]`);
            if (card) {
                card.click();
            }
        }
    });

    const adoptionModal = document.getElementById('adoptionModal');
    const closeAdoptionModal = document.getElementById('closeAdoptionModal');
    const adoptionForm = document.getElementById('adoptionForm');
    const adoptionPetId = document.getElementById('adoptionPetId');
    const hasOtherPets = document.getElementById('has_other_pets');
    const otherPetsDetailsGroup = document.getElementById('otherPetsDetailsGroup');

    closeAdoptionModal.addEventListener('click', function() {
        adoptionModal.style.display = 'none';
        document.body.style.overflow = 'auto';
    });

    window.addEventListener('click', (e) => {
        if (e.target === adoptionModal) {
            adoptionModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });

    hasOtherPets.addEventListener('change', function() {
        if (this.value == '1') {
            otherPetsDetailsGroup.style.display = 'block';
        } else {
            otherPetsDetailsGroup.style.display = 'none';
        }
    });

    adoptionForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(adoptionForm);
        fetch('/adopter/applications', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: formData
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('Application submitted successfully!');
                adoptionModal.style.display = 'none';
                modal.style.display = 'none';
                document.body.style.overflow = 'auto';
                window.location.href = '/adopter/application-status';
            } else {
                alert('Error submitting application.');
            }
        })
        .catch(() => alert('Error submitting application.'));
    });
</script>
@endsection
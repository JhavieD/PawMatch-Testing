@extends('layouts.pet-listings')    

@section('title', 'Pet-Listings - PawMatch')

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

            <button type="submit" class="btn btn-primary">Apply Filters</button>
        </form>
    </aside>

    <!-- Main Content -->
    <main>
        <div class="search-bar">
            <form action="{{ route('adopter.pet-listings') }}" method="GET">
                <input type="text" name="search" class="search-input" placeholder="Search pets by name, breed, or location..." value="{{ request('search') }}">
            </form>
        </div>

        <div class="pet-grid">
            @forelse($pets as $pet)
            <div class="pet-card" data-pet-id="{{ $pet->id }}">
                <img src="{{ $pet->image_url }}" alt="{{ $pet->name }}" class="pet-image">
                <div class="pet-info">
                    <h3 class="pet-name">{{ $pet->name }}</h3>
                    <p class="pet-details">{{ $pet->breed }} • {{ $pet->age }} years old<br>{{ $pet->shelter->city }}</p>
                    <span class="pet-status">{{ $pet->status }}</span>
                </div>
            </div>
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
                <img src="" alt="Main pet photo" class="main-image" id="mainImage">
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
                    <label>Weight</label>
                    <p id="petWeight"></p>
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
                <a href="#" id="applyButton" class="btn">Apply for Adoption</a>
                <button id="favoriteButton" class="btn" style="background: #f3f4f6; color: #4b5563;">Save to Favorites</button>
            </div>
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

    function closeModal() {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    petCards.forEach(card => {
        card.addEventListener('click', async () => {
            const petId = card.dataset.petId;
            try {
                const response = await fetch(`/api/pets/${petId}`);
                const pet = await response.json();
                
                // Update modal content
                document.getElementById('petName').textContent = pet.name;
                document.getElementById('petNameDesc').textContent = pet.name;
                document.getElementById('petBreed').textContent = pet.breed;
                document.getElementById('petAge').textContent = `${pet.age} years`;
                document.getElementById('petGender').textContent = pet.gender;
                document.getElementById('petSize').textContent = pet.size;
                document.getElementById('petWeight').textContent = `${pet.weight} lbs`;
                document.getElementById('petStatus').textContent = pet.status;
                document.getElementById('petDescription').textContent = pet.description;
                document.getElementById('shelterName').textContent = pet.shelter.name;
                document.getElementById('shelterAddress').textContent = pet.shelter.address;
                document.getElementById('shelterPhone').textContent = pet.shelter.phone;

                // Update images
                mainImage.src = pet.images[0];
                thumbnailGrid.innerHTML = pet.images.map(img => 
                    `<img src="${img}" alt="Pet photo" class="thumbnail">`
                ).join('');

                // Update buttons
                applyButton.href = `/adoption-form/${petId}`;
                favoriteButton.textContent = pet.is_favorite ? 'Remove from Favorites' : 'Save to Favorites';
                favoriteButton.dataset.petId = petId;

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
        } catch (error) {
            console.error('Error toggling favorite:', error);
        }
    });
</script>
@endsection
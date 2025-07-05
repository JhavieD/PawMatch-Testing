@extends('layouts.app')

@section('title', 'Find Pets - PawMatch')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/shared/public-pet-listings.css') }}">
@endpush

@section('content')
<div class="public-main-container">
    <h1>Browse Available Pets</h1>
    <main style="width:100%">
        <div class="pet-grid">
        @forelse($pets as $pet)
            @if($pet->status === 'available' || $pet->adoption_status === 'available')
                <div class="pet-card" data-pet-id="{{ $pet->pet_id ?? $pet->id }}"
                    data-pet-name="{{ $pet->name }}"
                    data-pet-description="{{ $pet->description }}"
                    data-pet-age="{{ $pet->age }}"
                    data-pet-gender="{{ $pet->gender }}"
                    data-pet-size="{{ $pet->size }}"
                    data-pet-breed="{{ $pet->breed }}"
                    data-pet-images='@json($pet->images)'
                    data-shelter-name="{{ $pet->shelter->name ?? '' }}"
                    data-shelter-address="{{ $pet->shelter->address ?? $pet->shelter->city ?? '' }}"
                    data-shelter-phone="{{ $pet->shelter->phone ?? '' }}">
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
            @endif
        @empty
            <div class="no-pets-message">
                <p>No pets found matching your criteria.</p>
            </div>
        @endforelse
        </div>
        {{ $pets->links() }}
    </main>
    <!-- Pet Details Modal -->
    <div id="petDetailsModal" class="modal" style="display:none;">
        <div class="modal-content" style="max-width:900px;padding:2rem;position:relative;">
            <span class="close-btn" style="position:absolute;top:1.5rem;right:2rem;cursor:pointer;font-size:2rem;">&times;</span>
            <div id="modalLoadingSpinner" style="display:none;justify-content:center;align-items:center;height:320px;">
                <div class="spinner" style="border: 6px solid #f3f3f3; border-top: 6px solid #4a90e2; border-radius: 50%; width: 48px; height: 48px; animation: spin 1s linear infinite;"></div>
            </div>
            <div id="modalContentArea">
                <h2 style="margin-top:0;">Pet Details</h2>
                <div class="pet-details-flex" style="display:flex;gap:2rem;flex-wrap:wrap;">
                    <div class="pet-image-area" style="flex:1 1 260px;min-width:260px;max-width:320px;display:flex;align-items:center;justify-content:center;background:#f7f7f7;border-radius:12px;min-height:260px;">
                        <img id="modalMainImage" src="" alt="Main pet photo" style="max-width:100%;max-height:260px;border-radius:8px;object-fit:cover;display:none;">
                        <span id="modalNoImage" style="color:#888;font-size:1rem;">No images available.</span>
                    </div>
                    <div class="pet-details-info" style="flex:2 1 320px;min-width:320px;">
                        <h3 id="modalPetName" style="margin-bottom:0.5rem;"></h3>
                        <div class="pet-attributes" style="display:flex;gap:1rem;flex-wrap:wrap;margin-bottom:1rem;">
                            <div class="attr-card" style="background:#f3f4f6;padding:0.75rem 1.2rem;border-radius:8px;min-width:110px;">
                                <div style="font-size:0.9em;color:#888;">Breed</div>
                                <div id="modalPetBreed" style="font-weight:500;"></div>
                            </div>
                            <div class="attr-card" style="background:#f3f4f6;padding:0.75rem 1.2rem;border-radius:8px;min-width:110px;">
                                <div style="font-size:0.9em;color:#888;">Age</div>
                                <div id="modalPetAge" style="font-weight:500;"></div>
                            </div>
                            <div class="attr-card" style="background:#f3f4f6;padding:0.75rem 1.2rem;border-radius:8px;min-width:110px;">
                                <div style="font-size:0.9em;color:#888;">Gender</div>
                                <div id="modalPetGender" style="font-weight:500;"></div>
                            </div>
                            <div class="attr-card" style="background:#f3f4f6;padding:0.75rem 1.2rem;border-radius:8px;min-width:110px;">
                                <div style="font-size:0.9em;color:#888;">Size</div>
                                <div id="modalPetSize" style="font-weight:500;"></div>
                            </div>
                            <div class="attr-card" style="background:#f3f4f6;padding:0.75rem 1.2rem;border-radius:8px;min-width:110px;">
                                <div style="font-size:0.9em;color:#888;">Status</div>
                                <div id="modalPetStatus" style="font-weight:500;"></div>
                            </div>
                        </div>
                        <div class="pet-section" style="margin-bottom:1rem;">
                            <div style="font-size:1.05em;font-weight:600;margin-bottom:0.2em;">About <span id="modalPetNameAbout"></span></div>
                            <div id="modalPetDescription" style="color:#444;"></div>
                        </div>
                        <div class="pet-section" style="margin-bottom:1rem;">
                            <div style="font-size:1.05em;font-weight:600;margin-bottom:0.2em;">Shelter Information</div>
                            <div class="shelter-info" style="background:#e8f0fe;padding:1em 1.2em;border-radius:8px;">
                                <div id="modalShelterName" style="font-weight:500;"></div>
                                <div id="modalShelterAddress"></div>
                                <div id="modalShelterPhone"></div>
                            </div>
                        </div>
                        <div class="pet-modal-actions" style="display:flex;gap:1.5em;justify-content:center;margin-top:2em;">
                            <button class="btn btn-outline" style="min-width:140px;opacity:0.6;cursor:not-allowed;" disabled>Message Shelter</button>
                            <button class="btn btn-primary" style="min-width:140px;opacity:0.6;cursor:not-allowed;" disabled>Apply for Adoption</button>
                        </div>
                    </div>
                </div>
                <!-- Image Gallery Thumbnails -->
                <div id="modalImageGallery" style="display:flex;gap:0.5rem;margin-top:1.2rem;justify-content:center;"></div>
            </div>
        </div>
    </div>
    <script>
    function showModalSpinner(show) {
        document.getElementById('modalLoadingSpinner').style.display = show ? 'flex' : 'none';
        document.getElementById('modalContentArea').style.display = show ? 'none' : 'block';
    }
    document.querySelectorAll('.pet-card').forEach(card => {
        card.addEventListener('click', function() {
            const petId = this.dataset.petId;
            console.log('Requesting petId:', petId);
            document.getElementById('petDetailsModal').style.display = 'block';
            showModalSpinner(true);
            fetch(`/api/pets/${petId}`)
                .then(res => res.json())
                .then(data => {
                    console.log('API response for petId', petId, ':', data);
                    if (data.error) throw new Error(data.error);
                    document.getElementById('modalPetName').textContent = data.name;
                    document.getElementById('modalPetNameAbout').textContent = data.name;
                    document.getElementById('modalPetDescription').textContent = data.description;
                    document.getElementById('modalPetAge').textContent = data.age + ' years';
                    document.getElementById('modalPetGender').textContent = data.gender;
                    document.getElementById('modalPetSize').textContent = data.size;
                    document.getElementById('modalPetBreed').textContent = data.breed;
                    document.getElementById('modalPetStatus').textContent = data.status || 'available';
                    document.getElementById('modalShelterName').textContent = data.shelter.name;
                    document.getElementById('modalShelterAddress').textContent = data.shelter.address;
                    document.getElementById('modalShelterPhone').textContent = data.shelter.phone;
                    // Images
                    const images = data.images || [];
                    const mainImage = document.getElementById('modalMainImage');
                    const noImage = document.getElementById('modalNoImage');
                    const gallery = document.getElementById('modalImageGallery');
                    gallery.innerHTML = '';
                    if (images.length > 0) {
                        mainImage.src = images[0];
                        mainImage.style.display = 'block';
                        noImage.style.display = 'none';
                        images.forEach((img, idx) => {
                            const thumb = document.createElement('img');
                            thumb.src = img;
                            thumb.style.width = '54px';
                            thumb.style.height = '54px';
                            thumb.style.objectFit = 'cover';
                            thumb.style.borderRadius = '6px';
                            thumb.style.cursor = 'pointer';
                            thumb.style.border = idx === 0 ? '2px solid #4a90e2' : '2px solid transparent';
                            thumb.onclick = function() {
                                mainImage.src = img;
                                Array.from(gallery.children).forEach((c, i) => c.style.border = i === idx ? '2px solid #4a90e2' : '2px solid transparent');
                            };
                            gallery.appendChild(thumb);
                        });
                    } else {
                        mainImage.src = "{{ asset('images/default-pet.png') }}";
                        mainImage.style.display = 'none';
                        noImage.style.display = 'block';
                    }
                    showModalSpinner(false);
                })
                .catch(err => {
                    console.log('API error for petId', petId, ':', err);
                    showModalSpinner(false);
                    document.getElementById('modalPetName').textContent = 'Pet not found.';
                    document.getElementById('modalPetDescription').textContent = '';
                    document.getElementById('modalMainImage').style.display = 'none';
                    document.getElementById('modalNoImage').style.display = 'block';
                    document.getElementById('modalImageGallery').innerHTML = '';
                });
        });
    });
    document.querySelector('#petDetailsModal .close-btn').onclick = function() {
        document.getElementById('petDetailsModal').style.display = 'none';
    };
    window.onclick = function(event) {
        const modal = document.getElementById('petDetailsModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
    </script>
    <style>
    @keyframes spin { 100% { transform: rotate(360deg); } }
    .spinner { display: inline-block; }
    .modal {
        display: none;
        position: fixed;
        z-index: 9999;
        left: 0; top: 0; width: 100vw; height: 100vh;
        background: rgba(0,0,0,0.32);
        justify-content: center;
        align-items: center;
    }
    .modal[style*='block'] {
        display: flex !important;
    }
    .modal-content {
        background: #fff;
        border-radius: 16px;
        box-shadow: 0 8px 32px rgba(0,0,0,0.18);
        max-width: 900px;
        width: 98vw;
        max-height: 96vh;
        overflow-y: auto;
        position: relative;
        padding: 2rem;
        animation: fadeInModal 0.18s;
    }
    @keyframes fadeInModal { from { opacity: 0; transform: translateY(-16px); } to { opacity: 1; transform: none; } }
    </style>
</div>
@endsection 
@extends('layouts.pet-swipe')

@section('title', 'Pet Swipe - PawMatch')

@section('adopter-content')
<div class="swipe-container"></div>

<div class="match-modal" id="matchModal" style="display:none; font-family: 'Inter', sans-serif;">
    <div class="match-content">
        <div class="match-icon" aria-hidden="true">🎉</div>
        <h2 class="match-title">It's a Match!</h2>
        <p class="match-text">You've found a potential furry friend! Would you like to learn more about this pet?</p>
        <div class="modal-buttons">
            <button class="action-btn like" aria-label="View Details" title="View Details" type="button" style="font-family: 'Inter', sans-serif;" onclick="viewPetDetails()">View Details</button>
            <button class="action-btn dislike" aria-label="Keep Swiping" title="Keep Swiping" type="button" style="font-family: 'Inter', sans-serif;" onclick="closeMatchModal()">Keep Swiping</button>
        </div>
    </div>
</div>

@php
$petsArray = $pets->map(function($pet) {
    return [
        'id' => $pet->id ?? $pet->pet_id,
        'name' => $pet->name,
        'age' => $pet->age,
        'breed' => $pet->breed,
        'image' => $pet->image_url ?? 'https://source.unsplash.com/random/800x1200/?dog',
        'description' => $pet->description,
        'tags' => isset($pet->traits) ? array_map('trim', explode(',', $pet->traits)) : [],
        'shelter' => $pet->shelter->shelter_name ?? '',
        'location' => $pet->shelter->location ?? 'Unknown',
        'rescuer' => $pet->rescuer->name ?? '',
        'gender' => $pet->gender ?? '',
        'size' => $pet->size ?? '',
        'color' => $pet->color ?? '',
    ];
});
@endphp

<script>
    const pets = @json($petsArray);

    let currentPetIndex = 0;
    let startX = 0;
    let currentX = 0;
    let isDragging = false;

    function createPetCard(pet) {
        const card = document.createElement('div');
        card.className = 'pet-card';
        card.innerHTML = `
            <img src="${pet.image}" alt="${pet.name}" class="pet-image">
            <div class="pet-info">
                <h2 class="pet-name">${pet.name ?? 'Unknown Pet'}, ${pet.age ?? '?'} years</h2>
                <div class="pet-details">
                    <span class="pet-detail-item">${pet.breed ?? 'Unknown Breed'}</span>
                    ${pet.gender ? `<span class="pet-detail-item">${pet.gender.charAt(0).toUpperCase() + pet.gender.slice(1)}</span>` : ''}
                </div>
                <p class="pet-description">${pet.description ?? 'No description available.'}</p>
                <div class="pet-tags">
                    ${pet.tags && pet.tags.length ? pet.tags.map(tag => `<span class="tag">${tag}</span>`).join('') : ''}
                </div>
                <div class="shelter-info" style="margin-top:1.2rem; text-align:center;">
                    ${pet.shelter ? `<div><strong>Shelter:</strong> ${pet.shelter}</div>` : ''}
                    ${pet.location ? `<div><strong>Location:</strong> ${pet.location}</div>` : ''}
                </div>
            </div>
        `;
        return card;
    }

    function createActionButtons() {
        const actions = document.createElement('div');
        actions.className = 'action-buttons';
        actions.innerHTML = `
            <button class="action-btn dislike" aria-label="Dislike" title="Dislike" type="button" style="font-family: 'Inter', sans-serif;" onclick="swipeLeft()">&#10060;</button>
            <button class="action-btn like" aria-label="Like" title="Like" type="button" style="font-family: 'Inter', sans-serif;" onclick="swipeRight()">&#10084;&#65039;</button>
        `;
        return actions;
    }

    function initSwipe() {
        const container = document.querySelector('.swipe-container');
        container.innerHTML = '';

        if (currentPetIndex < pets.length) {
            const pet = pets[currentPetIndex];
            const card = createPetCard(pet);
            const actions = createActionButtons();
            container.appendChild(card);
            container.appendChild(actions);

            // Add touch and mouse event listeners
            card.addEventListener('touchstart', handleDragStart);
            card.addEventListener('touchmove', handleDragMove);
            card.addEventListener('touchend', handleDragEnd);

            card.addEventListener('mousedown', handleDragStart);
            card.addEventListener('mousemove', handleDragMove);
            card.addEventListener('mouseup', handleDragEnd);
        } else {
            container.innerHTML = `
                <div class="pet-info-message">
                    <div style="font-size:3.5rem;">🐾</div>
                    <div>No more pets to show!</div>
                    <div style="font-size:1.1rem; color:#6b7280; font-weight:400;">Check back later for new arrivals or adjust your filters.</div>
                </div>
            `;
        }
    }

    function handleDragStart(e) {
        isDragging = true;
        const card = e.target.closest('.pet-card');
        startX = e.type === 'mousedown' ? e.pageX : e.touches[0].pageX;
        currentX = startX;
        card.style.transition = 'none';
    }

    function handleDragMove(e) {
        if (!isDragging) return;
        e.preventDefault();
        const card = e.target.closest('.pet-card');
        currentX = e.type === 'mousemove' ? e.pageX : e.touches[0].pageX;
        const diff = currentX - startX;
        card.style.transform = `translateX(${diff}px) rotate(${diff * 0.1}deg)`;
    }

    function handleDragEnd(e) {
        if (!isDragging) return;
        isDragging = false;
        const card = e.target.closest('.pet-card');
        const diff = currentX - startX;
        card.style.transition = 'transform 0.3s ease';
        if (Math.abs(diff) > 100) {
            card.style.transform = `translateX(${diff > 0 ? 1000 : -1000}px) rotate(${diff * 0.1}deg)`;
            setTimeout(() => {
                if (diff > 0) {
                    swipeRight();
                } else {
                    swipeLeft();
                }
            }, 300);
        } else {
            card.style.transform = 'translateX(0) rotate(0)';
        }
    }

    function swipeLeft() {
        currentPetIndex++;
        initSwipe();
    }

    function swipeRight() {
        if (currentPetIndex < pets.length) {
            showMatchModal();
            currentPetIndex++;
            initSwipe();
        }
    }

    function showMatchModal() {
        const modal = document.getElementById('matchModal');
        modal.style.display = 'flex';
    }

    function closeMatchModal() {
        const modal = document.getElementById('matchModal');
        modal.style.display = 'none';
    }

    function viewPetDetails() {
        // Redirect to browse pets with selected pet id as query param
        const petId = pets[currentPetIndex - 1].id;
        window.location.href = `/adopter/pet-listings?selected=${petId}`;
    }

    document.addEventListener('DOMContentLoaded', initSwipe);
</script>
@endsection

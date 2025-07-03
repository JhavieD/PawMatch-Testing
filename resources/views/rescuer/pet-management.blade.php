@extends('layouts.rescuer-pet-management')

@section('title', 'Pet Management - PawMatch')

@section('rescuer-content')
    <div class="main-content">
        <div class="content-wrapper">
            <div class="header">
                <h1>Pet Management</h1>
                <button class="btn add-pet-btn">+ Add New Pet</button>
            </div>

            <div class="search-bar">
                <input type="text" id="petSearchInput" class="search-input"
                    placeholder="Search pets by name, breed, or ID...">
                <select class="filter-dropdown">
                    <option value="all">All Status</option>
                    <option value="available">Available</option>
                    <option value="pending">Pending</option>
                    <option value="adopted">Adopted</option>
                </select>
            </div>

            <!-- Pet Cards -->
            <div class="pets-grid">
                @forelse($pets as $pet)
                    <div class="pet-card" data-name="{{ $pet->name }}" data-breed="{{ $pet->breed }}"
                        data-status="{{ strtolower($pet->adoption_status ?? $pet->status) }}">
                        <img src="{{ $pet->image_url ?? 'https://placehold.co/400x300' }}" alt="{{ $pet->name }}"
                            class="pet-image">
                        <div class="pet-info">
                            <h3 class="pet-name">{{ $pet->name }}</h3>
                            <p class="pet-details">{{ $pet->breed }} • {{ $pet->age }} years old </p>
                            <span class="pet-status status-{{ $pet->adoption_status }}">
                                {{ ucfirst($pet->adoption_status) }}
                            </span>
                            <div class="card-actions">
                                <button type="button" class="edit-pet-btn" data-pet-id="{{ $pet->pet_id }}"
                                    data-name="{{ $pet->name }}" data-species="{{ $pet->species }}"
                                    data-breed="{{ $pet->breed }}" data-age="{{ $pet->age }}"
                                    data-gender="{{ $pet->gender }}" data-size="{{ $pet->size }}"
                                    data-description="{{ $pet->description }}"
                                    data-adoption_status="{{ $pet->adoption_status }}"
                                    data-behavior="{{ $pet->behavior }}" data-daily_activity="{{ $pet->daily_activity }}"
                                    data-special_needs="{{ $pet->special_needs }}"
                                    data-compatibility="{{ $pet->compatibility }}"
                                    data-images='@json($pet->images)'
                                    data-eating_habits="{{ $pet->eating_habits }}"
                                    data-suitable_for="{{ $pet->suitable_for }}">
                                    Edit
                                </button>
                                <button type="button" class="view-applications-btn" data-pet-id="{{ $pet->pet_id }}"
                                    data-pet-name="{{ $pet->name }}">View Applications</button>
                                <form method="POST" action="{{ route('rescuer.pets.destroy', $pet->pet_id) }}"
                                    style="display: contents;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn"
                                        onclick="return confirm('Are you sure you want to delete this pet?')"
                                        style="justify-content: center;">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div>No pets found.</div>
                @endforelse
            </div>
        </div>

        <!-- Edit Pet Modal -->
        <div id="editPetModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Edit Pet Details</h2>
                    <button class="close-btn">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="editPetForm" method="POST" enctype="multipart/form-data" action="/rescuer/pets/__PET_ID__">
                        @csrf
                        @method('PUT')
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="edit-name">Pet Name</label>
                                <input type="text" name="name" id="edit-name">
                            </div>
                            <div class="form-group">
                                <label for="edit-species">Species</label>
                                <select id="edit-species" name="species" required>
                                    <option value=""></option>
                                    <option value="dog">Dog</option>
                                    <option value="cat">Cat</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit-breed">Breed</label>
                                <input type="text" id="edit-breed" name="breed" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-age">Age</label>
                                <input type="number" id="edit-age" name="age" required>
                            </div>
                            <div class="form-group">
                                <label for="edit-gender">Gender</label>
                                <select id="edit-gender" name="gender" required>
                                    <option value=""></option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit-size">Size</label>
                                <select id="edit-size" name="size" required>
                                    <option value=""></option>
                                    <option value="small">Small</option>
                                    <option value="medium">Medium</option>
                                    <option value="large">Large</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit-description">Description</label>
                            <textarea id="edit-description" name="description" rows="4" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="edit-adoption_status">Status</label>
                            <select id="edit-adoption_status" name="adoption_status" required>
                                <option value="available">Available</option>
                                <option value="pending">Application Pending</option>
                                <option value="adopted">Adopted</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-behavior">Behavior</label>
                            <select name="behavior" id="edit-behavior" required>
                                <option value="">Select Behavior</option>
                                <option value="Calm and Relaxed">Calm and Relaxed</option>
                                <option value="Playful and Energetic">Playful and Energetic</option>
                                <option value="Independent">Independent</option>
                                <option value="Protective">Protective</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-daily_activity">Daily Activity</label>
                            <select name="daily_activity" id="edit-daily_activity" required>
                                <option value="">Select Activity Level</option>
                                <option value="Low">Low</option>
                                <option value="Moderate">Moderate</option>
                                <option value="High">High</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-special_needs">Special Needs</label>
                            <select name="special_needs" id="edit-special_needs" required>
                                <option value="">Select Option</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit-compatibility">Compatibility</label>
                            <select name="compatibility" id="edit-compatibility" required>
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
                        <div class="form-group">
                            <label for="edit-suitable_for">Suitable For</label>
                            <select name="suitable_for" id="edit-suitable_for">
                                <option value="">Select Purpose (Optional)</option>
                                <option value="Family Companion">Family Companion</option>
                                <option value="Emotional Support / Mental Health">Emotional Support / Mental Health</option>
                                <option value="Senior Citizen Companion">Senior Citizen Companion</option>
                            </select>
                        </div>
                        <div class="image-upload">
                            <h3>Pet Images</h3>
                            <div class="image-grid" id="edit-image-grid">
                                <!-- Existing images will be injected here by JS -->
                                <label class="upload-box">
                                    <input type="file" accept="image/*" multiple>
                                    <span>+ Add Photos</span>
                                </label>
                            </div>
                        </div>
                        <div class="modal-actions">
                            <button type="submit" class="btn btn-primary">Save Changes</button>
                            <button type="button" class="btn btn-outline"
                                onclick="closeModal(editModal)">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Add Pet Modal -->
        <div id="addPetModal" class="modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Add New Pet</h2>
                    <button class="close-btn">&times;</button>
                </div>
                <div class="modal-body">
                    <form id="addPetForm" method="POST" action="{{ route('rescuer.pets.create') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="adoption_status" value="available">
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
                            <label for="compatibility">Compatibile with elders</label>
                            <select name="compatibility" id="compatibility">
                                <option value="">Select Option</option>
                                <option value="Yes">Yes</option>
                                <option value="No">No</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="eating_habits">Eating Habits</label>
                            <select name="eating_habits" id="eating_habits" required>
                                <option value="">Select Eating Habits</option>
                                <option value="Balanced Diet">Balanced Diet</option>
                                <option value="Portion Control">Portion Control</option>
                                <option value="Consistent Feeding Schedule">Consistent Feeding Schedule</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="suitable_for">Suitable For</label>
                            <select name="suitable_for" id="suitable_for">
                                <option value="">Select Purpose (Optional)</option>
                                <option value="Family Companion">Family Companion</option>
                                <option value="Emotional Support / Mental Health">Emotional Support / Mental Health</option>
                                <option value="Senior Citizen Companion">Senior Citizen Companion</option>
                            </select>
                        </div>
                        <div class="image-upload">
                            <h3>Pet Images</h3>
                            <div class="image-grid">
                                <label class="upload-box">
                                    <input type="file" accept="image/*" multiple>
                                    <span>+ Add Photos</span>
                                </label>
                            </div>
                        </div>
                        <div class="modal-actions">
                            <button type="submit" class="btn btn-primary">Add Pet</button>
                            <button type="button" class="btn btn-outline" onclick="closeModal(addModal)">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- View Applications Modal -->
        <div id="viewApplicationsModal" class="modal" style="display:none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Applications for <span id="petNameTitle"></span></h2>
                    <button class="close-btn" type="button">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="applications-list">
                        <!-- Applications will be loaded here by JS -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function logout() {
            window.location.href = 'login.html';
        }

        // Modal functionality
        const editModal = document.getElementById('editPetModal');
        const addModal = document.getElementById('addPetModal');
        const closeBtns = document.querySelectorAll('.close-btn');
        const editBtns = document.querySelectorAll('.edit-pet-btn');
        const addPetBtn = document.querySelector('.add-pet-btn');

        function closeModal(modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        document.addEventListener('submit', async (e) => {
            if (e.target.matches('.delete-image-form')) {
                e.preventDefault();

                if (!confirm('Delete this photo?')) return;

                const form = e.target;
                const formData = new FormData(form);

                try {
                    const response = await fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'Accept': 'application/json'
                        }
                    });

                    if (response.ok) {
                        form.closest('.thumbnail-wrapper').remove();
                    } else {
                        alert('Image delete failed.');
                    }
                } catch (err) {
                    console.error(err);
                    alert('Something went wrong.');
                }
            }
        });

        editBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const petId = btn.getAttribute('data-pet-id');
                const name = btn.getAttribute('data-name');
                const species = btn.getAttribute('data-species');
                const breed = btn.getAttribute('data-breed');
                const age = btn.getAttribute('data-age');
                const gender = btn.getAttribute('data-gender');
                const size = btn.getAttribute('data-size') ? btn.getAttribute('data-size').toLowerCase() :
                    '';
                const description = btn.getAttribute('data-description');
                const adoptionStatus = btn.getAttribute('data-adoption_status');
                const behavior = btn.getAttribute('data-behavior');
                const dailyActivity = btn.getAttribute('data-daily_activity');
                const specialNeeds = btn.getAttribute('data-special_needs');
                const compatibility = btn.getAttribute('data-compatibility');
                const eatingHabits = btn.getAttribute('data-eating_habits');
                const suitableFor = btn.getAttribute('data-suitable_for');

                // Display existing images and Delete
                document.getElementById('edit-name').value = name;
                document.getElementById('edit-species').value = species;
                document.getElementById('edit-breed').value = breed;
                document.getElementById('edit-age').value = age;
                document.getElementById('edit-gender').value = gender;
                document.getElementById('edit-size').value = size;
                document.getElementById('edit-description').value = description;
                document.getElementById('edit-adoption_status').value = adoptionStatus;
                document.getElementById('edit-behavior').value = behavior;
                document.getElementById('edit-daily_activity').value = dailyActivity;
                document.getElementById('edit-special_needs').value = specialNeeds;
                document.getElementById('edit-compatibility').value = compatibility;
                document.getElementById('edit-eating_habits').value = eatingHabits;
                if (document.getElementById('edit-suitable_for')) {
                    document.getElementById('edit-suitable_for').value = suitableFor || '';
                }
                document.getElementById('editPetForm').action = `/rescuer/pets/${petId}`;

                // TODO: Display images and delete buttons dynamically if needed

                // Update the form action to the correct pet ID
                const form = document.getElementById('editPetForm');
                form.action = form.action.replace('__PET_ID__', petId);

                editModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
            });
        });

        addPetBtn.addEventListener('click', () => {
            addModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });

        closeBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.modal');
                closeModal(modal);
            });
        });

        window.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                closeModal(e.target);
            }
        });

        // Form submission
        document.getElementById('editPetForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            formData.append('_method', 'PUT');

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
                            location.reload();
                        } else {
                            alert('Error saving changes. Please try again.');
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

            closeModal(editModal);
        });

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
                            location.reload();
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

            closeModal(addModal);
        });

        // Image upload handling
        document.querySelectorAll('.upload-box input').forEach(input => {
            input.addEventListener('change', (e) => {
                const files = e.target.files;
                alert(`${files.length} image(s) selected for upload`);
            });
        });

        // Remove image handling
        document.querySelectorAll('.remove-image').forEach(btn => {
            btn.addEventListener('click', () => {
                btn.parentElement.remove();
            });
        });

        // View Applications functionality
        const viewApplicationsModal = document.getElementById('viewApplicationsModal');
        const viewApplicationsBtns = document.querySelectorAll('.view-applications-btn');

        viewApplicationsBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                const petCard = e.target.closest('.pet-card');
                const petName = petCard.querySelector('.pet-name').textContent;
                document.getElementById('petNameTitle').textContent = petName;
                viewApplicationsModal.style.display = 'block';
                document.body.style.overflow = 'hidden';

                const petId = btn.getAttribute('data-pet-id');
                // Fetch and display applications for the selected pet
                fetch(`/rescuer/pets/${petId}/applications`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        const applicationsList = document.querySelector('.applications-list');
                        applicationsList.innerHTML = '';
                        if (data.applications && data.applications.length > 0) {
                            data.applications.forEach(app => {
                                const applicantName = app.adopter && app.adopter.user ? app.adopter.user.name : 'Unknown';
                                const phone = app.adopter && app.adopter.user ? app.adopter.user.phone || '' : '';
                                const submittedAt = app.submitted_at ? new Date(app.submitted_at).toLocaleDateString() : '';
                                const status = app.status ? app.status.charAt(0).toUpperCase() + app.status.slice(1) : '';
                                const statusClass = app.status ? `status-${app.status.replace(/[^a-zA-Z0-9_-]/g, '').toLowerCase()}` : '';
                                applicationsList.innerHTML += `
                                    <div class="application-item" style="display: flex; align-items: center; justify-content: space-between; background: white; border-radius: 16px; box-shadow: 0 1px 3px rgba(60, 70, 80, 0.1); padding: 1.5rem; margin-bottom: 1rem;">
                                        <div class="application-info">
                                            <h3 style="font-size: 1rem; font-weight: 600; color: #1a1a1a; margin-bottom: 0.25rem;">Application for ${petName}</h3>
                                            <p style="color: #6b7280; margin-bottom: 0.5rem;">From: <span style="font-weight: 500; color: #1a1a1a;">${applicantName}</span>${phone ? ' • Phone: <span style=\"color:#1a1a1a;\">' + phone + '</span>' : ''}</p>
                                            <div class="application-meta" style="font-size: 0.875rem; color: #6b7280;">
                                                Submitted: ${submittedAt} <span class="status-badge ${statusClass}" style="margin-left: 0.5rem;">${status}</span>
                                            </div>
                                        </div>
                                        <div class="action-buttons" style="display: flex; gap: 0.5rem;">
                                            <button class="btn btn-primary" onclick="viewApplicationDetails(${app.application_id})">Review</button>
                                            <button class="btn btn-outline" onclick="messageApplicant('${applicantName}')">Message</button>
                                        </div>
                                    </div>
                                `;
                            });
                        } else {
                            applicationsList.innerHTML = '<div>No applications found for this pet.</div>';
                        }
                    })
                    .catch(error => {
                        const applicationsList = document.querySelector('.applications-list');
                        applicationsList.innerHTML = '<div style="color:red;">Failed to load applications.</div>';
                        console.error('Error fetching applications:', error);
                    });
            });
        });

        function viewApplicationDetails(applicationId) {
            window.location.href = `applications-review.html?id=${applicationId}`;
        }

        function messageApplicant(applicantName) {
            window.location.href = `messages.html?applicant=${encodeURIComponent(applicantName)}`;
        }

        // filtering
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('petSearchInput');
            const statusFilter = document.querySelector('.filter-dropdown');
            const petCards = document.querySelectorAll('.pet-card');

            function filterPets() {
                const search = searchInput.value.trim().toLowerCase();
                const status = statusFilter.value;

                petCards.forEach(card => {
                    const name = (card.getAttribute('data-name') || '').toLowerCase();
                    const breed = (card.getAttribute('data-breed') || '').toLowerCase();
                    const cardStatus = (card.getAttribute('data-status') || '').toLowerCase();

                    const matchesSearch = !search ||
                        name.includes(search) ||
                        breed.includes(search);

                    const matchesStatus = status === 'all' || cardStatus === status;

                    card.style.display = (matchesSearch && matchesStatus) ? '' : 'none';
                });
            }

            searchInput.addEventListener('input', filterPets);
            statusFilter.addEventListener('change', filterPets);
        });
    </script>
@endsection

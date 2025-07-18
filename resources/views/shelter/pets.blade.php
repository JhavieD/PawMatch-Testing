@extends('layouts.pet-management')

@section('title', 'Pet Management')

@section('shelter-content')
    <!-- Top Bar (like Stray Reports) -->
    <div class="main-content">
        <div class="content-wrapper">
            <div class="top-bar">
                <div class="welcome-section">
                    <h1>Pet Management</h1>
                    <p>Manage all pets currently in your shelter</p>
                </div>
                <div class="profile-section">
                    <button class="btn add-pet-btn">
                        <span class="add-icon">&#43;</span>
                        Add New Pet
                    </button>
                </div>
            </div>
            <div class="search-bar">
                <div>
                    <span class="search-icon">
                        <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" />
                            <line x1="16.5" y1="16.5" x2="21" y2="21" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" />
                        </svg>
                    </span>
                    <input type="text" id="petSearchInput" class="search-input"
                        placeholder="Search pets by name, breed, or ID...">
                </div>
                <select class="filter-dropdown">
                    <option value="all">All Status</option>
                    <option value="available">Available</option>
                    <option value="pending">Pending</option>
                    <option value="adopted">Adopted</option>
                </select>
            </div>

            <div class="pets-grid">
                @forelse($pets as $pet)
                    <div class="pet-card" data-name="{{ $pet->name }}" data-breed="{{ $pet->breed }}"
                        data-status="{{ strtolower($pet->adoption_status ?? $pet->status) }}">
                        <img src="{{ $pet->image_url ?? 'https://placehold.co/400x300' }}" alt="{{ $pet->name }}"
                            class="pet-image">
                        <div class="pet-info">
                            <h3 class="pet-name">{{ $pet->name }}</h3>
                            <p class="pet-details">{{ $pet->breed }} • {{ $pet->age }} years old </p>
                            <span class="pet-status status-{{ strtolower($pet->adoption_status) }}">
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
                                    data-suitable_for="{{ $pet->suitable_for }}"
                                    data-medical_history='@json($pet->medical_history ?? [])'>
                                    Edit
                                </button>
                                <button type="button" class="view-applications-btn" data-pet-id="{{ $pet->pet_id }}"
                                    data-pet-name="{{ $pet->name }}">View Applications</button>
                                <form method="POST" action="{{ route('shelter.pets.destroy', $pet->pet_id) }}"
                                    class="delete-pet-form" style="display: contents;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn"
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
                    <form id="editPetForm" method="POST" enctype="multipart/form-data" action="/shelter/pets/__PET_ID__">
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
                                    <option value="">Select Species</option>
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
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="edit-size">Size</label>
                                <select id="edit-size" name="size" required>
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
                                <option value="pending">Pending</option>
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
                                <option value="Emotional Support / Mental Health">Emotional Support / Mental Health
                                </option>
                                <option value="Senior Citizen Companion">Senior Citizen Companion</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="medical_history">Medical Records (ex. PDF,Images) (optional)</label>
                            <!-- Existing medical records links -->
                            <div id="edit-medical-records-list" style="margin-bottom: 8px; color: #6b7280;"></div>
                            <input type="file" id="medical_history" name="medical_history[]"
                                accept="application/pdf,image/*" multiple>
                        </div>

                        <div class="image-upload">
                            <h3>Pet Images</h3>
                            <div class="image-grid" id="edit-image-grid">
                                <div class="image-container">
                                    <label class="upload-box" id="edit-upload-box">
                                        <input type="file" name="images[]" id="edit-images" multiple accept="image/*">
                                        <span>+</span>
                                    </label>
                                    <!-- Existing images will be loaded here -->
                                    <div class="existing-images" id="edit-existing-images"></div>
                                    <!-- Preview container for new images -->
                                    <div class="image-preview-container" id="edit-image-preview"></div>
                                </div>
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
                                <option value="Emotional Support / Mental Health">Emotional Support / Mental Health
                                </option>
                                <option value="Senior Citizen Companion">Senior Citizen Companion</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="medical_history">Medical Records (PDF,Images)</label>
                            <input type="file" id="medical_history" name="medical_history[]"
                                accept="application/pdf,image/*" multiple>
                        </div>

                        <div class="image-upload">
                            <h3>Pet Images</h3>
                            <div class="image-grid">
                                <div class="image-container">
                                    <label class="upload-box" id="add-upload-box">
                                        <input type="file" name="images[]" id="add-images" accept="image/*" multiple>
                                        <span>+</span>
                                    </label>
                                    <!-- Preview container for new images -->
                                    <div class="image-preview-container" id="add-image-preview"></div>
                                </div>
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

        {{-- added pet success alert --}}
        <div id="addSuccessAlert" class="success-alert">
            <span class="success-icon">&#10003;</span>
            <div>
                <div class="success-title">Success</div>
                <div>Added pet successfully</div>
            </div>
            <button id="closeAddSuccessAlert" class="success-close" type="button">&times;</button>
        </div>

        {{-- edit pet successfully --}}
        <div id="editSuccessAlert" class="success-alert edit-success-alert">
            <span class="success-icon">&#9998;</span>
            <div>
                <div class="success-title">Success!</div>
                <div>Pet updated successfully</div>
            </div>
            <button id="closeEditSuccessAlert" class="success-close" type="button">&times;</button>
        </div>
        {{-- Deleted pet --}}
        <div id="deleteSuccessAlert" class="success-alert">
            <span class="success-icon">&#10003;</span>
            <div>
                <div class="success-title">Success</div>
                <div>Pet deleted successfully</div>
            </div>
            <button id="closeDeleteSuccessAlert" class="success-close" type="button">&times;</button>
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
        // Auto reload of Pet Details

        document.addEventListener('DOMContentLoaded', function() {
            const editBtns = document.querySelectorAll('.edit-pet-btn');
            editBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    const petId = btn.getAttribute('data-pet-id');
                    const name = btn.getAttribute('data-name');
                    const species = btn.getAttribute('data-species');
                    const breed = btn.getAttribute('data-breed');
                    const age = btn.getAttribute('data-age');
                    const gender = btn.getAttribute('data-gender');
                    const size = btn.getAttribute('data-size') ? btn.getAttribute('data-size')
                        .toLowerCase() :
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
                    document.getElementById('editPetForm').action = `/shelter/pets/${petId}`;

                    // Load existing images using the new preview system
                    const images = JSON.parse(btn.getAttribute('data-images') || '[]');
                    const formattedImages = images.map(image => ({
                        id: image.id,
                        url: image.image_url
                    }));
                    loadExistingImages(formattedImages);

                    // --- Medical Records Display Logic ---
                    const recordsList = document.getElementById('edit-medical-records-list');
                    const medicalHistory = JSON.parse(btn.getAttribute('data-medical_history') ||
                        '[]');
                    if (recordsList) {
                        recordsList.innerHTML = '';
                        if (Array.isArray(medicalHistory) && medicalHistory.length > 0) {
                            medicalHistory.forEach(record => {
                                if (typeof record === 'object' && record.url && record
                                    .name) {
                                    recordsList.innerHTML +=
                                        `<div><a href="${record.url}" target="_blank">${record.name}</a></div>`;
                                } else if (typeof record === 'string') {
                                    const fileName = record.split('/').pop();
                                    recordsList.innerHTML +=
                                        `<div><a href="${record}" target="_blank">${fileName}</a></div>`;
                                }
                            });
                        } else {
                            recordsList.innerHTML = '<em>No medical records uploaded.</em>';
                        }
                    }
                    // --- End Medical Records Display Logic ---

                    editModal.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                });
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

            // Debug: Log what's being sent
            console.log('Images to delete:', imagesToDelete);
            console.log('Form data entries:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

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
                            closeModal(editModal);
                            sessionStorage.setItem('showEditSuccess', '1');
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
        });

        document.getElementById('addPetForm').addEventListener('submit', (e) => {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            // Debug: log all form data
            for (let [key, value] of formData.entries()) {
                console.log('ADD FORM FIELD:', key, value);
            }

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
                            closeModal(addModal);
                            sessionStorage.setItem('showAddSuccess', '1');
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
        });

        // ===== Toast Notification Utility (with style) =====
        function showToast(message, duration = 2500) {
            let toast = document.createElement('div');
            toast.className = 'custom-toast styled-toast';
            toast.innerHTML = `<span style="margin-right:8px;"></span> ${message}`;
            document.body.appendChild(toast);
            setTimeout(() => {
                toast.classList.add('show');
            }, 10);
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, duration);
        }

        // Add Pet Modal: Show styled toast on image selection
        const addPetImageInput = document.querySelector('#addPetModal input[type="file"][name="images[]"]');
        if (addPetImageInput) {
            addPetImageInput.addEventListener('change', function() {
                if (this.files && this.files.length > 0) {
                    showToast(`${this.files.length} image(s) selected for upload.`);
                }
            });
        }

        // Image upload handling (styled toast, not alert)
        document.querySelectorAll('.upload-box input').forEach(input => {
            input.addEventListener('change', (e) => {
                const files = e.target.files;
                if (files && files.length > 0) {
                    showToast(`${files.length} image(s) selected for upload.`);
                }
            });
        });

        // Toast CSS (inject if not present)
        if (!document.getElementById('custom-toast-style')) {
            const style = document.createElement('style');
            style.id = 'custom-toast-style';
            style.textContent = `
        .custom-toast.styled-toast {
            position: fixed;
            top: 2rem;
            left: 50%;
            transform: translateX(-50%);
            background: #323232;
            color: #fff;
            padding: 1rem 2rem;
            border-radius: 2rem;
            font-size: 1.1rem;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
            opacity: 0;
            pointer-events: none;
            z-index: 9999;
            transition: opacity 0.3s, top 0.3s;
            display: flex;
            align-items: center;
        }
        .custom-toast.styled-toast.show {
            opacity: 1;
            top: 3.5rem;
        }
    `;
            document.head.appendChild(style);
        }
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
                fetch(`/shelter/pets/${petId}/applications`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        const applicationsList = document.querySelector('.applications-list');
                        applicationsList.innerHTML = '';
                        // Defensive: check for data.applications as array
                        if (Array.isArray(data.applications) && data.applications.length > 0) {
                            data.applications.forEach(app => {
                                let applicantName = app.applicant_name || 'Unknown';
                                let phone = app.phone || '';
                                const submittedAt = app.submitted_at ? new Date(app
                                    .submitted_at).toLocaleDateString() : '';
                                const status = app.status ? app.status.charAt(0).toUpperCase() +
                                    app.status.slice(1) : '';
                                const statusClass = app.status ?
                                    `status-${app.status.replace(/[^a-zA-Z0-9_-]/g, '').toLowerCase()}` :
                                    '';
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
                                            <button class="btn btn-outline" onclick="messageApplicant('${app.user_id || app.applicant_id || (app.user && app.user.id) || ''}')">Message</button>
                                        </div>
                                    </div>
                                `;
                                console.log('Application object:', app); // Debug log
                            });
                        } else {
                            applicationsList.innerHTML =
                                '<div>No applications found for this pet.</div>';
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching applications:', error);
                    });
            });
        });

        function viewApplicationDetails(applicationId) {
            // Redirect to the correct Laravel route for application review
            window.location.href = `/shelter/applications/${applicationId}/review`;
        }

        function messageApplicant(userId) {
            // Redirect to the correct Laravel route for messaging with a specific user
            if (userId) {
                window.location.href = `/shelter/messages?receiver_id=${userId}`;
            } else {
                alert('No applicant user ID found.');
            }
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


            // add pet alert

            document.addEventListener('DOMContentLoaded', function() {
                const addSuccessAlert = document.getElementById('addSuccessAlert');
                const closeAddSuccessAlert = document.getElementById('closeAddSuccessAlert');

                if (addSuccessAlert && closeAddSuccessAlert) {
                    closeAddSuccessAlert.addEventListener('click', function() {
                        addSuccessAlert.style.display = 'none';
                        location.reload();
                    });
                }
            });
        });

        // --- Success Alert Persistence ---
        function showSuccessAlert(type) {
            let alertEl = null;
            if (type === 'add') {
                alertEl = document.getElementById('addSuccessAlert');
            } else if (type === 'edit') {
                alertEl = document.getElementById('editSuccessAlert');
            } else if (type === 'delete') {
                alertEl = document.getElementById('deleteSuccessAlert');
            }
            if (alertEl) {
                alertEl.classList.add('active');
                // Auto close after 2 seconds with fade out
                setTimeout(() => {
                    alertEl.classList.remove('active');
                }, 2000);
            }
        }

        // On page load, check for alert flag
        window.addEventListener('DOMContentLoaded', function() {
            if (sessionStorage.getItem('showAddSuccess')) {
                showSuccessAlert('add');
                sessionStorage.removeItem('showAddSuccess');
            }
            if (sessionStorage.getItem('showEditSuccess')) {
                showSuccessAlert('edit');
                sessionStorage.removeItem('showEditSuccess');
            }
            if (sessionStorage.getItem('showDeleteSuccess')) {
                showSuccessAlert('delete');
                sessionStorage.removeItem('showDeleteSuccess');
            }
        });

        // Success alert close buttons (no reload)
        document.getElementById('closeAddSuccessAlert').addEventListener('click', function() {
            document.getElementById('addSuccessAlert').classList.remove('active');
        });
        document.getElementById('closeEditSuccessAlert').addEventListener('click', function() {
            document.getElementById('editSuccessAlert').classList.remove('active');
        });
        document.getElementById('closeDeleteSuccessAlert').addEventListener('click', function() {
            document.getElementById('deleteSuccessAlert').classList.remove('active');
        });

        // --- FIXED: Delete Pet Handler (single, clean version) ---
        document.querySelectorAll('.delete-pet-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                fetch(form.action, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': form.querySelector('input[name="_token"]').value,
                            'Accept': 'application/json'
                        }
                    })
                    .then(async response => {
                        if (response.ok) {
                            const data = await response.json();
                            if (data.success) {
                                sessionStorage.setItem('showDeleteSuccess', '1');
                                location.reload();
                            } else {
                                alert('Error deleting pet. Please try again.');
                            }
                        } else {
                            alert('Error deleting pet. Please try again.');
                        }
                    })
                    .catch(error => {
                        console.error('Delete error:', error);
                        alert('Error deleting pet. Please try again.');
                    });
            });
        });

        // Image Preview Functionality
        let addSelectedFiles = [];
        let editSelectedFiles = [];
        let imagesToDelete = [];

        // Setup image preview for Add Pet modal
        document.getElementById('add-images').addEventListener('change', function(e) {
            handleImagePreview(e.target.files, 'add');
        });

        // Setup image preview for Edit Pet modal
        document.getElementById('edit-images').addEventListener('change', function(e) {
            handleImagePreview(e.target.files, 'edit');
        });

        function handleImagePreview(files, modalType) {
            const filesArray = Array.from(files);
            const container = document.getElementById(`${modalType}-image-preview`);
            
            if (modalType === 'add') {
                addSelectedFiles = addSelectedFiles.concat(filesArray);
            } else {
                editSelectedFiles = editSelectedFiles.concat(filesArray);
            }
            
            displayImagePreviews(modalType);
        }

        function displayImagePreviews(modalType) {
            const container = document.getElementById(`${modalType}-image-preview`);
            const selectedFiles = modalType === 'add' ? addSelectedFiles : editSelectedFiles;
            
            container.innerHTML = '';
            
            selectedFiles.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'image-preview-item';
                        previewItem.innerHTML = `
                            <img src="${e.target.result}" alt="Preview">
                            <button type="button" class="image-remove-btn" onclick="removeImagePreview(${index}, '${modalType}')">
                                ×
                            </button>
                        `;
                        container.appendChild(previewItem);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }

        function removeImagePreview(index, modalType) {
            if (modalType === 'add') {
                addSelectedFiles.splice(index, 1);
            } else {
                editSelectedFiles.splice(index, 1);
            }
            displayImagePreviews(modalType);
            updateFileInput(modalType);
        }
        window.removeImagePreview = removeImagePreview;

        function updateFileInput(modalType) {
            const fileInput = document.getElementById(`${modalType}-images`);
            const selectedFiles = modalType === 'add' ? addSelectedFiles : editSelectedFiles;
            
            // Create a new DataTransfer object to update the file input
            const dt = new DataTransfer();
            selectedFiles.forEach(file => dt.items.add(file));
            fileInput.files = dt.files;
        }

        // Clear previews when modals are closed
        function clearImagePreviews(modalType) {
            if (modalType === 'add') {
                addSelectedFiles = [];
                document.getElementById('add-image-preview').innerHTML = '';
                document.getElementById('add-images').value = '';
            } else {
                editSelectedFiles = [];
                imagesToDelete = [];
                document.getElementById('edit-image-preview').innerHTML = '';
                document.getElementById('edit-existing-images').innerHTML = '';
                document.getElementById('edit-images').value = '';
                // Remove any existing hidden deletion inputs
                const existingDeleteInputs = document.querySelectorAll('input[name="images_to_delete[]"]');
                existingDeleteInputs.forEach(input => input.remove());
            }
        }

        // Load existing images for edit modal
        function loadExistingImages(petImages) {
            const container = document.getElementById('edit-existing-images');
            container.innerHTML = '';
            
            if (petImages && petImages.length > 0) {
                petImages.forEach((image, index) => {
                    const imageItem = document.createElement('div');
                    imageItem.className = 'existing-image-item';
                    imageItem.innerHTML = `
                        <img src="${image.url}" alt="Pet Image">
                        <button type="button" class="existing-image-remove-btn" onclick="markImageForDeletion(${image.id}, ${index})">
                            ×
                        </button>
                        <input type="hidden" name="existing_images[]" value="${image.id}">
                    `;
                    container.appendChild(imageItem);
                });
            }
        }

        function markImageForDeletion(imageId, index) {
            imagesToDelete.push(imageId);
            const imageItems = document.querySelectorAll('.existing-image-item');
            if (imageItems[index]) {
                imageItems[index].remove();
            }
            
            // Add hidden input to mark image for deletion
            const deleteInput = document.createElement('input');
            deleteInput.type = 'hidden';
            deleteInput.name = 'images_to_delete[]';
            deleteInput.value = imageId;
            document.getElementById('editPetForm').appendChild(deleteInput);
        }
        window.markImageForDeletion = markImageForDeletion;

        // Override the existing modal close functions to clear previews
        const originalCloseModal = window.closeModal;
        window.closeModal = function(modal) {
            if (modal && modal.id === 'addPetModal') {
                clearImagePreviews('add');
            } else if (modal && modal.id === 'editPetModal') {
                clearImagePreviews('edit');
            }
            originalCloseModal(modal);
        };

    </script>
@endsection

@extends ('layouts.pet-management')

@section('title', 'Pet Management')

@section('shelter-content')
<div class="container" style="margin-left: 250px;">
    <div class="header">
        <h1>Pet Management</h1>
        <button class="btn add-pet-btn">+ Add New Pet</button>
    </div>

    <div class="search-bar">
        <input type="text" class="search-input" placeholder="Search pets by name, breed, or ID...">
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
        <div class="pet-card">
            <img src="{{ $pet->image_url ?? 'https://placehold.co/400x300' }}" alt="{{ $pet->name }}" class="pet-image">
            <div class="pet-info">
                <h3 class="pet-name">{{ $pet->name }}</h3>
                <p class="pet-details">{{ $pet->breed }} • {{ $pet->age }} years old </p>
                <span class="pet-status status-{{ $pet->adoption_status }}">
                    {{ ucfirst($pet->adoption_status) }}
                </span>
                <div class="card-actions">
                    <button type="button" class="edit-pet-btn" data-pet-id="{{ $pet->pet_id }}"
                        data-name="{{ $pet->name }}"
                        data-type="{{ $pet->type }}"
                        data-breed="{{ $pet->breed }}"
                        data-age="{{ $pet->age }}"
                        data-gender="{{ $pet->gender }}"
                        data-size="{{ $pet->size }}"
                        data-species="{{ $pet->species }}"
                        data-description="{{ $pet->description }}"
                        data-adoption_status="{{ $pet->adoption_status }}">
                        Edit
                    </button>
                    <button type="button" class="view-applications-btn" data-pet-id="{{ $pet->id }}" data-pet-name="{{ $pet->name }}">View Applications</button>
                    <form method="POST" action="{{ route('shelter.pets.destroy', $pet->pet_id) }}" style="display: contents;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn" onclick="return confirm('Are you sure you want to delete this pet?')" style="justify-content: center;">Delete</button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div>No pets found.</div>
        @endforelse
    </div>

    <!-- <div class="pet-card">
            <img src="https://scontent.fmnl17-4.fna.fbcdn.net/v/t1.15752-9/483141889_1675833660025267_1290389856524842791_n.jpg?_nc_cat=105&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeFiU2l-lMqiT0yOzqHvaKcBjpUqFd79mTaOlSoV3v2ZNlyoGX5LvyV0WGq8B-1ou-da8iwMWIqMvLc6zVV7ocSO&_nc_ohc=Ig-bidMrjgAQ7kNvgFbwmsH&_nc_oc=AdiWsP5H0Z3Fod7YR9kmeQoIz8fr778iAnJLBmNhxdsqELJJRYvz2uIdxR48AbN7Vkw&_nc_zt=23&_nc_ht=scontent.fmnl17-4.fna&oh=03_Q7cD1wGU_rMD2FIgwYa-HG-mjZwjOaYCt4_tmj0bfU_3bldL1A&oe=67F7BCA9" alt="Fort" class="pet-image">
            <div class="pet-info">
                <h3 class="pet-name">Fort</h3>
                <p class="pet-details">Belgian Cat • 1 year old</p>
                <span class="pet-status status-pending">Application Pending</span>
                <div class="card-actions">
                    <button>Edit</button>
                    <button>View Applications</button>
                    <button>Delete</button>
                </div>
            </div>
        </div>

        <div class="pet-card">
            <img src="https://scontent.fmnl17-2.fna.fbcdn.net/v/t1.15752-9/482640700_627447673475554_6154494766157000980_n.jpg?_nc_cat=107&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeHwxEiY35eQJpJbi1AievedO8DosPsyGwY7wOiw-zIbBhsQ6fFsfo-g-0r22I7cxnXopUSrX2reZo4RYNdjDJHu&_nc_ohc=ciEvSNmnJ3MQ7kNvgGikfsF&_nc_oc=Adgy6d2x88wvmtbCvuhM-0IpHAYtnTjUQIMdpv54ogffEumEEEtWJctAE76Iw1Gwjy8&_nc_zt=23&_nc_ht=scontent.fmnl17-2.fna&oh=03_Q7cD1wGnvieAKeYEanduwoXtD_JDDbrT6dgOzNCLApF17iq-GQ&oe=67F7D0E7" alt="Rocky" class="pet-image">
            <div class="pet-info">
                <h3 class="pet-name">Rocky</h3>
                <p class="pet-details">German Shepherd • 3 years old</p>
                <span class="pet-status status-adopted">Adopted</span>
                <div class="card-actions">
                    <button>Edit</button>
                    <button>View Applications</button>
                    <button>Delete</button>
                </div>
            </div>
        </div> -->
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
            <form id="editPetForm" method="POST" action="/shelter/pets/__PET_ID__">
                @csrf
                @method('PUT')
                <div class="form-grid">
                    <div class="form-group">
                        <label for="edit-name">Pet Name</label>
                        <input type="text" name="name" id="edit-name">
                    </div>
                    <div class="form-group">
                        <label for="edit-type">Type</label>
                        <select id="edit-type" name="type" required>
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
                    <label for="edit-species">Species</label>
                    <select id="edit-species" name="species" required>
                        <option value="">Select Species</option>
                        <option value="canine">Canine</option>
                        <option value="feline">Feline</option>
                        <option value="avian">Avian</option>
                        <option value="rodent">Rodent</option>
                        <option value="reptile">Reptile</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="edit-description">Description</label>
                    <textarea id="edit-description" name="description" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label for="adoption-status">Status</label>
                    <select id="edit-adoption_status" name="adoption_status" required>
                        <option value="available">Available</option>
                        <option value="pending">Application Pending</option>
                        <option value="adopted">Adopted</option>
                    </select>
                </div>

                <div class="image-upload">
                    <h3>Pet Images</h3>
                    <div class="image-grid">
                        <div class="image-item">
                            <img src="https://scontent.fmnl17-1.fna.fbcdn.net/v/t1.15752-9/482958427_546890997878558_7939868464340469320_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEg26tmyKvJjnwIVGA4p7VpyQUyRIOYwdHJBTJEg5jB0Wx4wKGdnDZxTVL1IbZ-XSjbaTHznmX8rfBWnCPmJmMd&_nc_ohc=Z4p-MgCZnLUQ7kNvgEaxlgm&_nc_oc=Adj34yEQkoq4BVC1rbv3hqxuOEPtGTzHnNq_bsWvPqyo8vgZ9z2tbbWgJ7HSzT1tE3w&_nc_zt=23&_nc_ht=scontent.fmnl17-1.fna&oh=03_Q7cD1wGK65XEYIiIyewAECv3ua60cvr7cDuW6l98OwR5gTpdRw&oe=67F7AA9D" alt="Pet photo">
                            <button type="button" class="remove-image">&times;</button>
                        </div>
                        <div class="image-item">
                            <img src="https://scontent.fmnl17-2.fna.fbcdn.net/v/t1.15752-9/481596592_1188427862939538_5723652600303824613_n.jpg?_nc_cat=107&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeGBLyrExbLdDdTzASuJF6NziczxXHFDeI2JzPFccUN4jbX8-GWmxshexj8fLTOn8MEB0l1mnBiqSAj0oKU2VMTQ&_nc_ohc=4l6zYonHqqgQ7kNvgGPA7TT&_nc_oc=Adi0zMNxPg4e893EM5wgYGH2M358oifh1ipsMnTcQcry2B4YiZ0xDmV4yy8Wy3cKQGE&_nc_zt=23&_nc_ht=scontent.fmnl17-2.fna&oh=03_Q7cD1wGu2m2aE4YaCrNXfc-CQFEhi7ZLq-lP2OaIJdyeJ9tOvA&oe=67F7B056" alt="Pet photo">
                            <button type="button" class="remove-image">&times;</button>
                        </div>
                        <label class="upload-box">
                            <input type="file" accept="image/*" multiple>
                            <span>+ Add Photos</span>
                        </label>
                    </div>
                </div>

                <div class="modal-actions">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <button type="button" class="btn btn-outline" onclick="closeModal(editModal)">Cancel</button>
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
            <form id="addPetForm" method="POST" action="{{ route('shelter.pets.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="form-grid">
                    <div class="form-group">
                        <label for="name">Pet Name</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="type">Type</label>
                        <select id="type" name="type" required>
                            <option value="">Select Type</option>
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
                    <label for="species">Species</label>
                    <select id="species" name="species" required>
                        <option value="">Select Species</option>
                        <option value="canine">Canine</option>
                        <option value="feline">Feline</option>
                        <option value="avian">Avian</option>
                        <option value="rodent">Rodent</option>
                        <option value="reptile">Reptile</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label for="adoption_status">Status</label>
                    <select id="adoption_status" name="adoption_status" required>
                        <option value="">Select Status</option>
                        <option value="available">Available</option>
                        <option value="pending">Application Pending</option>
                        <option value="adopted">Adopted</option>
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
                    <button type="button" class="btn btn-outline" onclick="closeModal()">Cancel</button>
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

@if($showApplicationsModal)
@include('shelter.partials.applications-modal', ['pet' => $selectedPet])
@endif

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
            // Reset edit form action if closing edit modal
            if (modal === editModal) {
                const editForm = document.getElementById('editPetForm');
                editForm.action = '/shelter/pets/__PET_ID__';
                // Optionally clear fields if you want
                // editForm.reset();
                // Disable save button
                editForm.querySelector('button[type="submit"]').disabled = true;
            }
        }

        editBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                editModal.style.display = 'block';
                document.body.style.overflow = 'hidden';
                const petId = btn.getAttribute('data-pet-id');
                const editForm = document.getElementById('editPetForm');
                editForm.action = `/shelter/pets/${petId}`;
                console.log('Edit button clicked, form action set to:', editForm.action);
                // Enable save button
                editForm.querySelector('button[type="submit"]').disabled = false;
                // Pre-fill all fields in the edit modal
                document.getElementById('edit-name').value = btn.dataset.name || '';
                document.getElementById('edit-type').value = btn.dataset.type || '';
                document.getElementById('edit-breed').value = btn.dataset.breed || '';
                document.getElementById('edit-age').value = btn.dataset.age || '';
                document.getElementById('edit-gender').value = btn.dataset.gender || '';
                document.getElementById('edit-size').value = btn.dataset.size || '';
                document.getElementById('edit-species').value = btn.dataset.species || '';
                document.getElementById('edit-description').value = btn.dataset.description || '';
                document.getElementById('edit-adoption_status').value = btn.dataset.adoption_status || '';
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

        // Form submission this is only alert page it and it doesn't submit to the backend
        // document.getElementById('editPetForm').addEventListener('submit', (e) => {
        //     e.preventDefault();
        //     alert('Changes saved successfully!');
        //     closeModal(editModal);
        // });

        // document.getElementById('addPetForm').addEventListener('submit', (e) => {
        //     e.preventDefault();
        //     alert('Pet added successfully!');
        //     closeModal(addModal);
        // });

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

        function viewApplicationDetails(applicationId) {
            // Redirect to the application details page
            window.location.href = `applications-review.html?id=${applicationId}`;
        }

        //close applications modal
        document.querySelector('#viewApplicationsModal .close-btn').addEventListener('click', () => {
            viewApplicationsModal.style.display = 'none';
            document.body.style.overflow = 'auto';
        });

        function messageApplicant(applicantName) {
            // Redirect to messages with the applicant
            window.location.href = `messages.html?applicant=${encodeURIComponent(applicantName)}`;
        }

        document.getElementById('editPetForm').addEventListener('submit', function(e) {
            const action = this.action;
            console.log('Submitting form, action is:', action);
            // Check if the action ends with a number (pet ID)
            if (!/\/shelter\/pets\/\d+$/.test(action)) {
                e.preventDefault();
                alert('Pet ID is missing in the form action. Please use the Edit button to open the modal.');
                // Disable save button to prevent further attempts
                this.querySelector('button[type="submit"]').disabled = true;
            }
        });

        // Attach event listeners for View Applications buttons
        document.querySelectorAll('.view-applications-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                showApplications(this.getAttribute('data-pet-id'), this.getAttribute('data-pet-name'));
            });
        });
    });
</script>

<script>
    function escapeHtml(text) {
        if (!text) return '';
        return text.replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#39;');
    }

    function showApplications(petId, petName) {
        document.getElementById('petNameTitle').textContent = petName;
        fetch(`/shelter/pets/${petId}/applications`)
            .then(response => response.json())
            .then(applications => {
                let html = '';
                if (!applications || applications.length === 0) {
                    html = `<p>No recent applications for <strong>${petName}</strong>.</p>`;
                } else {
                    applications.forEach(app => {
                        const appName = app?.adopter?.user?.name || '';
                        const appStatus = app?.status || '';
                        const appStatusClass = appStatus.toLowerCase();
                        const appCreated = app?.created_at ? new Date(app.created_at).toLocaleDateString() : '';
                        html += `
<div class="application-item">
    <div class="applicant-info">
        <h3>${appName}</h3>
        <p>Submitted: ${appCreated}</p>
        <span class="status-badge status-${appStatusClass}">${appStatus}</span>
    </div>
    <div class="btn-group">
        <button class="btn btn-primary view-details-btn" data-id="${app.id}">View Details</button>
        <button class="btn btn-outline message-applicant-btn" data-applicant="${appName}">Message</button>
    </div>
</div>
`;
                    });
                }
                document.querySelector('#viewApplicationsModal .applications-list').innerHTML = html;
                document.getElementById('viewApplicationsModal').style.display = 'block';
                document.body.style.overflow = 'hidden';

                // Attach event listeners for the new buttons
                document.querySelectorAll('.view-details-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const id = this.getAttribute('data-id');
                        viewApplicationDetails(id);
                    });
                });
                document.querySelectorAll('.message-applicant-btn').forEach(btn => {
                    btn.addEventListener('click', function() {
                        const name = this.getAttribute('data-applicant');
                        messageApplicant(name);
                    });
                });
            })
            .catch(error => {
                document.querySelector('#viewApplicationsModal .applications-list').innerHTML = '<p>No loading applications.</p>';
                document.getElementById('viewApplicationsModal').style.display = 'block';
                document.body.style.overflow = 'hidden';
                console.error('Error fetching applications:', error);
            });
    }
</script>
@endsection
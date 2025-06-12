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

    <div class="pets-grid">
        <!-- Sample Pet Cards -->
        <div class="pet-card">
            <img src="https://scontent.fmnl17-1.fna.fbcdn.net/v/t1.15752-9/482958427_546890997878558_7939868464340469320_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEg26tmyKvJjnwIVGA4p7VpyQUyRIOYwdHJBTJEg5jB0Wx4wKGdnDZxTVL1IbZ-XSjbaTHznmX8rfBWnCPmJmMd&_nc_ohc=Z4p-MgCZnLUQ7kNvgEaxlgm&_nc_oc=Adj34yEQkoq4BVC1rbv3hqxuOEPtGTzHnNq_bsWvPqyo8vgZ9z2tbbWgJ7HSzT1tE3w&_nc_zt=23&_nc_ht=scontent.fmnl17-1.fna&oh=03_Q7cD1wGK65XEYIiIyewAECv3ua60cvr7cDuW6l98OwR5gTpdRw&oe=67F7AA9D" alt="Ester" class="pet-image">
            <div class="pet-info">
                <h3 class="pet-name">Ester</h3>
                <p class="pet-details">Tabby Cat • 2 years old</p>
                <span class="pet-status status-available">Available</span>
                <div class="card-actions">
                    <button>Edit</button>
                    <button>View Applications</button>
                    <button>Delete</button>
                </div>
            </div>
        </div>

        <div class="pet-card">
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
        </div>
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
            <form id="editPetForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="petName">Pet Name</label>
                        <input type="text" id="petName" name="petName" value="Ester">
                    </div>
                    <div class="form-group">
                        <label for="petType">Type</label>
                        <select id="petType" name="petType">
                            <option value="dog">Dog</option>
                            <option value="cat">Cat</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="breed">Breed</label>
                        <input type="text" id="breed" name="breed" value="Tabby Cat">
                    </div>
                    <div class="form-group">
                        <label for="age">Age</label>
                        <input type="number" id="age" name="age" value="2">
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender</label>
                        <select id="gender" name="gender">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="size">Size</label>
                        <select id="size" name="size">
                            <option value="small">Small</option>
                            <option value="medium">Medium</option>
                            <option value="large">Large</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4">Friendly and energetic Tabby Cat looking for a loving home.</textarea>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status">
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
                    <button type="button" class="btn btn-outline" onclick="closeModal(addModal)">Cancel</button>
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
            <form id="addPetForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="petName">Pet Name</label>
                        <input type="text" id="petName" name="petName" required>
                    </div>
                    <div class="form-group">
                        <label for="petType">Type</label>
                        <select id="petType" name="petType" required>
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
                    <label for="description">Description</label>
                    <textarea id="description" name="description" rows="4" required></textarea>
                </div>

                <div class="form-group">
                    <label for="status">Status</label>
                    <select id="status" name="status" required>
                        <option value="">Select Status</option>
                        <option value="available">Available</option>
                        <option value="pending">Application Pending</option>
                        <option value="adopted">Adopted</option>
                    </select>
                </div>

                <div class="image-upload">
                    <h3>Pet Images</h3>
                    <div class="image-grid">
                        <div class="image-item">
                            <img src="https://placehold.co/400x300" alt="Pet photo">
                            <button type="button" class="remove-image">&times;</button>
                        </div>
                        <div class="image-item">
                            <img src="https://placehold.co/400x300" alt="Pet photo">
                            <button type="button" class="remove-image">&times;</button>
                        </div>
                        <label class="upload-box">
                            <input type="file" accept="image/*" multiple>
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
<div id="viewApplicationsModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Applications for <span id="petNameTitle">Ester</span></h2>
            <button class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
            <div class="applications-list">
                <div class="application-item">
                    <div class="applicant-info">
                        <h3>Jan Vincent Dominguez</h3>
                        <p>Submitted: March 15, 2024</p>
                        <span class="status-badge status-pending">Pending Review</span>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-primary" onclick="viewApplicationDetails(1)">View Details</button>
                        <button class="btn btn-outline" onclick="messageApplicant('Jan Vincent Dominguez')">Message</button>
                    </div>
                </div>

                <div class="application-item">
                    <div class="applicant-info">
                        <h3>Allainne Villanueva</h3>
                        <p>Submitted: March 14, 2024</p>
                        <span class="status-badge status-approved">Approved</span>
                    </div>
                    <div class="btn-group">
                        <button class="btn btn-primary" onclick="viewApplicationDetails(2)">View Details</button>
                        <button class="btn btn-outline" onclick="messageApplicant('Allainne Villanueva')">Message</button>
                    </div>
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
    const editBtns = document.querySelectorAll('.card-actions button:first-child');
    const addPetBtn = document.querySelector('.add-pet-btn');

    function closeModal(modal) {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    editBtns.forEach(btn => {
        btn.addEventListener('click', () => {
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
        alert('Changes saved successfully!');
        closeModal(editModal);
    });

    document.getElementById('addPetForm').addEventListener('submit', (e) => {
        e.preventDefault();
        alert('Pet added successfully!');
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
    const viewApplicationsBtns = document.querySelectorAll('.card-actions button:nth-child(2)');

    viewApplicationsBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            const petCard = e.target.closest('.pet-card');
            const petName = petCard.querySelector('.pet-name').textContent;
            document.getElementById('petNameTitle').textContent = petName;
            viewApplicationsModal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
    });

    function viewApplicationDetails(applicationId) {
        // Redirect to the application details page
        window.location.href = `applications-review.html?id=${applicationId}`;
    }

    function messageApplicant(applicantName) {
        // Redirect to messages with the applicant
        window.location.href = `messages.html?applicant=${encodeURIComponent(applicantName)}`;
    }
</script>
@endsection
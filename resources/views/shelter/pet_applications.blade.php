@extends ('layouts.applications-review')

@section('title', 'Applications')

@section('shelter-content')
<div class="container">
    <div class="header">
        <h1>Adoption Applications</h1>
    </div>

    <div class="search-bar">
        <input type="text" class="search-input" placeholder="Search by applicant name or pet name...">
        <select class="filter-dropdown">
            <option value="all">All Status</option>
            <option value="pending">Pending Review</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    <div class="applications-list">
        <!-- Sample Application Items -->
        <div class="application-item">
            <img src="https://scontent.fmnl17-1.fna.fbcdn.net/v/t1.15752-9/482958427_546890997878558_7939868464340469320_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEg26tmyKvJjnwIVGA4p7VpyQUyRIOYwdHJBTJEg5jB0Wx4wKGdnDZxTVL1IbZ-XSjbaTHznmX8rfBWnCPmJmMd&_nc_ohc=Z4p-MgCZnLUQ7kNvgEaxlgm&_nc_oc=Adj34yEQkoq4BVC1rbv3hqxuOEPtGTzHnNq_bsWvPqyo8vgZ9z2tbbWgJ7HSzT1tE3w&_nc_zt=23&_nc_ht=scontent.fmnl17-1.fna&oh=03_Q7cD1wGK65XEYIiIyewAECv3ua60cvr7cDuW6l98OwR5gTpdRw&oe=67F7AA9D" alt="Ester" class="pet-image">
            <div class="application-info">
                <h3>Application for Ester</h3>
                <p>From: Jan Vincent Dominguez • Phone: 0912-345-6789</p>
                <div class="application-meta">
                    <span>Submitted: March 15, 2024</span>
                    <span class="status-badge status-pending">Pending Review</span>
                </div>
            </div>
            <div class="action-buttons">
                <button class="btn btn-primary">Review</button>
                <button onclick="messageApplicant('Jan Vincent Dominguez')" class="btn btn-outline">Message</button>
            </div>
        </div>

        <div class="application-item">
            <img src="https://scontent.fmnl17-1.fna.fbcdn.net/v/t1.15752-9/482487994_519954501125001_2078025813899306849_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEeZjpdAK_M-yf5h5IvWdXQUR3Ko2en25tRHcqjZ6fbm_KRvQPSzX18tPL-2ParloJBk9AHSq4CfWq5m8dMHjOZ&_nc_ohc=bZ16vY0c35YQ7kNvgGtgcCE&_nc_oc=AdhzMhPeljm5uTxIQuorDQdXH2IQFxYfI27IX1SynAkecUVIN-tpoisUk_G_BIkQA-U&_nc_zt=23&_nc_ht=scontent.fmnl17-1.fna&oh=03_Q7cD1wEaT8XJlz7gNg-5YTKvcnQf3ePKvmU6Pe0I0jDrb0IO1w&oe=67F7B9B4" alt="Fort" class="pet-image">
            <div class="application-info">
                <h3>Application for Fort</h3>
                <p>From: Allainne Villanueva • Phone: 0912-456-345</p>
                <div class="application-meta">
                    <span>Submitted: March 14, 2024</span>
                    <span class="status-badge status-approved">Approved</span>
                </div>
            </div>
            <div class="action-buttons">
                <button class="btn btn-primary">View Details</button>
                <button class="btn btn-outline">Message</button>
            </div>
        </div>

        <div class="application-item">
            <img src="https://scontent.fmnl17-2.fna.fbcdn.net/v/t1.15752-9/482640700_627447673475554_6154494766157000980_n.jpg?_nc_cat=107&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeHwxEiY35eQJpJbi1AievedO8DosPsyGwY7wOiw-zIbBhsQ6fFsfo-g-0r22I7cxnXopUSrX2reZo4RYNdjDJHu&_nc_ohc=ciEvSNmnJ3MQ7kNvgGikfsF&_nc_oc=Adgy6d2x88wvmtbCvuhM-0IpHAYtnTjUQIMdpv54ogffEumEEEtWJctAE76Iw1Gwjy8&_nc_zt=23&_nc_ht=scontent.fmnl17-2.fna&oh=03_Q7cD1wGnvieAKeYEanduwoXtD_JDDbrT6dgOzNCLApF17iq-GQ&oe=67F7D0E7" alt="Rocky" class="pet-image">
            <div class="application-info">
                <h3>Application for Rocky</h3>
                <p>From: Vince Rubio • Phone: 0912-456-3452</p>
                <div class="application-meta">
                    <span>Submitted: March 13, 2024</span>
                    <span class="status-badge status-rejected">Rejected</span>
                </div>
            </div>
            <div class="action-buttons">
                <button class="btn btn-primary">View Details</button>
                <button class="btn btn-outline">Message</button>
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
        <div class="modal-body">
            <div class="applicant-info">
                <h3>Applicant Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Full Name</label>
                        <p id="applicantName">Jan Vincent Dominguez</p>
                    </div>
                    <div class="info-item">
                        <label>Phone</label>
                        <p id="applicantPhone">0912-345-6789</p>
                    </div>
                    <div class="info-item">
                        <label>Email</label>
                        <p id="applicantEmail">jan@gmail.com</p>
                    </div>
                    <div class="info-item">
                        <label>Address</label>
                        <p id="applicantAddress">Guadalupe, Makati City</p>
                    </div>
                </div>
            </div>

            <div class="application-details">
                <h3>Application Details</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Pet Name</label>
                        <p id="petName">Ester</p>
                    </div>
                    <div class="info-item">
                        <label>Submission Date</label>
                        <p id="submissionDate">March 15, 2024</p>
                    </div>
                    <div class="info-item">
                        <label>Status</label>
                        <p id="applicationStatus">Pending Review</p>
                    </div>
                </div>

                <div class="questionnaire">
                    <h4>Questionnaire Responses</h4>
                    <div class="question">
                        <label>Why do you want to adopt this pet?</label>
                        <p>I've always wanted a Tabby Cat and feel ready to provide a loving home.</p>
                    </div>
                    <div class="question">
                        <label>Do you have experience with pets?</label>
                        <p>Yes, I grew up with dogs and currently have a cat.</p>
                    </div>
                    <div class="question">
                        <label>Living situation</label>
                        <p>I own a house with a fenced backyard.</p>
                    </div>
                </div>
            </div>

            <div class="modal-actions">
                <button class="btn btn-primary" id="approveBtn">Approve Application</button>
                <button class="btn btn-outline" id="rejectBtn">Reject Application</button>
                <button class="btn btn-outline" id="requestInfoBtn">Request More Info</button>
            </div>
        </div>
    </div>
</div>

<script>
    function logout() {
        window.location.href = 'login.html';
    }

    // Modal functionality
    const modal = document.getElementById('applicationModal');
    const closeBtn = document.querySelector('.close-btn');
    const reviewBtns = document.querySelectorAll('.btn-primary');

    reviewBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
        });
    });

    function messageApplicant(applicantName) {
        // Redirect to messages with the applicant
        window.location.href = `messages.html?applicant=${encodeURIComponent(applicantName)}`;
    }

    closeBtn.addEventListener('click', () => {
        modal.style.display = 'none';
        document.body.style.overflow = 'auto';
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            document.body.style.overflow = 'auto';
        }
    });

    // Sample action handlers
    document.getElementById('approveBtn').addEventListener('click', () => {
        alert('Application approved!');
        modal.style.display = 'none';
    });

    document.getElementById('rejectBtn').addEventListener('click', () => {
        alert('Application rejected!');
        modal.style.display = 'none';
    });

    document.getElementById('requestInfoBtn').addEventListener('click', () => {
        alert('Information request sent to applicant!');
        modal.style.display = 'none';
    });
</script>
@endsection
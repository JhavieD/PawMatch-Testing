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
        @foreach ($applications as $application)
            <div class="application-item">
                <img src="{{ $application->pet->image_url ?? '/images/default-pet.png' }}" alt="{{ $application->pet->name }}" class="pet-image">
                <div class="application-info">
                    <h3>Application for {{ $application->pet->name }}</h3>
                    <p>From: {{ $application->adopter->user->name }} • Phone: {{ $application->adopter->user->phone }}</p>
                    <div class="application-meta">
                        <span>Submitted: {{ \Carbon\Carbon::parse($application->submitted_at)->format('F d, Y') }}</span>
                        <span class="status-badge status-{{ $application->status }}">{{ ucfirst($application->status) }}</span>
                    </div>
                </div>
                <div class="action-buttons">
                    <button class="btn btn-primary" onclick="showApplicationModal({{ $application->application_id }})">Review</button>
                    <button class="btn btn-outline" onclick="messageApplicant('{{ $application->adopter->user->name }}')">Message</button>
                </div>
            </div>
        @endforeach
    </div>
</div>

<!-- Application Review Modal -->
<div id="applicationModal" class="modal" style="display:none;">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Application Details</h2>
            <button class="close-btn">&times;</button>
        </div>
        <div class="modal-body">
            @if(isset($selectedApplication))
            <div class="applicant-info">
                <h3>Applicant Information</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Full Name</label>
                        <p id="applicantName">{{ $selectedApplication->adopter->user->name ?? '' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Phone</label>
                        <p id="applicantPhone">{{ $selectedApplication->adopter->user->phone ?? '' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Email</label>
                        <p id="applicantEmail">{{ $selectedApplication->adopter->user->email ?? '' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Address</label>
                        <p id="applicantAddress">{{ $selectedApplication->adopter->user->address ?? '' }}</p>
                    </div>
                </div>
            </div>

            <div class="application-details">
                <h3>Application Details</h3>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Pet Name</label>
                        <p id="petName">{{ $selectedApplication->pet->name ?? '' }}</p>
                    </div>
                    <div class="info-item">
                        <label>Submission Date</label>
                        <p id="submissionDate">{{ \Carbon\Carbon::parse($selectedApplication->submitted_at)->format('F d, Y') }}</p>
                    </div>
                    <div class="info-item">
                        <label>Status</label>
                        <p id="applicationStatus">{{ ucfirst($selectedApplication->status ?? '') }}</p>
                    </div>
                </div>

                <div class="questionnaire">
                    <h4>Questionnaire Responses</h4>
                    <div class="question">
                        <label>Why do you want to adopt this pet?</label>
                        <p>{{ $selectedApplication->reason_for_adoption ?? '' }}</p>
                    </div>
                    <div class="question">
                        <label>Do you have experience with pets?</label>
                        <p>{{ $selectedApplication->experience_with_pets ?? '' }}</p>
                    </div>
                    <div class="question">
                        <label>Living situation</label>
                        <p>{{ $selectedApplication->living_environment ?? '' }}</p>
                    </div>
                    <div class="question">
                        <label>Household Members</label>
                        <p>{{ $selectedApplication->household_members ?? '' }}</p>
                    </div>
                    <div class="question">
                        <label>Allergies</label>
                        <p>{{ $selectedApplication->allergies ? 'Yes' : 'No' }}</p>
                    </div>
                    <div class="question">
                        <label>Has Other Pets</label>
                        <p>{{ $selectedApplication->has_other_pets ? 'Yes' : 'No' }}</p>
                    </div>
                    @if($selectedApplication->has_other_pets)
                    <div class="question">
                        <label>Other Pets Details</label>
                        <p>{{ $selectedApplication->other_pets_details ?? '' }}</p>
                    </div>
                    @endif
                    <div class="question">
                        <label>Can Provide Vet Care</label>
                        <p>{{ $selectedApplication->can_provide_vet_care ? 'Yes' : 'No' }}</p>
                    </div>
                    @if($selectedApplication->status == 'rejected')
                    <div class="question">
                        <label>Rejection Reason</label>
                        <p>{{ $selectedApplication->rejection_reason ?? '' }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif
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
    function showApplicationModal(id) {
        // You should implement AJAX or Livewire to load the selected application details by id
        modal.style.display = 'block';
        document.body.style.overflow = 'hidden';
    }
    function messageApplicant(applicantName) {
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
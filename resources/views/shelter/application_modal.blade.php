<div class="applicant-info">
    <h3>Applicant Information</h3>
    <div class="info-grid">
        <div class="info-item">
            <label>Full Name</label>
            <p>{{ $application->adopter->user->name ?? '' }}</p>
        </div>
        <div class="info-item">
            <label>Phone</label>
            <p>{{ $application->adopter->user->phone_number ?? '' }}</p>
        </div>
        <div class="info-item">
            <label>Email</label>
            <p>{{ $application->adopter->user->email ?? '' }}</p>
        </div>
        <div class="info-item">
            <label>Address</label>
            <p>{{ $application->adopter->address ?? '' }}</p>
        </div>
    </div>
</div>
<div class="application-details">
    <h3>Application Details</h3>
    <div class="info-grid">
        <div class="info-item">
            <label>Pet Name</label>
            <p>{{ $application->pet->name ?? '' }}</p>
        </div>
        <div class="info-item">
            <label>Submission Date</label>
            <p>{{ \Carbon\Carbon::parse($application->submitted_at)->format('F d, Y') }}</p>
        </div>
        <div class="info-item">
            <label>Status</label>
            <p>
                <span class="status-badge status-{{ $application->status ?? 'pending' }}">
                    {{ ucfirst($application->status ?? '') }}
                </span>
            </p>
        </div>
    </div>
    <div class="questionnaire">
        <h4>Questionnaire Responses</h4>
        <div class="question">
            <label>Why do you want to adopt this pet?</label>
            <p>{{ $application->reason_for_adoption ?? '' }}</p>
        </div>
        <div class="question">
            <label>Do you have experience with pets?</label>
            <p>{{ $application->experience_with_pets ?? '' }}</p>
        </div>
        <div class="question">
            <label>Living situation</label>
            <p>{{ $application->living_environment ?? '' }}</p>
        </div>
        <div class="question">
            <label>Household Members</label>
            <p>{{ $application->household_members ?? '' }}</p>
        </div>
        <div class="question">
            <label>Allergies</label>
            <p>{{ $application->allergies ? 'Yes' : 'No' }}</p>
        </div>
        <div class="question">
            <label>Has Other Pets</label>
            <p>{{ $application->has_other_pets ? 'Yes' : 'No' }}</p>
        </div>
        @if ($application->has_other_pets)
            <div class="question">
                <label>Other Pets Details</label>
                <p>{{ $application->other_pets_details ?? '' }}</p>
            </div>
        @endif
        <div class="question">
            <label>Can Provide Vet Care</label>
            <p>{{ $application->can_provide_vet_care ? 'Yes' : 'No' }}</p>
        </div>
        @if ($application->status == 'rejected')
            <div class="question">
                <label>Rejection Reason</label>
                <p>{{ $application->rejection_reason ?? '' }}</p>
            </div>
        @endif
    </div>
</div>
<div class="modal-actions">
    <button class="approve-btn" id="approveBtn"
        style="background-color: #28a745; color: #fff; border: none; padding: 8px 16px; border-radius: 8px;">
        Approve Application
    </button>
    <button class="reject-btn" id="rejectBtn"
        style="background-color: #dc3545; color: #fff; border: none; padding: 8px 16px; border-radius: 8px;">
        Reject Application
    </button>
    <button class="message-btn"
        style="background-color: #f1f1f1; color: #333; border: none; padding: 8px 16px; border-radius: 8px; outline: black;"
        onclick="messageApplicant({{ $application->adopter->user->user_id }})">Message</button>
</div>

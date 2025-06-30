@extends('layouts.admin')

@section('content')
<div class="content-section">
    <div class="top-bar">
        <div class="welcome-section">
            <h1>Verification Management</h1>
            <p>Review and manage user verification requests</p>
        </div>
    </div>

    <div class="filters-section">
        <input type="text" class="form-control search-input" placeholder="Search by name or email...">
        <select class="form-select role-filter">
            <option value="">All Types</option>
            <option value="shelter">Shelter</option>
            <option value="rescuer">Rescuer</option>
            <option value="adopter">Adopter</option>
        </select>
        <select class="form-select status-filter">
            <option value="">All Status</option>
            <option value="pending">Pending</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
        </select>
    </div>

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value pending-count">{{ $stats['pending'] }}</div>
            <div class="stat-title">Pending Verifications</div>
        </div>
        <div class="stat-card">
            <div class="stat-value approved-count">{{ $stats['approved_today'] }}</div>
            <div class="stat-title">Approved Today</div>
        </div>
        <div class="stat-card">
            <div class="stat-value rejected-count">{{ $stats['rejected_today'] }}</div>
            <div class="stat-title">Rejected Today</div>
        </div>
    </div>

    <div class="table-container">
        <table class="verification-table">
            <thead>
                <tr>
                    <th>USER</th>
                    <th>TYPE</th>
                    <th>DOCUMENTS</th>
                    <th>SUBMITTED</th>
                    <th>STATUS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @foreach($verifications as $verification)
                <tr>
                    <td>
                        <div class="user-cell">
                            <div class="user-avatar">
                                <div class="profile-img-placeholder">
                                    {{ substr($verification->first_name, 0, 1) . substr($verification->last_name, 0, 1) }}
                                </div>
                            </div>
                            <div class="user-info">
                                <span class="user-name">{{ $verification->first_name . ' ' . $verification->last_name }}</span>
                                <span class="user-email">{{ $verification->email }}</span>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="role-badge role-{{ $verification->type }}">
                            {{ ucfirst($verification->type) }}
                        </span>
                    </td>
                    <td>

                        <!-- Work in progress -->
                        <div class="document-previews">
                            @if($verification->type === 'shelter')
                                <button class="action-icon" 
                                        onclick="viewRegistrationDocument('{{ Storage::disk('s3')->url($verification->document_url) }}')" 
                                        title="View Registration Document">
                                    <i class="fas fa-file-alt"></i> Registration Doc
                                </button>
                        <!-- Work in progress -->

                                </a>
                            @elseif($verification->type === 'rescuer')
                                <a href="{{ Storage::disk('s3')->url($verification->document_url) }}" target="_blank" class="document-link">
                                    <i class="fas fa-file-alt"></i> Credentials
                                </a>
                            @endif
                        </div>
                    </td>
                    <td>
                        <div class="submitted-info">
                            <div>{{ \Carbon\Carbon::parse($verification->submitted_at)->format('M d, Y') }}</div>
                            <div class="time-ago">{{ \Carbon\Carbon::parse($verification->submitted_at)->diffForHumans() }}</div>
                        </div>
                    </td>
                    <td>
                        <span class="status-badge status-{{ $verification->status }}">
                            {{ ucfirst($verification->status) }}
                        </span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <button class="action-icon" title="View Details" onclick="viewVerification({{ $verification->verification_id }})">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if($verification->status === 'pending')
                                <button class="action-icon approve" title="Approve" onclick="approveVerification({{ $verification->verification_id }})">
                                    <i class="fas fa-check"></i>
                                </button>
                                <button class="action-icon reject" title="Reject" onclick="rejectVerification({{ $verification->verification_id }})">
                                    <i class="fas fa-times"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Verification Details Modal -->
<div id="verificationModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Verification Details</h2>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <div class="verification-details">
                <div class="user-profile">
                    <div class="user-avatar-large">
                        <img id="modalUserAvatar" src="" alt="User Avatar">
                    </div>
                    <div class="user-info-detailed">
                        <h3 id="modalUserName" class="user-name"></h3>
                        <div class="user-meta">
                            <div class="meta-item">
                                <span class="meta-label">Email:</span>
                                <span id="modalUserEmail" class="meta-value"></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Type:</span>
                                <span id="modalUserType" class="meta-value"></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Status:</span>
                                <span id="modalUserStatus" class="meta-value"></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Submitted:</span>
                                <span id="modalSubmissionDate" class="meta-value"></span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="documents-section">
                    <h4>Submitted Documents</h4>
                    <div id="documentPreviews" class="document-preview-grid">
                        <!-- Document previews will be inserted here -->
                    </div>
                </div>

                <div id="reviewSection" class="review-section" style="display: none;">
                    <h4>Review Decision</h4>
                    <div class="review-form">
                        <textarea id="reviewNotes" class="form-control" placeholder="Add notes about your decision..."></textarea>
                        <div class="review-actions">
                            <button class="btn btn-success" onclick="approveWithNotes()">
                                <i class="fas fa-check"></i> Approve
                            </button>
                            <button class="btn btn-danger" onclick="rejectWithNotes()">
                                <i class="fas fa-times"></i> Reject
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Document Modal - To view registration document -->
<div id="documentModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Registration Document</h2>
            <button class="close-document-modal" title="Close">&times;</button>
        </div>
        <div class="modal-body">
            <div class="document-frame-wrapper" style="max-height: 80vh; overflow-y: auto;">
                <iframe id="registrationDocViewer"
                        width="100%"
                        height="500"
                        frameborder="0"
                        style="border-radius: 6px; border: 1px solid #ccc;"></iframe>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    let currentVerificationStatus = '';

    function viewVerification(id) {
        // Implementation for viewing verification details
        fetch(`/admin/verifications/${id}`)
            .then(response => response.json())
            .then(data => {
                currentVerificationStatus = data.status;
                // Show/hide review section based on status
                document.getElementById('reviewSection').style.display = 
                    currentVerificationStatus === 'pending' ? 'block' : 'none';
                
                // Populate modal with verification details
                document.getElementById('modalUserName').textContent = data.first_name + ' ' + data.last_name;
                document.getElementById('modalUserEmail').textContent = data.email;
                document.getElementById('modalUserType').textContent = data.type;
                document.getElementById('modalUserStatus').textContent = data.status;
                document.getElementById('modalSubmissionDate').textContent = 
                    new Date(data.submitted_at).toLocaleDateString('en-US', {
                        year: 'numeric',
                        month: 'long',
                        day: 'numeric'
                    });
                
                // Show modal
                document.getElementById('verificationModal').style.display = 'flex';
            });
    }
    //Replaced data.verification.status with data.status

    function approveVerification(id) {
        if (confirm('Are you sure you want to approve this verification?')) {
            fetch(`/admin/verifications/${id}/approve`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => {
                window.location.reload();
            });
        }
    }

    function rejectVerification(id) {
        if (confirm('Are you sure you want to reject this verification?')) {
            fetch(`/admin/verifications/${id}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => {
                window.location.reload();
            });
        }
    }

    //Close modal when clicking the close button or outside the modal
    document.addEventListener('DOMContentLoaded', () => {
        const closeBtn = document.querySelector('.close-modal');
        if (closeBtn) {
            closeBtn.addEventListener('click', () => {
                document.getElementById('verificationModal').style.display = 'none';
            });
        }

        const modal = document.getElementById('verificationModal');
        if (modal) {
            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.style.display = 'none';
                }
            });
        }
    });

    // Registration Doc Modal
        window.viewRegistrationDocument = function(url) {
            const viewer = document.getElementById('registrationDocViewer');
            const modal = document.getElementById('documentModal');
            viewer.src = url;
            modal.style.display = 'flex';
        };

        document.addEventListener('DOMContentLoaded', () => {
            const closeDocBtn = document.querySelector('.close-document-modal');
            const docModal = document.getElementById('documentModal');
            const docViewer = document.getElementById('registrationDocViewer');

            if (closeDocBtn) {
                closeDocBtn.addEventListener('click', () => {
                    docModal.style.display = 'none';
                    docViewer.src = '';
                });
            }

            if (docModal) {
                window.addEventListener('click', (e) => {
                    if (e.target === docModal) {
                        docModal.style.display = 'none';
                        docViewer.src = '';
                    }
                });
            }
        });
</script>
@endsection 
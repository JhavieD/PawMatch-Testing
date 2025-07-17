@extends('layouts.admin')

@section('page-title', 'Verification Management')
@section('page-subtitle', 'Review and manage user verification requests')

@section('content')
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
                    <th>SUBMITTED</th>
                    <th>STATUS</th>
                    <th>ACTIONS</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($verifications as $verification)
                    <tr>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar">
                                    <div class="profile-img-placeholder">
                                        {{ substr($verification->first_name, 0, 1) . substr($verification->last_name, 0, 1) }}
                                    </div>
                                </div>
                                <div class="user-info">
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
                            <div class="submitted-info">
                                <div>{{ \Carbon\Carbon::parse($verification->submitted_at)->format('M d, Y') }}</div>
                                <div class="time-ago">
                                    {{ \Carbon\Carbon::parse($verification->submitted_at)->diffForHumans() }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $verification->status }}">
                                {{ ucfirst($verification->status) }}
                            </span>
                        </td>
                        <td>
                            <button class="action-icon" title="View Details"
                                onclick="viewVerification({{ $verification->verification_id }}, '{{ $verification->type }}')">
                                <i class="fas fa-eye"></i>
                            </button>
                            </button>
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
                            <img id="modalUserAvatar" src="/images/default-profile.png" alt="Profile Picture" class="profile-img" />
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

                        <div id="documentPreviewContainer" class="document-preview-container">
                            <!-- Injected preview (image/pdf) goes here -->
                        </div>

                        <div class="facebook-link-container mt-2">
                            <a id="modalFacebookLink" href="#" class="btn btn-primary btn-sm" target="_blank"
                                style="display: none;">
                                <i class="fab fa-facebook-square"></i> View Facebook Page
                            </a>
                            <div id="modalFacebookFallback" class="text-muted" style="display: none;">
                                No Facebook link provided.
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Transfer Functionality of buttons here -->
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

@endsection

@section('scripts')
    <script>
        let currentVerificationId = null;
        let currentVerificationStatus = '';
        let currentVerificationType = '';

        function viewVerification(id, type) {
            currentVerificationId = id;
            currentVerificationType = type;

            fetch(`/admin/verifications/${id}?type=${type}`)
                .then(response => response.json())
                .then(data => {
                    currentVerificationStatus = data.status;

                    document.getElementById('modalUserName').textContent = `${data.first_name} ${data.last_name}`;
                    document.getElementById('modalUserEmail').textContent = data.email;
                    document.getElementById('modalUserType').textContent = data.type;
                    document.getElementById('modalUserStatus').textContent = data.status;
                    document.getElementById('modalSubmissionDate').textContent = new Date(data.submitted_at)
                        .toLocaleDateString('en-US', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric'
                        });

                    document.getElementById('reviewSection').style.display = data.status === 'pending' ? 'block' :
                    document.getElementById('modalUserAvatar').src = data.profile_image ? data.profile_image : '/images/default-profile.png';
                        'none';

                    const docContainer = document.getElementById('documentPreviewContainer');
                    docContainer.innerHTML = '';

                    if (data.document_url) {
                        const fileType = data.document_url.split('.').pop().toLowerCase();
                        if (fileType === 'pdf') {
                            docContainer.innerHTML =
                                `<iframe src="${data.document_url}" style="width: 100%; height: 400px; border: 1px solid #ccc; border-radius: 6px;"></iframe>`;
                        } else {
                            docContainer.innerHTML =
                                `<img src="${data.document_url}" alt="Document Image" style="max-width: 90%; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.1);">`;
                        }
                    } else {
                        docContainer.innerHTML = `<p class="text-muted">No document uploaded.</p>`;
                    }

                    const fbLinkBtn = document.getElementById('modalFacebookLink');
                    const fbFallback = document.getElementById('modalFacebookFallback');

                    if (data.facebook_link && data.facebook_link.trim() !== '') {
                        fbLinkBtn.href = data.facebook_link;
                        fbLinkBtn.style.display = 'inline-block';
                        fbFallback.style.display = 'none';
                    } else {
                        fbLinkBtn.style.display = 'none';
                        fbFallback.style.display = 'block';
                    }

                    document.getElementById('verificationModal').style.display = 'flex';
                });
        }

        function approveWithNotes() {
            if (!currentVerificationId) return;

            if (confirm('Are you sure you want to approve this verification?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/verifications/${currentVerificationId}/approve?type=${currentVerificationType}`;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;

                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

        function rejectWithNotes() {
            if (!currentVerificationId) return;

            if (confirm('Are you sure you want to reject this verification?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/admin/verifications/${currentVerificationId}/reject?type=${currentVerificationType}`;

                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = document.querySelector('meta[name="csrf-token"]').content;

                form.appendChild(csrfInput);
                document.body.appendChild(form);
                form.submit();
            }
        }

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
    </script>
@endsection

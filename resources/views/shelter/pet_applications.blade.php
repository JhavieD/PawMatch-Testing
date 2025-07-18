@extends ('layouts.applications-review')

@section('title', 'Applications')

@section('shelter-content')
    <div class="main-content">
        <div class="content-wrapper">
            <div class="top-bar">
                <div class="welcome-section">
                    <h1>Adoption Applications</h1>
                    <p>Manage all adoption applications for your shelter</p>
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
                <select class="filter-dropdown" id="statusFilter">
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
            </div>

            <div class="content-card">
                <!-- Applications Table -->
                <table class="applications-table" style="width:100%; border-collapse:separate; border-spacing:0 1rem;">
                    <thead>
                        <tr>
                            <th>Pet</th>
                            <th>Applicant</th>
                            <th>Phone</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($applications as $application)
                            <tr class="application-item"
                                data-applicant="{{ strtolower($application->adopter->user->name ?? '') }}"
                                data-pet="{{ strtolower($application->pet->name ?? '') }}"
                                data-status="{{ strtolower($application->status) }}">
                                <td>
                                    <img src="{{ $application->pet->image_url ?? '/images/default-pet.png' }}"
                                        alt="{{ $application->pet->name }}" class="pet-image"
                                        style="width:48px; height:48px; object-fit:cover; border-radius:8px;">
                                </td>
                                <td>
                                    <strong>Application for {{ $application->pet->name }}</strong><br>
                                    {{ $application->adopter->user->name }}
                                </td>
                                <td>
                                    {{ $application->adopter->user->phone_number }}
                                </td>
                                <td>
                                    {{ \Carbon\Carbon::parse($application->submitted_at)->format('F d, Y') }}
                                </td>
                                <td>
                                    <span class="status-badge status-{{ strtolower($application->status) }}"
                                        data-id="{{ $application->application_id }}">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-primary"
                                        onclick="showApplicationModal({{ $application->application_id }})">Review</button>

                                    @if (!in_array(strtolower($application->status), ['completed', 'cancelled']))
                                        <button class="successfull-btn mark-completed-btn" title="Mark as Completed"
                                            data-id="{{ $application->application_id }}">
                                            <i class="fa-solid fa-circle-check" style="color:green"></i>
                                        </button>
                                        <button class="cancelled-btn mark-cancelled-btn" title="Mark as Cancelled"
                                            data-id="{{ $application->application_id }}">
                                            <i class="fa-solid fa-xmark" style="color:red"></i>
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Application Review Modal -->
        <div id="applicationModal" class="modal" style="display:none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Application Details</h2>
                    <button class="close-btn">&times;</button>
                </div>
                <div class="modal-body" id="applicationModalBody">
                    <!-- Application details will be loaded here via AJAX -->
                </div>
            </div>
        </div>

        <!-- Rejection Reason Modal -->
        <div id="rejectionModal" class="modal" style="display: none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Reject Application</h2>
                    <button class="close-rejection-btn">&times;</button>
                </div>
                <div class="modal-body">
                    <label for="rejectionReason">Please provide a reason for rejection:</label>
                    <textarea id="rejectionReason" rows="4" placeholder="Enter reason here..." style="width: 100%;"></textarea>
                    <div style="margin-top: 1rem; text-align: right;">
                        <button class="btn btn-outline" id="cancelRejectionBtn">Cancel</button>
                        <button class="btn btn-primary" id="confirmRejectionBtn">Confirm Reject</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Redirect to messages page for the given adopter
        function messageApplicant(adopterId) {
            window.location.href = `/shelter/messages?receiver_id=${adopterId}`;
        }

        let currentApplicationId = null;
        const modal = document.getElementById('applicationModal');
        const rejectionModal = document.getElementById('rejectionModal');
        const closeBtn = document.querySelector('.close-btn');
        const closeRejectionBtn = document.querySelector('.close-rejection-btn');
        const cancelRejectionBtn = document.getElementById('cancelRejectionBtn');
        const confirmRejectionBtn = document.getElementById('confirmRejectionBtn');

        function logout() {
            window.location.href = 'login.html';
        }

        function showApplicationModal(id) {
            currentApplicationId = id;
            fetch(`/shelter/applications/${id}/review`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('applicationModalBody').innerHTML = html;
                    modal.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                    attachActionHandlers(id);
                });
        }
        // Make showApplicationModal globally accessible
        window.showApplicationModal = showApplicationModal;

        function closeModal(modalElement) {
            modalElement.style.display = 'none';
            document.body.style.overflow = 'auto';
        }

        closeBtn?.addEventListener('click', () => closeModal(modal));
        closeRejectionBtn?.addEventListener('click', () => closeModal(rejectionModal));
        cancelRejectionBtn?.addEventListener('click', () => closeModal(rejectionModal));

        window.addEventListener('click', (e) => {
            if (e.target === modal) closeModal(modal);
            if (e.target === rejectionModal) closeModal(rejectionModal);
        });

        function attachActionHandlers(id) {
            const approveBtn = document.getElementById('approveBtn');
            const rejectBtn = document.getElementById('rejectBtn');
            const requestInfoBtn = document.getElementById('requestInfoBtn');
            // Get the status from the badge
            const badge = document.querySelector(`.status-badge[data-id="${id}"]`);
            const status = badge ? badge.innerText.trim().toLowerCase() : '';
            if (status === 'completed' || status === 'cancelled') {
                if (approveBtn) approveBtn.style.display = 'none';
                if (rejectBtn) rejectBtn.style.display = 'none';
                if (requestInfoBtn) requestInfoBtn.style.display = 'none';
                return;
            }

            if (approveBtn) {
                approveBtn.onclick = () => {
                    fetch(`/shelter/applications/${id}/approve`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Content-Type': 'application/json',
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({}) // sending empty body to avoid Laravel errors
                        })
                        .then(res => {
                            if (!res.ok) throw new Error('Failed to approve');
                            return res.json();
                        })
                        .then(() => {
                            updateStatusBadge(id, 'approved');
                            closeModal(modal);
                        })
                        .catch(err => alert('Approval failed. Check console.'));
                };
            }

            if (rejectBtn) {
                rejectBtn.onclick = () => {
                    currentApplicationId = id;
                    rejectionModal.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                };
            }

            if (requestInfoBtn) {
                requestInfoBtn.onclick = () => {
                    fetch(`/shelter/applications/${id}/request-info`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    }).then(res => res.json()).then(() => {
                        updateStatusBadge(id, 'info-requested');
                        closeModal(modal);
                    });
                };
            }
        }

        confirmRejectionBtn?.addEventListener('click', () => {
            const reason = document.getElementById('rejectionReason').value.trim();
            if (!reason) return alert("Please enter a reason for rejection.");

            fetch(`/shelter/applications/${currentApplicationId}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    rejection_reason: reason
                })
            }).then(res => res.json()).then(() => {
                updateStatusBadge(currentApplicationId, 'rejected');
                closeModal(rejectionModal);
                closeModal(modal);
            });
        });

        function updateStatusBadge(id, newStatus) {
            const badge = document.querySelector(`.status-badge[data-id="${id}"]`);
            if (badge) {
                badge.innerText = newStatus.replace('-', ' ').replace(/\b\w/g, c => c.toUpperCase());
                badge.className = `status-badge status-${newStatus}`;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.querySelector('.search-input');
            const statusFilter = document.querySelector('.filter-dropdown');

            function filterApplications() {
                const search = searchInput.value.trim().toLowerCase();
                const status = statusFilter.value;
                const applicationItems = document.querySelectorAll('.application-item');

                applicationItems.forEach(item => {
                    const applicant = item.getAttribute('data-applicant');
                    const pet = item.getAttribute('data-pet');
                    const itemStatus = item.getAttribute('data-status');

                    const matchesSearch = !search ||
                        applicant.includes(search) ||
                        pet.includes(search);

                    const matchesStatus =
                        status === 'all' ||
                        itemStatus === status;

                    if (matchesSearch && matchesStatus) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }
            searchInput.addEventListener('input', filterApplications);
            statusFilter.addEventListener('change', filterApplications);
            filterApplications();

            // Add event listeners for mark-complete and mark-cancelled buttons directly
            document.querySelectorAll('.mark-completed-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    fetch(`/shelter/applications/${id}/complete`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) updateStatusBadge(id, 'completed');
                        });
                });
            });
            document.querySelectorAll('.mark-cancelled-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    fetch(`/shelter/applications/${id}/cancel`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) updateStatusBadge(id, 'cancelled');
                        });
                });
            });
        });
    </script>
@endsection

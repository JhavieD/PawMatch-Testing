@extends('layouts.rescuer-applications')

@section('title', 'Pet Applications - PawMatch')

@section('rescuer-content')
    <div class="container" style="margin-left: 250px;">
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
                <div class="application-item"
                    data-applicant="{{ strtolower($application->adopter->user->name ?? '') }}"
                    data-pet="{{ strtolower($application->pet->name ?? '') }}"
                    data-status="{{ strtolower($application->status) }}">
                    <img src="{{ $application->pet->image_url ?? '/images/default-pet.png' }}" alt="{{ $application->pet->name }}" class="pet-image">
                    <div class="application-info">
                        <h3>Application for {{ $application->pet->name }}</h3>
                        <p>From: {{ $application->adopter->user->name }} • Phone: {{ $application->adopter->user->phone_number }}</p>
                        <div class="application-meta">
                            <span>Submitted: {{ \Carbon\Carbon::parse($application->submitted_at)->format('F d, Y') }}</span>
                            <span class="status-badge status-{{ $application->status }}" data-id="{{ $application->application_id }}">
                                {{ ucfirst($application->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="action-buttons">
                        <button class="btn btn-primary" onclick="showApplicationModal({{ $application->application_id }})">Review</button>
                        <button class="btn btn-outline" onclick="messageApplicant({{ $application->adopter->user->user_id }})">Message</button>
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

    <script>
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
            fetch(`/rescuer/applications/${id}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('applicationModalBody').innerHTML = html;
                    modal.style.display = 'block';
                    document.body.style.overflow = 'hidden';
                    attachActionHandlers(id);
                });
        }

        function messageApplicant(adopterId) {
            window.location.href = `/rescuer/messages?receiver_id=${adopterId}`;
        }

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

            if (approveBtn) {
                approveBtn.onclick = () => {
                    fetch(`/rescuer/applications/${id}/approve`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({})
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
                    fetch(`/rescuer/applications/${id}/request-info`, {
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

            fetch(`/rescuer/applications/${currentApplicationId}/reject`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ rejection_reason: reason })
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
            const applicationItems = document.querySelectorAll('.application-item');
            function filterApplications() {
                const search = searchInput.value.trim().toLowerCase();
                const status = statusFilter.value;

                applicationItems.forEach(item => {
                    const applicant = item.getAttribute('data-applicant');
                    const pet = item.getAttribute('data-pet');
                    const itemStatus = item.getAttribute('data-status');

                    const matchesSearch = !search ||
                        applicant.includes(search) ||
                        pet.includes(search);

                    const matchesStatus = status === 'all' ||
                        (status === 'pending' && itemStatus === 'pending') ||
                        (status === 'approved' && itemStatus === 'approved') ||
                        (status === 'rejected' && itemStatus === 'rejected');

                    if (matchesSearch && matchesStatus) {
                        item.style.display = '';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }
            searchInput.addEventListener('input', filterApplications);
            statusFilter.addEventListener('change', filterApplications);
        });
    </script>
@endsection

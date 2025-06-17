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
        <div class="modal-body" id="applicationModalBody">
            <!-- Application details will be loaded here via AJAX -->
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
        fetch(`/shelter/applications/${id}`)
            .then(response => response.text())
            .then(html => {
                document.getElementById('applicationModalBody').innerHTML = html;
                modal.style.display = 'block';
                document.body.style.overflow = 'hidden';
                attachActionHandlers(id);
            });
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

    function attachActionHandlers(id) {
        const approveBtn = document.getElementById('approveBtn');
        const rejectBtn = document.getElementById('rejectBtn');
        const requestInfoBtn = document.getElementById('requestInfoBtn');
        if (approveBtn) {
            approveBtn.onclick = function() {
                fetch(`/shelter/applications/${id}/approve`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(res => res.json()).then(data => {
                    alert('Application approved!');
                    location.reload();
                });
            };
        }
        if (rejectBtn) {
            rejectBtn.onclick = function() {
                const reason = prompt('Enter rejection reason:');
                if (!reason) return;
                fetch(`/shelter/applications/${id}/reject`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ rejection_reason: reason })
                }).then(res => res.json()).then(data => {
                    alert('Application rejected!');
                    location.reload();
                });
            };
        }
        if (requestInfoBtn) {
            requestInfoBtn.onclick = function() {
                fetch(`/shelter/applications/${id}/request-info`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                }).then(res => res.json()).then(data => {
                    alert('Information request sent to applicant!');
                    location.reload();
                });
            };
        }
    }
</script>
@endsection
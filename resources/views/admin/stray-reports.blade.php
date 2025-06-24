@extends('layouts.admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/shared/strayreports.css') }}">
@endpush

@section('content')
<main class="main-content">
    <div class="content-wrapper">
        <!-- Top Bar-->
        <div class="top-bar">
            <div class="welcome-section">
                <h1>Stray Reports</h1>
                <p>Monitor and manage stray animal reports</p>
            </div>
            <div class="profile-section">
                <div class="profile-img">
                    <img src="https://scontent.fmnl17-2.fna.fbcdn.net/v/t1.15752-9/476503441_517858481005706_1040348296589328384_n.jpg?_nc_cat=111&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeFnq_r6iLm0176l921rbXReB2Ibi4wAzpAHYhuLjADOkO5EECoPu1bv3ZuLeNQ2NR2O4Zb5QIRaIZeqJ1qkbs_5&_nc_ohc=Zp0xtej9oQYQ7kNvgEid6-4&_nc_oc=AdhyKmcrcacii3szUFiVX_G2QAB2Y3jCJUGOi7FlI1fMbkckAB3fEOUDUwzE0dQQsdE&_nc_zt=23&_nc_ht=scontent.fmnl17-2.fna&oh=03_Q7cD1wHKQEtcytcr-LBageGP1U_4-tQMwLfeHlrd0sH7cg5YnA&oe=67F7C93F" alt="Admin Profile">
                </div>
                <div class="profile-info">
                    <strong>Super Admin</strong>
                </div>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="content-card">
            <form method="GET" action="{{ route('admin.stray-reports') }}" class="search-filter">
                <input type="text" name="search" class="search-box" placeholder="Search reports..." value="{{ request('search') }}">
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="investigating" {{ request('status') == 'investigating' ? 'selected' : '' }}>Investigating</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>

        <!-- Reports Grid -->
        <div class="report-grid">
            @forelse($reports as $report)
                <div class="report-card"
                    data-report-id="{{ $report->report_id }}"
                    data-image="{{ $report->image_url ?? 'https://via.placeholder.com/150' }}"
                    data-description="{{ $report->description }}"
                    data-location="{{ $report->location }}"
                    data-status="{{ $report->status }}"
                    data-reported-at="{{ $report->reported_at ? \Carbon\Carbon::parse($report->reported_at)->format('F d, Y') : '' }}"
                    data-animal-type="{{ $report->animal_type ?? '' }}"
                    data-reporter="{{ $report->adopter->user->name }}"
                    data-reporter-contact="{{ $report->adopter->user->email }}"
                    data-timeline='@json($report->timeline ?? [])'
                    data-comments='@json($report->comments ?? [])'
                >
                    <img src="{{ $report->image_url ?? 'https://via.placeholder.com/150' }}" alt="Stray Animal" class="report-image">
                    <div class="report-content">
                        <h3 class="report-title">{{ $report->description }}</h3>
                        <div class="report-location">
                            <i class="fas fa-map-marker-alt"></i>
                            {{ $report->location }}
                        </div>
                        <div class="status-badge status-{{ $report->status }}">
                            {{ ucfirst($report->status) }}
                        </div>
                    </div>
                </div>
            @empty
                <div>No reports found.</div>
            @endforelse
        </div>
        {{ $reports->links() }}
    </div>
</main>

<!-- Report Details Modal -->
<div class="report-modal" id="reportModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Stray Report Details</h2>
            <button class="close-modal" onclick="closeReportModal()">&times;</button>
        </div>
        <div class="report-header">
            <div class="report-images">
                <img id="modalReportImage" src="" alt="Stray Animal" class="report-image">
            </div>
            <div>
                <div class="info-block">
                    <div class="info-label">Report ID:</div>
                    <div class="info-value" id="reportId"></div>
                </div>
                <div class="status-badge" id="reportStatus"></div>
            </div>
        </div>
        <div class="report-info">
            <div class="info-block">
                <div class="info-label">Reporter</div>
                <div class="info-value" id="reporterName"></div>
                <div class="info-value" id="reporterContact"></div>
            </div>
            <div class="info-block">
                <div class="info-label">Location</div>
                <div class="info-value" id="reportLocation"></div>
            </div>
            <div class="info-block">
                <div class="info-label">Date Reported</div>
                <div class="info-value" id="reportDate"></div>
            </div>
            <div class="info-block">
                <div class="info-label">Animal Type</div>
                <div class="info-value" id="animalType"></div>
            </div>
        </div>
        <div class="report-description">
            <h3>Description</h3>
            <p id="reportDescription"></p>
        </div>
        <div class="report-actions">
            <button class="btn btn-primary" onclick="event.stopPropagation(); updateStatus('investigating')">Mark as Investigating</button>
            <button class="btn btn-outline" onclick="event.stopPropagation(); updateStatus('resolved')">Mark as Resolved</button>
            <button class="btn btn-danger" onclick="event.stopPropagation(); updateStatus('cancelled')">Cancel Report</button>
        </div>
        <div class="status-update">
            <h3>Status Updates</h3>
            <div class="report-timeline"></div>
        </div>
        <div class="comment-section">
            <h3>Comments</h3>
            <div class="comment-list"></div>
            <div class="form-group">
                <textarea id="newComment" rows="3" placeholder="Add a comment..." style="width: 100%; padding: 0.75rem; border: 1px solid #e5e7eb; border-radius: 8px; font-size: 0.875rem; margin-bottom: 1rem;"></textarea>
                    <button class="btn btn-primary" onclick="event.stopPropagation(); addComment()">Add Comment</button>            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openReportModal(card) {
    const modal = document.getElementById('reportModal');
    modal.style.display = 'flex';

    // Fill modal fields
    document.getElementById('modalReportImage').src = card.dataset.image;
    document.getElementById('reportId').textContent = card.dataset.reportId;
    document.getElementById('reportStatus').className = `status-badge status-${card.dataset.status}`;
    document.getElementById('reportStatus').textContent = card.dataset.status.charAt(0).toUpperCase() + card.dataset.status.slice(1);
    document.getElementById('reporterName').textContent = card.dataset.reporter;
    document.getElementById('reporterContact').textContent = card.dataset.reporterContact;
    document.getElementById('reportLocation').textContent = card.dataset.location;
    document.getElementById('reportDate').textContent = card.dataset.reportedAt;
    document.getElementById('animalType').textContent = card.dataset.animalType;
    document.getElementById('reportDescription').textContent = card.dataset.description;

    // Always load timeline from backend for persistence
    loadTimeline(card.dataset.reportId);

    // Comments
    const commentList = document.querySelector('.comment-list');
    commentList.innerHTML = '';
    let commentsData = [];
    try { commentsData = JSON.parse(card.dataset.comments); } catch {}
    if (commentsData.length) {
        commentsData.forEach(comment => {
            commentList.innerHTML += `
                <div class="comment-item">
                    <div class="comment-header">
                        <span class="comment-author">${comment.author}</span>
                        <span class="comment-date">${comment.date}</span>
                    </div>
                    <p>${comment.text}</p>
                </div>
            `;
        });
    } else {
        commentList.innerHTML = '<div class="comment-item"><p>No comments yet.</p></div>';
    }
}

// Attach click handler to each report card
document.querySelectorAll('.report-card').forEach(card => {
    card.addEventListener('click', function() {
        openReportModal(this);
    });
});

function closeReportModal() {
    document.getElementById('reportModal').style.display = 'none';
}

function addComment() {
    const commentText = document.getElementById('newComment').value;
    if (!commentText.trim()) return;

    const commentList = document.querySelector('.comment-list');
    // Remove "No comments yet." if present
    const noComments = commentList.querySelector('.comment-item p');
    if (noComments && noComments.textContent === 'No comments yet.') {
        commentList.innerHTML = '';
    }

    const newComment = document.createElement('div');
    newComment.className = 'comment-item';
    const now = new Date();
    const formattedDate = now.toLocaleString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
    newComment.innerHTML = `
        <div class="comment-header">
            <span class="comment-author">Admin</span>
            <span class="comment-date">${formattedDate}</span>
        </div>
        <p>${commentText}</p>
    `;
    commentList.insertBefore(newComment, commentList.firstChild);

    // Clear the comment input
    document.getElementById('newComment').value = '';
}

function updateStatus(status) {
    console.log('Updating status to:', status);
    const reportId = document.getElementById('reportId').textContent;
    fetch(`/admin/stray-reports/${reportId}/status`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Update the status badge
            const statusBadge = document.getElementById('reportStatus');
            statusBadge.className = `status-badge status-${status}`;
            statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);

            // Reload timeline from backend
            loadTimeline(reportId);
        } else {
            alert(data.message || 'Failed to update status.');
        }
    })
    .catch(() => alert('Failed to update status.'));
}

function loadTimeline(reportId) {
    fetch(`/admin/stray-reports/${reportId}/timeline`)
        .then(res => res.json())
        .then(data => {
            const timeline = document.querySelector('.report-timeline');
            timeline.innerHTML = '';
            if (data.timeline && data.timeline.length) {
                data.timeline.forEach(item => {
                    timeline.innerHTML += `
                        <div class="timeline-item">
                            <div class="timeline-date">${item.date}</div>
                            <div class="timeline-content">${item.content}</div>
                        </div>
                    `;
                });
            } else {
                timeline.innerHTML = '<div class="timeline-item"><div class="timeline-content">No status updates yet.</div></div>';
            }
        });
}
</script>
@endpush
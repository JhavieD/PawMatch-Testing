
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
                <input type="text" 
                       name="search" 
                       class="search-input" 
                       placeholder="Search by report ID, location, or animal type..." 
                       value="{{ request('search') }}">
                
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="investigating" {{ request('status') == 'investigating' ? 'selected' : '' }}>Investigating</option>
                    <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>Resolved</option>
                </select>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Search
                </button>
                
                @if(request('search') || request('status'))
                    <a href="{{ route('admin.stray-reports') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                @endif
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
            <button class="btn btn-primary" onclick="event.stopPropagation(); showInvestigatingModal()">Mark as Investigating</button>
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
                <button class="btn btn-primary" onclick="event.stopPropagation(); addComment()">Add Comment</button>
            </div>
        </div>
    </div>
</div>

<!-- Investigating Modal -->
<div class="investigating-modal" id="investigatingModal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>🔍 Mark as Investigating - Notify Shelters</h3>
            <button class="close-modal" onclick="closeInvestigatingModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="location-info">
                <p><strong>📍 Report Location:</strong> <span id="reportLocationText"></span></p>
                <p>Select shelters to notify about this stray report:</p>
            </div>
            
            <div class="shelters-container" id="sheltersContainer">
                <div class="loading-message">🔄 Finding nearby shelters...</div>
            </div>
            
            <div class="notification-message">
                <label for="notificationMessage">Additional Message (Optional):</label>
                <textarea id="notificationMessage" placeholder="Add any specific instructions for the shelters..." rows="3"></textarea>
            </div>
            
            <div class="modal-actions">
                <button class="btn btn-primary" onclick="submitToShelters()" id="submitBtn" disabled>📨 Submit to Shelters</button>
                <button class="btn btn-outline" onclick="closeInvestigatingModal()">Cancel</button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Custom notification system
function showNotification(message, type = 'info', duration = 4000) {
    // Remove existing notifications
    document.querySelectorAll('.notification').forEach(notif => notif.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    
    const icons = {
        success: '✅',
        error: '❌',
        warning: '⚠️',
        info: 'ℹ️'
    };
    
    notification.innerHTML = `
        <div class="notification-content">
            <span class="notification-icon">${icons[type]}</span>
            <span class="notification-text">${message}</span>
            <button class="notification-close" onclick="this.parentElement.parentElement.remove()">&times;</button>
        </div>
    `;
    
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => notification.classList.add('show'), 100);
    
    // Auto remove after duration
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, duration);
}

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

    // Always load comments from backend for persistence
    loadComments(card.dataset.reportId);
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
    if (!commentText.trim()) {
        showNotification('Please enter a comment before submitting', 'warning');
        return;
    }

    const reportId = document.getElementById('reportId').textContent;
    
    // Save comment to backend
    fetch(`/admin/stray-reports/${reportId}/comment`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ comment: commentText })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Comment added successfully', 'success');
            document.getElementById('newComment').value = '';
            loadComments(reportId);
        } else {
            showNotification(data.message || 'Failed to add comment', 'error');
        }
    })
    .catch(() => showNotification('Failed to add comment. Please try again.', 'error'));
}

function updateStatus(status) {
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
            showNotification(`Report status updated to ${status}`, 'success');
            
            // Update the modal status badge
            const statusBadge = document.getElementById('reportStatus');
            statusBadge.className = `status-badge status-${status}`;
            statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
            
            // 🎯 NEW: Update the report card in the grid
            const reportCard = document.querySelector(`[data-report-id="${reportId}"]`);
            if (reportCard) {
                // Update the data attribute
                reportCard.dataset.status = status;
                
                // Update the visual status badge in the card
                const cardStatusBadge = reportCard.querySelector('.status-badge');
                if (cardStatusBadge) {
                    cardStatusBadge.className = `status-badge status-${status}`;
                    cardStatusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                }
            }
            
            // Reload timeline from backend
            loadTimeline(reportId);
        } else {
            showNotification(data.message || 'Failed to update status', 'error');
        }
    })
    .catch(() => showNotification('Failed to update status. Please try again.', 'error'));
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
                            <div class="timeline-date">📝 ${item.date}</div>
                            <div class="timeline-content">${item.content}</div>
                            ${item.author ? `<div class="timeline-author">by ${item.author}</div>` : ''}
                        </div>
                    `;
                });
            } else {
                timeline.innerHTML = '<div class="timeline-item"><div class="timeline-content">No status updates yet.</div></div>';
            }
        });
}

function loadComments(reportId) {
    fetch(`/admin/stray-reports/${reportId}/comments`)
        .then(res => res.json())
        .then(data => {
            const commentList = document.querySelector('.comment-list');
            commentList.innerHTML = '';
            if (data.comments && data.comments.length) {
                data.comments.forEach(comment => {
                    commentList.innerHTML += `
                        <div class="comment-item">
                            <div class="comment-header">
                                <span class="comment-author">${comment.author}</span>
                                <span class="comment-date">${comment.date}</span>
                            </div>
                            <p>${comment.content}</p>
                        </div>
                    `;
                });
            } else {
                commentList.innerHTML = '<div class="comment-item"><p>No comments yet.</p></div>';
            }
        })
        .catch(() => {
            const commentList = document.querySelector('.comment-list');
            commentList.innerHTML = '<div class="comment-item"><p>Error loading comments.</p></div>';
        });
}

let selectedShelters = [];
let currentReportId = null;

function showInvestigatingModal() {
    const reportId = document.getElementById('reportId').textContent;
    const reportLocation = document.getElementById('reportLocation').textContent;
    
    // Get the current report's actual status from the database/card data
    const reportCards = document.querySelectorAll('.report-card');
    let reportCard = null;
    
    reportCards.forEach(card => {
        if (card.dataset.reportId === reportId) {
            reportCard = card;
        }
    });
    
    if (!reportCard) {
        showNotification('Could not find report data. Please refresh the page and try again.', 'error');
        return;
    }
    
    const currentStatus = reportCard.dataset.status.toLowerCase();
    
    // Only check for investigating status
    if (currentStatus === 'investigating') {
        showNotification('This report is already being investigated!', 'warning');
        return;
    }
    
    // Proceed with opening the modal
    currentReportId = reportId;
    document.getElementById('reportLocationText').textContent = reportLocation;
    document.getElementById('investigatingModal').style.display = 'flex';
    
    // Reset selections
    selectedShelters = [];
    const submitBtn = document.getElementById('submitBtn');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submit to Shelters';
    }
    
    // Load nearby shelters
    loadNearbyShelters(reportId);
}

function loadNearbyShelters(reportId) {
    fetch(`/admin/stray-reports/${reportId}/nearby-shelters`)
        .then(res => {
            console.log('Response status:', res.status);
            return res.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                displayShelters(data.shelters);
            } else {
                document.getElementById('sheltersContainer').innerHTML = 
                    '<div class="error-message">❌ Error loading shelters: ' + (data.message || 'Unknown error') + '</div>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('sheltersContainer').innerHTML = 
                '<div class="error-message">❌ Network error loading shelters</div>';
        });
}


function displayShelters(shelters) {
    const container = document.getElementById('sheltersContainer');
    
    if (shelters.length === 0) {
        container.innerHTML = '<div class="no-shelters">⚠️ No verified shelters found in the database.</div>';
        return;
    }

    // Separate shelters into priority (same area) and other areas since merong view details button
    const priorityShelters = shelters.filter(shelter => shelter.match_score > 0);
    const otherShelters = shelters.filter(shelter => shelter.match_score === 0);
    
    let html = '<div class="shelters-list">';
    
    // Priority Shelters dito
    if (priorityShelters.length > 0) {
        html += `
            <div class="priority-section">
                <h4 class="section-title">🎯 Priority Shelters (Same Area)</h4>
                <div class="priority-shelters">
        `;
        
        priorityShelters.forEach(shelter => {
            html += generateShelterCard(shelter, true);
        });
        
        html += `
                </div>
            </div>
        `;
    }
    
    
    if (otherShelters.length > 0) {
        html += `
            <div class="other-section">
                <div class="view-more-container">
                    <button class="view-more-shelters-btn" onclick="toggleOtherShelters()">
                        <span id="toggleText">View More Shelters (${otherShelters.length} in other areas)</span>
                        <span id="toggleIcon">▼</span>
                    </button>
                </div>
                <div class="other-shelters" id="otherShelters" style="display: none;">
                    <h4 class="section-title">📍 Other Available Shelters</h4>
        `;
        
        otherShelters.forEach(shelter => {
            html += generateShelterCard(shelter, false);
        });
        
        html += `
                </div>
            </div>
        `;
    }
    
    html += '</div>';
    container.innerHTML = html;
}

function generateShelterCard(shelter, isPriority) {
    const contactName = shelter.first_name && shelter.last_name 
        ? `${shelter.first_name} ${shelter.last_name}` 
        : 'Contact Person';
    
    const priorityClass = isPriority ? 'priority-shelter' : '';
    const distanceClass = isPriority ? 'nearby' : 'far';
    
    return `
        <div class="shelter-item ${priorityClass}">
            <div class="shelter-checkbox">
                <input type="checkbox" id="shelter_${shelter.shelter_id}" 
                       value="${shelter.shelter_id}" onchange="toggleShelter(${shelter.shelter_id})">
            </div>
            <div class="shelter-info">
                <div class="shelter-header">
                    <h4>${shelter.shelter_name}</h4>
                    <span class="distance-badge ${distanceClass}">${shelter.distance_text}</span>
                </div>
                <div class="shelter-compact-details">
                    <span>📍 ${shelter.location}</span>
                    <span>📧 ${shelter.email}</span>
                </div>
            </div>
        </div>
    `;
}

function toggleShelter(shelterId) {
    const checkbox = document.getElementById(`shelter_${shelterId}`);
    
    if (checkbox.checked) {
        // Add shelter to selected list
        if (!selectedShelters.includes(shelterId)) {
            selectedShelters.push(shelterId);
        }
    } else {
        // Remove shelter from selected list
        const index = selectedShelters.indexOf(shelterId);
        if (index > -1) {
            selectedShelters.splice(index, 1);
        }
    }
    
    // Enable/disable submit button based on selection
    const submitBtn = document.getElementById('submitBtn');
    if (selectedShelters.length > 0) {
        submitBtn.disabled = false;
        submitBtn.textContent = `Submit to Shelters (${selectedShelters.length} selected)`;
    } else {
        submitBtn.disabled = true;
        submitBtn.textContent = 'Submit to Shelters';
    }
    
    console.log('Selected shelters:', selectedShelters); // For debugging
}


function toggleOtherShelters() {
    const otherShelters = document.getElementById('otherShelters');
    const toggleText = document.getElementById('toggleText');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (otherShelters.style.display === 'none') {
        otherShelters.style.display = 'block';
        toggleText.textContent = 'Hide Other Shelters';
        toggleIcon.textContent = '▲';
    } else {
        otherShelters.style.display = 'none';
        const otherCount = document.querySelectorAll('.other-shelters .shelter-item').length;
        toggleText.textContent = `View More Shelters (${otherCount} in other areas)`;
        toggleIcon.textContent = '▼';
    }
}

function submitToShelters() {
    if (selectedShelters.length === 0) {
        showNotification('Please select at least one shelter before submitting', 'warning');
        return;
    }
    
    const notificationMessage = document.getElementById('notificationMessage').value;
    
    // Disable button to prevent double submission since dapat isang investigating button click lng
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.textContent = 'Submitting...';
    
    fetch(`/admin/stray-reports/${currentReportId}/mark-investigating`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            selected_shelters: selectedShelters,
            notification_message: notificationMessage
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeInvestigatingModal();
            closeReportModal();
            setTimeout(() => location.reload(), 1000); //  delay to show notification pwede ko to iedit
        } else {
            showNotification(data.message || 'Failed to submit to shelters', 'error');
            submitBtn.disabled = false;
            submitBtn.textContent = 'Submit to Shelters';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error. Please check your connection and try again.', 'error');
        submitBtn.disabled = false;
        submitBtn.textContent = 'Submit to Shelters';
    });
}


function closeInvestigatingModal() {
    document.getElementById('investigatingModal').style.display = 'none';
    selectedShelters = [];
    currentReportId = null;
}
</script>
@endpush
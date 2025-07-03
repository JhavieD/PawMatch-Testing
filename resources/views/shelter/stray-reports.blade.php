@extends('layouts.stray-reports')

@section('title', 'Stray Reports')

@section('shelter-content')
<main class="main-content">
    <div class="content-wrapper">
        <!-- Top Bar -->        
        <div class="top-bar">
            <div class="welcome-section">
                <h1>Stray Reports</h1>
                <p>Review stray animal reports sent to your shelter</p>
            </div>
            <div class="profile-section">
                <div class="profile-img">
                    <img src="{{ auth()->user()->profile_image ?? asset('images/default-profile.png') }}" alt="Shelter Profile">
                </div>
                <div class="profile-info">
                    @php
                        $shelter = auth()->user()->shelter;
                    @endphp
                    <strong>{{ $shelter->shelter_name ?? 'Shelter' }}</strong>
                </div>
            </div>
        </div>
        <!-- Search and Filter -->
        <div class="content-card">
            <form method="GET" action="{{ route('shelter.stray-reports') }}" class="search-filter">
                <input type="text" name="search" class="search-box" placeholder="Search reports..." value="{{ request('search') }}">
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="investigating" {{ request('status') == 'investigating' ? 'selected' : '' }}>Investigating</option>
                    <option value="accepted" {{ request('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                </select>
                <button type="submit" class="btn btn-primary">Filter</button>
            </form>
        </div>
        <!-- Reports Grid -->
        <div class="report-grid">
            @forelse($reports as $report)
                <div class="report-card {{ !$report->is_read ? 'unread' : '' }}"
                    data-report-id="{{ $report->report_id }}"
                    data-image="{{ $report->image_url ?? 'https://via.placeholder.com/150' }}"
                    data-description="{{ $report->description }}"
                    data-location="{{ $report->location }}"
                    data-status="{{ $report->status }}"
                    data-reported-at="{{ $report->reported_at ? \Carbon\Carbon::parse($report->reported_at)->format('F d, Y') : '' }}"
                    data-animal-type="{{ $report->animal_type ?? '' }}"
                    data-reporter="{{ $report->reporter_name }}"
                    data-reporter-contact="{{ $report->reporter_email }}"
                    data-sent-at="{{ $report->sent_at ? \Carbon\Carbon::parse($report->sent_at)->format('F d, Y g:i A') : '' }}"
                >
                    @if(!$report->is_read)
                        <div class="unread-indicator"></div>
                    @endif
                    <img src="{{ $report->image_url ?? 'https://via.placeholder.com/150' }}" alt="Stray Animal" class="report-image">
                    <div class="report-content">
                        <div class="report-title">{{ $report->animal_type ?? 'Stray Animal' }}</div>
                        <div class="report-location">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>{{ $report->location }}</span>
                        </div>
                        <div class="report-meta">
                            <span class="animal-type">{{ ucfirst($report->animal_type) }}</span>
                            <span class="sent-time">{{ $report->sent_at ? \Carbon\Carbon::parse($report->sent_at)->diffForHumans() : '' }}</span>
                        </div>
                        <div class="status-badge status-{{ $report->status }}">
                            {{ ucfirst($report->status) }}
                        </div>
                    </div>
                </div>
            @empty
                <div class="no-reports">
                    <div class="no-reports-icon"><i class="fas fa-dog"></i></div>
                    <h3>No reports found.</h3>
                    <p>There are currently no stray animal reports for your shelter.</p>
                </div>
            @endforelse
        </div>
        @if($reports->hasPages())
            {{ $reports->links() }}
        @endif
    </div>
</main>
@endsection

@push('scripts')
<script>
// Custom notification system - ADD THIS FUNCTION
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

// Open report modal and mark as read
function openReportModal(card) {
    const modal = document.getElementById('reportModal');
    modal.style.display = 'flex';

    // Mark as read if unread
    if (card.classList.contains('unread')) {
        markAsRead(card.dataset.reportId);
        card.classList.remove('unread');
        const indicator = card.querySelector('.unread-indicator');
        if (indicator) indicator.remove();
    }

    // Fill modal fields
    document.getElementById('modalReportImage').src = card.dataset.image;
    document.getElementById('reportId').textContent = card.dataset.reportId;
    document.getElementById('reportStatus').className = `status-badge status-${card.dataset.status}`;
    document.getElementById('reportStatus').textContent = card.dataset.status.charAt(0).toUpperCase() + card.dataset.status.slice(1);
    document.getElementById('reportLocation').textContent = card.dataset.location;
    document.getElementById('animalType').textContent = card.dataset.animalType || 'Not specified';
    document.getElementById('reporterName').textContent = card.dataset.reporter;
    document.getElementById('reporterContact').textContent = card.dataset.reporterContact;
    document.getElementById('reportDate').textContent = card.dataset.reportedAt;
    document.getElementById('sentAt').textContent = card.dataset.sentAt;
    document.getElementById('reportDescription').textContent = card.dataset.description;


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

function markAsRead(reportId) {
    fetch(`/shelter/stray-reports/${reportId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    }).catch(error => console.error('Error marking as read:', error));
}

function acceptReport() {
    const reportId = document.getElementById('reportId').textContent;
    
    // Remove the confirm() - just proceed directly
    fetch(`/shelter/stray-reports/${reportId}/accept`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Report accepted successfully! 🎉', 'success');
            closeReportModal();
            
            // Update the report card status in the grid
            const reportCard = document.querySelector(`[data-report-id="${reportId}"]`);
            if (reportCard) {
                reportCard.dataset.status = 'accepted';
                const statusBadge = reportCard.querySelector('.status-badge');
                if (statusBadge) {
                    statusBadge.className = 'status-badge status-accepted';
                    statusBadge.textContent = 'Accepted';
                }
            }
            
            // Reload page after showing notification
            setTimeout(() => location.reload(), 2000);
        } else {
            showNotification('Error: ' + (data.message || 'Failed to accept report'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Network error. Please check your connection and try again.', 'error');
    });
}

//ito ug google maps like ahhahhha
function getDirections() {
    const location = document.getElementById('reportLocation').textContent;
    const encodedLocation = encodeURIComponent(location);
    window.open(`https://www.google.com/maps/search/${encodedLocation}`, '_blank');
}

// Close modal when clicking outside
window.addEventListener('click', (e) => {
    const modal = document.getElementById('reportModal');
    if (e.target === modal) {
        closeReportModal();
    }
});
</script>
@endpush
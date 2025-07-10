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
        <div class="search-filter">
        <form method="GET" action="{{ route('shelter.stray-reports') }}" class="search-form">
            <div class="search-box-container">
                <input type="text" 
                    name="search" 
                    class="search-input" 
                    placeholder="Search by report ID, location, or animal type..." 
                    value="{{ request('search') }}">
                <i class="fas fa-search search-icon"></i>
            </div>
            
            <select name="status" class="filter-select">
                <option value="">All Status</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="investigating" {{ request('status') == 'investigating' ? 'selected' : '' }}>Investigating</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
            
            <button type="submit" class="btn btn-search">
                <i class="fas fa-search"></i>
                Search
            </button>
            
            @if(request('search') || request('status'))
                <a href="{{ route('shelter.stray-reports') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Clear
                </a>
            @endif
        </form>
    </div>
</div>
        <!-- Reports Grid -->
        <div class="report-grid">
            @forelse($reports as $report)
                @php
                    // Handle all cases: array, JSON string, or null
                    if (is_array($report->image_url)) {
                        $imageUrls = $report->image_url;
                    } elseif ($report->image_url) {
                        $imageUrls = json_decode($report->image_url, true);
                    } else {
                        $imageUrls = [];
                    }

                    // Ensure we have a valid array
                    $imageUrls = is_array($imageUrls) ? $imageUrls : [];
                    $firstImage = !empty($imageUrls) ? $imageUrls[0] : null;
                    $imageCount = count($imageUrls);
                @endphp

                <div class="report-card {{ !$report->is_read ? 'unread' : '' }}"
                    data-report-id="{{ $report->report_id }}"
                    data-image="{{ !empty($imageUrls) ? $imageUrls[0] : 'https://via.placeholder.com/150' }}"
                    data-images="{{ json_encode($imageUrls) }}"
                    data-description="{{ $report->description }}"
                    data-location="{{ $report->location }}"
                    data-status="{{ $report->status }}"
                    data-reported-at="{{ $report->reported_at ? \Carbon\Carbon::parse($report->reported_at)->format('F d, Y') : '' }}"
                    data-animal-type="{{ $report->animal_type ?? '' }}"
                    data-reporter="{{ $report->reporter_name }}"
                    data-reporter-contact="{{ $report->reporter_email }}"
                    data-sent-at="{{ $report->sent_at ? \Carbon\Carbon::parse($report->sent_at)->format('F d, Y g:i A') : '' }}"
                >   
                    @if($firstImage)
                        <div class="image-container">
                            <img src="{{ $firstImage }}" alt="Stray Animal" class="report-image">
                        </div>
                    @else
                        <img src="https://via.placeholder.com/150" alt="No Image" class="report-image">
                    @endif
                    @if(!$report->is_read)
                        <div class="unread-indicator"></div>
                    @endif
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

<!-- Report Details Modal -->
<div class="report-modal" id="reportModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Stray Report Details</h2>
            <button class="close-modal" onclick="closeReportModal()">&times;</button>
        </div>
        <div class="report-header">
            <div class="report-images">
            <div class="main-image-container">
                <img id="modalReportImage" src="" alt="Stray Animal" class="report-image">
            </div>
            <div class="additional-images" id="additionalImages" style="display: none;">
                    <div class="image-gallery">
                    </div>
                </div>
            </div>
            <div class="report-header-info">
                <div class="info-block">
                    <div class="info-label">Report ID:</div>
                    <div class="info-value" id="reportId"></div>
                </div>
                <div class="status-badge" id="reportStatus"></div>
            </div>
        </div>

        <div class="report-info">
            <div class="info-block">
                <div class="info-label">📍 Location</div>
                <div class="info-value" id="reportLocation"></div>
            </div>
            <div class="info-block">
                <div class="info-label">🐕 Animal Type</div>
                <div class="info-value" id="animalType"></div>
            </div>
            <div class="info-block">
                <div class="info-label">👤 Reporter</div>
                <div class="info-value" id="reporterName"></div>
                <div class="info-value small-text" id="reporterContact"></div>
            </div>
            <div class="info-block">
                <div class="info-label">📅 Date Reported</div>
                <div class="info-value" id="reportDate"></div>
            </div>
            <div class="info-block">
                <div class="info-label">📨 Sent to You</div>
                <div class="info-value" id="sentAt"></div>
            </div>
        </div>

        <div class="report-description">
            <h3>Description</h3>
            <p id="reportDescription"></p>
        </div>

        <div class="report-actions">
            <button class="btn btn-primary" onclick="acceptReport()">
                Mark as Accepted
            </button>
            <button class="btn btn-info" onclick="getDirections()">
                Get Directions
            </button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// notification system 
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

    if (card.classList.contains('unread')) {
        markAsRead(card.dataset.reportId);
        card.classList.remove('unread');
        const indicator = card.querySelector('.unread-indicator');
        if (indicator) indicator.remove();
    }

    const imageData = card.dataset.images;
    let images = [];
    
    try {
        images = imageData ? JSON.parse(imageData) : [];
    } catch (e) {
        console.error('Error parsing images:', e);
        images = [];
    }

    const mainImage = card.dataset.image;
    const additionalImagesContainer = document.getElementById('additionalImages');
    const imageGallery = additionalImagesContainer.querySelector('.image-gallery');

    document.getElementById('modalReportImage').src = mainImage;

    if (images.length > 1) {
        additionalImagesContainer.style.display = 'block';
        imageGallery.innerHTML = '';

        images.forEach((imageUrl, index) => {
            const img = document.createElement('img');
            img.src = imageUrl;
            img.alt = `Report image ${index + 1}`;
            img.className = 'gallery-image';
            if (imageUrl === mainImage) {
                img.classList.add('active');
            }

            img.addEventListener('click', () => {
                document.getElementById('modalReportImage').src = imageUrl;
                
                imageGallery.querySelectorAll('.gallery-image').forEach(img => img.classList.remove('active'));
                img.classList.add('active');
            });

            imageGallery.appendChild(img);
        });
    } else {
        additionalImagesContainer.style.display = 'none';
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
    const acceptBtn = document.querySelector('.btn-primary');
    
    // Disable the button immediately to prevent double clicks
    acceptBtn.disabled = true;
    acceptBtn.textContent = 'Processing...';
    
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
            return response.json().then(data => {
                throw new Error(data.message || 'HTTP error!');
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Report accepted successfully!', 'success');
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
                
                // Mark the card as handled to prevent future clicks
                reportCard.classList.add('report-handled');
            }
            
            // Reload page after showing notification
            setTimeout(() => location.reload(), 2000);
        } else {
            throw new Error(data.message || 'Failed to accept report');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Check if it's an "already accepted" error
        if (error.message.includes('already accepted')) {
            showNotification(error.message, 'warning');
            
            // Update button to show it's already accepted
            acceptBtn.textContent = 'Already Accepted';
            acceptBtn.classList.remove('btn-primary');
            acceptBtn.classList.add('btn-success');
            
            // Update status in the grid
            const reportCard = document.querySelector(`[data-report-id="${reportId}"]`);
            if (reportCard) {
                reportCard.dataset.status = 'accepted';
                const statusBadge = reportCard.querySelector('.status-badge');
                if (statusBadge) {
                    statusBadge.className = 'status-badge status-accepted';
                    statusBadge.textContent = 'Accepted';
                }
                reportCard.classList.add('report-handled');
            }
        } else {
            showNotification('Error: ' + error.message, 'error');
            
            // Re-enable button on other errors
            acceptBtn.disabled = false;
            acceptBtn.textContent = 'Mark as Accepted';
        }
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
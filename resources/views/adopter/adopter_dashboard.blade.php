@extends('layouts.adopter')

@section('title', 'Adopter Dashboard - PawMatch')

@section('adopter-content')

<main class="main-content">
    <!-- Centering Wrapper -->
    <div class="content-wrapper">
        <!-- Top Bar -->
        <div class="top-bar" style="margin-top: 2rem;">
            <div class="welcome-section">
                <h1>Welcome, {{ $user->first_name ?? 'User' }}!</h1>
                <p>Here's what's happening with your pet adoption journey</p>
            </div>
            <div class="profile-section" style="display: flex; align-items: center; gap: 2rem; position: relative; z-index: 3000;">
                <div class="profile-img">
                    <img src="{{ $user->profile_image }}" alt="{{ $user->first_name }} {{ $user->last_name }}'s profile photo" style="width: 100%; height: 100%; border-radius: 50%; font-family: 'Inter', sans-serif;" />
                </div>
                <!-- Notification Bell -->
                <div class="notification-bell-wrapper" style="position: relative; z-index: 3010; pointer-events: auto;">
                    <div id="notificationBell" class="notification-bell" style="z-index: 3020; pointer-events: auto;">
                        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="#4a90e2" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-bell">
                            <path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9"></path>
                            <path d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                        </svg>
                        @php $unreadCount = auth()->user()->notifications()->where('is_read', false)->count(); @endphp
                        @if($unreadCount > 0)
                            <span id="notificationBadge" class="notification-badge">{{ $unreadCount }}</span>
                        @endif
                    </div>
                    <div id="notificationDropdown" class="notification-dropdown notification-dropdown-fixed" style="z-index: 3030; pointer-events: auto;">
                        <div class="notification-dropdown-header">Notifications</div>
                        @php $notifications = auth()->user()->notifications()->latest()->take(10)->get(); @endphp
                        @forelse($notifications as $notification)
                            <div class="notification-item" data-id="{{ $notification->id }}" style="{{ !$notification->is_read ? 'background:#eaf3fb;' : '' }}">
                                <div class="notification-title">{{ $notification->title }}</div>
                                <div class="notification-message">{{ $notification->message }}</div>
                                <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                            </div>
                        @empty
                            <div class="notification-empty">No notifications</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <!-- Favorite Pets -->
            <div class="content-card" id="favoritePetsSection">
                <div class="card-header">
                    <div class="card-header-row">
                        <h2>Favorite Pets</h2>
                        <a href="{{ route('adopter.pet-listings') }}" class="btn btn-outline card-header-btn" aria-label="Find more pets">Find More</a>
                    </div>
                </div>
                <div class="pet-grid">
                    @forelse($favoritePets as $pet)
                        <a href="{{ route('adopter.pet-listings') }}?pet_id={{ $pet->pet_id ?? $pet->id }}" class="pet-card favorite-pet-card" data-pet-id="{{ $pet->pet_id ?? $pet->id }}" style="display: flex; align-items: center; gap: 1rem; padding: 0.5rem 0; border: none; box-shadow: none; cursor: pointer; text-decoration: none;">
                            <img src="{{ $pet->images->first()->image_url ?? asset('images/default-pet.png') }}" alt="{{ $pet->name }}" class="pet-image" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px; font-family: 'Inter', sans-serif; margin: 0;" />
                            <div class="pet-info" style="flex: 1;">
                                <div class="pet-name" style="color:#1a1a1a; font-size: 1rem; font-weight: 500;">{{ $pet->name }}</div>
                            </div>
                        </a>
                    @empty
                        <div class="no-pets-message">
                            <p>No favorite pets yet.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Applications -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-header-row">
                        <h2>Recent Applications</h2>
                        <a href="{{ route('adopter.application-status') }}" class="btn btn-outline card-header-btn" aria-label="View all applications">View All</a>
                    </div>
                </div>
                <ul class="application-list">
                    @forelse($recentApplications as $application)
                        <li class="application-item">
                            <a href="{{ route('adopter.application-status') }}?application_id={{ $application->id }}" style="text-decoration: none; color: inherit; display: block;">
                                <div class="pet-name">{{ $application->pet->name ?? 'Unknown Pet' }} - {{ $application->pet->breed ?? '' }}</div>
                                <div class="pet-details">{{ $application->pet->shelter->shelter_name ?? 'Unknown Shelter' }}</div>
                                <span class="status status-{{ $application->status }}">
                                    @if($application->status === 'pending')
                                        Application Pending
                                    @elseif($application->status === 'approved')
                                        Approved - Schedule Visit
                                    @elseif($application->status === 'completed')
                                        Adoption Completed
                                    @else
                                        {{ ucfirst($application->status) }}
                                    @endif
                                </span>
                            </a>
                        </li>
                    @empty
                        <li class="application-item">
                            <div class="pet-name">No recent applications.</div>
                        </li>
                    @endforelse
                </ul>
            </div>

            <!-- Recent Messages -->
            <div class="content-card">
                <div class="card-header">
                    <div class="card-header-row">
                        <h2>Recent Messages</h2>
                        <a href="{{ route('adopter.messages') }}" class="btn btn-outline card-header-btn" aria-label="View all messages">View All</a>
                    </div>
                </div>
                <ul class="message-list">
                    @forelse($recentMessages as $message)
                        <li class="message-item">
                            <div class="message-header">
                                <span class="message-sender">{{ $message->sender->name ?? 'Unknown Sender' }}</span>
                                <span class="message-time">{{ $message->created_at->format('g:i A') }}</span>
                            </div>
                            <div class="message-preview">
                                {{ $message->content }}
                            </div>
                        </li>
                    @empty
                        <li class="message-item">
                            <div class="message-header">
                                <span class="message-sender">No recent messages.</span>
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div><!-- .content-wrapper -->
</main>
@endsection

@push('styles')
<style>
.notification-bell-wrapper {
    position: relative;
    display: flex;
    align-items: center;
    z-index: 3010;
    pointer-events: auto;
}
.notification-bell {
    cursor: pointer;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f3f4f6;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    transition: background 0.2s;
    box-shadow: 0 2px 8px rgba(74, 144, 226, 0.08);
    z-index: 3020;
    pointer-events: auto;
}
.notification-bell:hover {
    background: #eaf3fb;
}
.notification-badge {
    position: absolute;
    top: 4px;
    right: 4px;
    background: #e3342f;
    color: #fff;
    border-radius: 50%;
    padding: 2px 7px;
    font-size: 0.8rem;
    font-weight: bold;
    box-shadow: 0 1px 4px rgba(74, 144, 226, 0.12);
}
.notification-dropdown-fixed {
    position: fixed !important;
    z-index: 3030 !important;
    box-shadow: 0 4px 24px rgba(74, 144, 226, 0.13);
    width: 340px;
    max-width: 90vw;
    border-radius: 12px;
    background: #fff;
    border: 1.5px solid #e5e7eb;
    max-height: 420px;
    overflow-y: auto;
    display: none;
    animation: fadeInNotif 0.18s;
    pointer-events: auto;
}
.notification-dropdown-fixed.show {
    display: block;
}
@media (max-width: 900px) {
    .notification-dropdown-fixed {
        left: 50% !important;
        transform: translateX(-50%) !important;
        width: 90vw !important;
        max-width: 95vw !important;
        border-radius: 16px !important;
        top: 80px !important;
        margin-top: 0 !important;
    }
    .main-content, .content-wrapper {
        padding-left: 1.2rem !important;
        padding-right: 1.2rem !important;
    }
    .top-bar {
        margin-left: 0.5rem !important;
    }
}
@keyframes fadeInNotif {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
.notification-dropdown-header {
    padding: 0.7rem 1.2rem;
    font-weight: 700;
    color: #2563eb;
    border-bottom: 1px solid #e5e7eb;
    background: #f7fafd;
    border-radius: 12px 12px 0 0;
}
.notification-item {
    padding: 0.9rem 1.2rem 0.7rem 1.2rem;
    cursor: pointer;
    border-bottom: 1px solid #f1f1f1;
    transition: background 0.18s;
    border-radius: 0;
}
.notification-item:last-child {
    border-bottom: none;
}
.notification-item:hover {
    background: #f3f4f6;
}
.notification-title {
    font-weight: 600;
    color: #1a1a1a;
    font-size: 1.05rem;
}
.notification-message {
    font-size: 0.97rem;
    color: #4b5563;
    margin-top: 2px;
}
.notification-time {
    font-size: 0.8rem;
    color: #6b7280;
    margin-top: 2px;
}
.notification-empty {
    padding: 1.2rem;
    text-align: center;
    color: #b0b7c3;
    font-size: 1rem;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const bell = document.getElementById('notificationBell');
    const dropdown = document.getElementById('notificationDropdown');
    function positionDropdown() {
        if (!bell || !dropdown) return;
        if (window.innerWidth <= 900) {
            dropdown.style.left = '50%';
            dropdown.style.transform = 'translateX(-50%)';
            dropdown.style.top = '80px';
            dropdown.style.width = '90vw';
            dropdown.style.maxWidth = '95vw';
            dropdown.style.borderRadius = '16px';
        } else {
            const bellRect = bell.getBoundingClientRect();
            dropdown.style.left = (bellRect.right + 8) + 'px';
            dropdown.style.top = (bellRect.top + bellRect.height/2 - 8) + 'px';
            dropdown.style.width = '340px';
            dropdown.style.maxWidth = '90vw';
            dropdown.style.borderRadius = '12px';
            dropdown.style.transform = 'none';
        }
    }
    if (bell && dropdown) {
        bell.addEventListener('click', function(e) {
            e.stopPropagation();
            positionDropdown();
            dropdown.classList.toggle('show');
        });
        document.addEventListener('click', function() {
            dropdown.classList.remove('show');
        });
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        // Mark notification as read when clicked
        dropdown.querySelectorAll('.notification-item').forEach(function(item) {
            item.addEventListener('click', function() {
                const notifId = this.getAttribute('data-id');
                fetch(`/notifications/${notifId}/read`, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name=\"csrf-token\"').content } })
                    .then(() => { this.style.background = '#fff'; this.style.fontWeight = 'normal'; });
            });
        });
    }
    window.addEventListener('resize', function() {
        if (dropdown && dropdown.classList.contains('show')) {
            positionDropdown();
        }
    });
});
</script>
@endpush
<link rel="stylesheet" href="{{ asset('css/shared/notification-bell.css') }}">
<style>
@media (min-width: 901px) {
  .notification-dropdown-fixed {
    left: unset !important;
    transform: none !important;
  }
}
</style>
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
    <div id="notificationDropdown" class="notification-dropdown notification-dropdown-fixed" style="z-index: 3030; pointer-events: auto; display: none;">
        <div class="notification-dropdown-header">Notifications</div>
        @php $notifications = auth()->user()->notifications()->latest()->take(10)->get(); @endphp
        @forelse($notifications as $notification)
            <a href="{{ $notification->action_url ?? '#' }}" class="notification-item" data-id="{{ $notification->id }}" style="{{ !$notification->is_read ? 'background:#eaf3fb;' : '' }}; position: relative;">
                <div class="notification-title">{{ $notification->title }}</div>
                <div class="notification-message">{{ $notification->message }}</div>
                <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
                <button class="remove-notification-btn" data-id="{{ $notification->id }}" style="position: absolute; top: 10px; right: 10px; background: none; border: none; color: #e3342f; font-size: 1.1em; cursor: pointer;">&times;</button>
            </a>
        @empty
            <div class="notification-empty">No notifications</div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function attachNotificationEvents() {
    const dropdown = document.getElementById('notificationDropdown');
    // Mark notification as read when clicked
    dropdown.querySelectorAll('.notification-item').forEach(function(item) {
        item.addEventListener('click', function(e) {
            // Prevent if remove button is clicked
            if (e.target.classList.contains('remove-notification-btn')) return;
            const notifId = this.getAttribute('data-id');
            const url = this.getAttribute('href');
            console.debug('Notification clicked:', { notifId, url }); // DEBUG
            if (url && url !== '#') {
                e.preventDefault();
                fetch(`/notifications/${notifId}/read`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                }).then(() => {
                    window.location.href = url;
                }).catch(() => {
                    // Always redirect even if AJAX fails
                    window.location.href = url;
                });
            }
        });
    });
    // Remove notification
    dropdown.querySelectorAll('.remove-notification-btn').forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            e.stopPropagation();
            e.preventDefault();
            const notifId = this.getAttribute('data-id');
            fetch(`/notifications/${notifId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            }).then(() => {
                // Remove the notification from the DOM
                const notifItem = btn.closest('.notification-item');
                if (notifItem) notifItem.remove();
                // Optionally, update badge count
                const badge = document.getElementById('notificationBadge');
                if (badge) {
                    let count = parseInt(badge.textContent, 10);
                    if (!isNaN(count) && count > 0) {
                        badge.textContent = count - 1;
                        if (count - 1 <= 0) badge.remove();
                    }
                }
                // If no notifications left, show empty message
                if (!dropdown.querySelector('.notification-item') && !dropdown.querySelector('.notification-empty')) {
                    dropdown.innerHTML += '<div class="notification-empty">No notifications</div>';
                }
            });
        });
    });
}
document.addEventListener('DOMContentLoaded', function() {
    const bell = document.getElementById('notificationBell');
    const dropdown = document.getElementById('notificationDropdown');
    function positionDropdown() {
        if (!bell || !dropdown) return;
        // Show dropdown but keep it hidden for measurement
        dropdown.style.visibility = 'hidden';
        dropdown.style.display = 'block';
        // Wait for layout to stabilize
        requestAnimationFrame(function() {
            if (window.innerWidth <= 900) {
                dropdown.style.position = 'fixed';
                dropdown.style.setProperty('left', '12px', 'important');
                dropdown.style.setProperty('right', '12px', 'important');
                dropdown.style.setProperty('top', '60px', 'important');
                dropdown.style.setProperty('width', 'auto', 'important');
                dropdown.style.setProperty('max-width', '500px', 'important');
                dropdown.style.setProperty('min-width', '200px', 'important');
                dropdown.style.setProperty('background', '#fff', 'important');
                dropdown.style.setProperty('border', '1.5px solid #e5e7eb', 'important');
                dropdown.style.setProperty('border-radius', '14px', 'important');
                dropdown.style.setProperty('transform', '', 'important');
            } else {
                dropdown.style.position = 'absolute';
                dropdown.style.setProperty('left', '50%', 'important');
                dropdown.style.setProperty('top', '100%', 'important');
                dropdown.style.setProperty('transform', 'translateX(-50%)', 'important');
                dropdown.style.setProperty('width', '340px', 'important');
                dropdown.style.setProperty('max-width', '90vw', 'important');
                dropdown.style.setProperty('border-radius', '12px', 'important');
            }
            dropdown.style.visibility = '';
            dropdown.style.display = '';
        });
    }
    if (bell && dropdown) {
        bell.addEventListener('click', function(e) {
            e.stopPropagation();
            positionDropdown();
            dropdown.classList.toggle('show');
            attachNotificationEvents();
        });
        document.addEventListener('click', function() {
            dropdown.classList.remove('show');
        });
        dropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        // Attach events initially
        attachNotificationEvents();
    }
    window.addEventListener('resize', function() {
        if (dropdown && dropdown.classList.contains('show')) {
            positionDropdown();
        }
    });
});
</script>
@endpush 
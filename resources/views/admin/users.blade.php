@extends('layouts.admin')

@section('title', 'User Management - PawMatch Admin')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/admin/user-management.css') }}">
@endpush

@section('content')
<!-- Main Content -->
<main class="main-content">
    <div class="content-wrapper">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="welcome-section">
                <h1>User Management</h1>
                <p>Manage and monitor user accounts</p>
            </div>
            <div class="profile-section">
                <div class="profile-img">
                    @if(auth()->user()->profile_photo_path)
                        <img src="{{ asset(auth()->user()->profile_photo_path) }}" alt="Admin Profile">
                    @else
                        <div style="width: 40px; height: 40px; background: #e5e7eb; border-radius: 50%; display: flex; align-items: center; justify-content: center; color: #6b7280; font-weight: 600;">
                            {{ auth()->user()->initials ?? 'A' }}
                        </div>
                    @endif
                </div>
                <div class="profile-info">
                    <strong>{{ auth()->user()->name ?? 'Admin' }}</strong>
                    <div style="font-size: 0.875rem; color: #6b7280;">{{ ucfirst(auth()->user()->role ?? 'admin') }}</div>
                </div>
            </div>
        </div>

        <!-- User Management Content -->
        <div class="content-card">
            <div class="card-header">
                <h2>All Users ({{ $stats['total'] }})</h2>
            </div>

            <!-- Search and Filter -->
            <form method="GET" action="{{ route('admin.users') }}" class="search-filter">
                <input type="text" 
                       name="search" 
                       class="search-box" 
                       placeholder="Search users..." 
                       value="{{ request('search') }}">
                
                <select name="role" class="filter-select">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="shelter" {{ request('role') == 'shelter' ? 'selected' : '' }}>Shelter</option>
                    <option value="adopter" {{ request('role') == 'adopter' ? 'selected' : '' }}>Adopter</option>
                    <option value="rescuer" {{ request('role') == 'rescuer' ? 'selected' : '' }}>Rescuer</option>
                </select>
                
                <select name="status" class="filter-select">
                    <option value="">All Status</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="banned" {{ request('status') == 'banned' ? 'selected' : '' }}>Banned</option>
                </select>
                
                <button type="submit" class="btn btn-outline">
                    <i class="fas fa-search"></i> Search
                </button>
                
                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users') }}" class="btn btn-outline">
                        <i class="fas fa-times"></i> Clear
                    </a>
                @endif
            </form>

            <!-- User Statistics -->
            <div class="user-stats">
                <div class="user-stats-card user-stats-total">
                    <div class="user-stats-value">{{ $stats['total'] ?? 0 }}</div>
                    <div class="user-stats-label">Total Users</div>
                </div>
                <div class="user-stats-card user-stats-active">
                    <div class="user-stats-value">{{ $stats['active'] ?? 0 }}</div>
                    <div class="user-stats-label">Active</div>
                </div>
                <div class="user-stats-card user-stats-inactive">
                    <div class="user-stats-value">{{ $stats['inactive'] ?? 0 }}</div>
                    <div class="user-stats-label">Inactive</div>
                </div>
                <div class="user-stats-card user-stats-banned">
                    <div class="user-stats-value">{{ $stats['banned'] ?? 0 }}</div>
                    <div class="user-stats-label">Banned</div>
                </div>
            </div>

            <!-- Users Table -->
            <div class="user-table-container">
            <table class="user-table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th class="user-actions-header">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td class="user-id">#{{ str_pad($user->user_id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td class="user-name-cell">
                            <div class="user-avatar">
                                @if($user->profile_photo_path)
                                    <img src="{{ asset($user->profile_photo_path) }}" alt="User Avatar">
                                @else
                                    <span class="user-initials">{{ $user->initials }}</span>
                                @endif
                            </div>
                            <div class="user-name-info">
                                <div class="user-name">{{ $user->name }}</div>
                                <div class="user-phone">{{ $user->phone_number ?? 'No phone' }}</div>
                            </div>
                        </td>
                        <td class="user-email-cell">
                            <div class="user-email">{{ $user->email }}</div>
                            <div class="user-joined">Joined {{ $user->created_at->format('M d, Y') }}</div>
                        </td>
                        <td class="user-role-cell">
                            <span class="user-role user-role-{{ strtolower($user->role) }}">{{ ucfirst($user->role) }}</span>
                        </td>
                        <td class="user-status-cell">
                            @if($user->status === 'banned')
                                <span class="user-status user-status-banned">Banned</span>
                            @elseif($user->status === 'active')
                                <span class="user-status user-status-active">Active</span>
                            @else
                                <span class="user-status user-status-inactive">Inactive</span>
                            @endif
                        </td>
                        <td class="user-actions">
                            @if($user->user_id !== auth()->user()->user_id)
                                <button class="user-action-btn view" title="View Details" onclick="viewUser({{ $user->user_id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="user-action-btn unban" title="Unban User" onclick="userAction({{ $user->user_id }}, 'unban')">
                                    <i class="fas fa-user-check"></i>
                                </button>
                                <button class="user-action-btn ban" title="Ban User" onclick="userAction({{ $user->user_id }}, 'ban')">
                                    <i class="fas fa-user-slash"></i>
                                </button>
                                <button class="user-action-btn deactivate" title="Deactivate User" onclick="userAction({{ $user->user_id }}, 'deactivate')">
                                    <i class="fas fa-user-times"></i>
                                </button>
                                <button class="user-action-btn activate" title="Activate User" onclick="userAction({{ $user->user_id }}, 'activate')">
                                    <i class="fas fa-user-check"></i>
                                </button>
                                <button class="user-action-btn delete" title="Delete User" onclick="userAction({{ $user->user_id }}, 'delete')">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="user-table-empty">
                            <i class="fas fa-users"></i>
                            <div>No users found</div>
                            @if(request()->hasAny(['search', 'role', 'status']))
                                <div class="user-table-clear">
                                    <a href="{{ route('admin.users') }}" class="btn btn-outline">Clear Filters</a>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="pagination">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</main>

<!-- View User Modal -->
<div class="modal" id="viewUserModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>User Details</h2>
            <button class="close-modal" onclick="closeViewUserModal()">&times;</button>
        </div>
        <div class="modal-body">
            <div class="user-info-header">
                <div class="user-avatar-large" id="viewUserAvatar">
                    <!-- Avatar will be populated by JavaScript -->
                </div>
                <div>
                    <h3 id="viewUserName">User Name</h3>
                    <div class="user-status">
                        <span class="status-dot" id="viewUserStatusDot"></span>
                        <span id="viewUserStatus">Active</span>
                    </div>
                </div>
            </div>

            <div class="tab-navigation">
                <button class="tab-button active" onclick="switchTab('info')">Information</button>
            </div>

            <div id="infoTab" class="tab-content active">
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Email</div>
                        <div class="info-value" id="viewUserEmail">user@example.com</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Phone</div>
                        <div class="info-value" id="viewUserPhone">+1234567890</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Role</div>
                        <div class="info-value" id="viewUserRole">Admin</div>
                    </div>
                    <div class="info-item">
                        <div class="info-label">Joined</div>
                        <div class="info-value" id="viewUserJoined">Jan 1, 2024</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ban User Modal -->
<div class="modal" id="banUserModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Ban User</h2>
            <button class="close-modal" onclick="closeBanUserModal()">&times;</button>
        </div>
        <form id="banUserForm" onsubmit="handleBanUser(event)">
            <div class="modal-body">
                <p style="margin-bottom: 1rem; color: #6b7280;">Please provide a reason for banning this user:</p>
                <div class="form-group">
                    <label for="banReason">Reason</label>
                    <textarea id="banReason" name="reason" rows="4" required placeholder="Enter the reason for banning this user..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeBanUserModal()">Cancel</button>
                <button type="submit" class="btn" style="background: #ef4444; color: white;">Ban User</button>
            </div>
        </form>
    </div>
</div>

<!-- Toast Container -->
<div id="toast-container" class="fixed top-6 right-6 z-50 space-y-2"></div>
@endsection

@push('scripts')
<script>
let currentUserId = null;

// View User Modal Functions
function viewUser(userId) {
    fetch(`{{ url('admin/users') }}/${userId}`)
        .then(response => response.json())
        .then(data => {
            // Populate user info
            document.getElementById('viewUserName').textContent = data.name;
            document.getElementById('viewUserEmail').textContent = data.email;
            document.getElementById('viewUserPhone').textContent = data.phone_number || 'No phone';
            document.getElementById('viewUserRole').textContent = data.role.charAt(0).toUpperCase() + data.role.slice(1);
            document.getElementById('viewUserJoined').textContent = new Date(data.created_at).toLocaleDateString();
            
            // Set status
            const statusElement = document.getElementById('viewUserStatus');
            const statusDot = document.getElementById('viewUserStatusDot');
            
            if (data.is_banned) {
                statusElement.textContent = 'Banned';
                statusDot.className = 'status-dot inactive';
            } else if (data.status) {
                statusElement.textContent = 'Active';
                statusDot.className = 'status-dot active';
            } else {
                statusElement.textContent = 'Inactive';
                statusDot.className = 'status-dot inactive';
            }
            
            // Set avatar
            const avatarElement = document.getElementById('viewUserAvatar');
            if (data.profile_photo_path) {
                avatarElement.innerHTML = `<img src="${data.profile_photo_path}" alt="User Avatar">`;
            } else {
                avatarElement.textContent = data.initials;
            }
            
            document.getElementById('viewUserModal').style.display = 'flex';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading user details');
        });
}

function closeViewUserModal() {
    document.getElementById('viewUserModal').style.display = 'none';
}

function switchTab(tabName) {
    // Hide all tabs
    document.querySelectorAll('.tab-content').forEach(tab => {
        tab.classList.remove('active');
    });
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });

    // Show selected tab
    document.getElementById(tabName + 'Tab').classList.add('active');
    event.target.classList.add('active');
}

// Ban User Modal Functions
function banUser(userId) {
    currentUserId = userId;
    document.getElementById('banUserModal').style.display = 'flex';
}

function closeBanUserModal() {
    document.getElementById('banUserModal').style.display = 'none';
    document.getElementById('banUserForm').reset();
    currentUserId = null;
}

function handleBanUser(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());
    
    fetch(`{{ url('admin/users') }}/${currentUserId}/ban`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('User banned successfully!');
            closeBanUserModal();
            window.location.reload();
        } else {
            alert(data.message || 'Error banning user');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error banning user');
    });
}

// Unban User
function unbanUser(userId) {
    if (confirm('Are you sure you want to unban this user?')) {
        fetch(`{{ url('admin/users') }}/${userId}/unban`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User unbanned successfully!');
                window.location.reload();
            } else {
                alert(data.message || 'Error unbanning user');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error unbanning user');
        });
    }
}

// Delete User
function deleteUser(userId) {
    if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        fetch(`{{ url('admin/users') }}/${userId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User deleted successfully!');
                window.location.reload();
            } else {
                alert(data.message || 'Error deleting user');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting user');
        });
    }
}

// Export Users
function exportUsers() {
    const params = new URLSearchParams(window.location.search);
    window.location.href = `{{ route('admin.users.export') }}?${params.toString()}`;
}

// Close modals when clicking outside
window.onclick = function(event) {
    const modals = document.querySelectorAll('.modal');
    modals.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
            // Reset forms
            const forms = modal.querySelectorAll('form');
            forms.forEach(form => form.reset());
            currentUserId = null;
        }
    });
}

// Auto-submit search form on input change
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    const selectInputs = document.querySelectorAll('select[name="role"], select[name="status"]');
    
    // Auto submit on select change
    selectInputs.forEach(select => {
        select.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });
    
    // Debounced search
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                this.closest('form').submit();
            }, 500);
        });
    }
});

function showToast(message, type = 'success') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');
    toast.className = `px-4 py-3 rounded shadow text-white font-semibold flex items-center space-x-2 animate-fadeIn ${type === 'success' ? 'bg-green-600' : 'bg-red-600'}`;
    toast.innerHTML = `<span>${message}</span>`;
    container.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('animate-fadeOut');
        setTimeout(() => toast.remove(), 500);
    }, 2000);
}

function userAction(userId, action) {
    let url = '';
    let method = 'POST';
    if (action === 'activate') url = `{{ url('admin/users') }}/${userId}/activate`;
    else if (action === 'deactivate') url = `{{ url('admin/users') }}/${userId}/deactivate`;
    else if (action === 'ban') url = `{{ url('admin/users') }}/${userId}/ban`;
    else if (action === 'unban') url = `{{ url('admin/users') }}/${userId}/unban`;
    else if (action === 'delete') { url = `{{ url('admin/users') }}/${userId}`; method = 'DELETE'; }
    if (action === 'delete' && !confirm('Are you sure you want to delete this user? This action cannot be undone.')) return;
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showToast(data.message, 'success');
            setTimeout(() => window.location.reload(), 1200);
        } else {
            showToast(data.message || 'Action failed', 'error');
        }
    })
    .catch(() => showToast('Action failed', 'error'));
}

// Toast animation
const style = document.createElement('style');
style.innerHTML = `
@keyframes fadeIn { from { opacity: 0; transform: translateY(-10px);} to { opacity: 1; transform: translateY(0);} }
@keyframes fadeOut { from { opacity: 1;} to { opacity: 0; transform: translateY(-10px);} }
.animate-fadeIn { animation: fadeIn 0.3s; }
.animate-fadeOut { animation: fadeOut 0.5s; }
`;
document.head.appendChild(style);
</script>
@endpush
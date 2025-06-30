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
                <button class="btn btn-primary" onclick="openAddUserModal()">
                    <i class="fas fa-plus"></i> Add New User
                </button>
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
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
                <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 600; color: #1f2937;">{{ $stats['total'] }}</div>
                    <div style="color: #6b7280; font-size: 0.875rem;">Total Users</div>
                </div>
                <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 600; color: #10b981;">{{ $stats['active'] }}</div>
                    <div style="color: #6b7280; font-size: 0.875rem;">Active</div>
                </div>
                <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 600; color: #f59e0b;">{{ $stats['inactive'] }}</div>
                    <div style="color: #6b7280; font-size: 0.875rem;">Inactive</div>
                </div>
                <div style="background: #f9fafb; padding: 1rem; border-radius: 8px; text-align: center;">
                    <div style="font-size: 1.5rem; font-weight: 600; color: #ef4444;">{{ $stats['banned'] }}</div>
                    <div style="color: #6b7280; font-size: 0.875rem;">Banned</div>
                </div>
            </div>

            <!-- Users Table -->
            <table class="user-table">
                <thead>
                    <tr>
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>#{{ str_pad($user->user_id, 3, '0', STR_PAD_LEFT) }}</td>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar">
                                    @if($user->profile_photo_path)
                                        <img src="{{ asset($user->profile_photo_path) }}" alt="User Avatar">
                                    @else
                                        {{ $user->initials }}
                                    @endif
                                </div>
                                <div class="user-info">
                                    <div class="user-name">{{ $user->name }}</div>
                                    <div class="user-id">{{ $user->phone_number ?? 'No phone' }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="contact-info">
                                <div class="contact-email">{{ $user->email }}</div>
                                <div class="contact-phone">Joined {{ $user->created_at->format('M d, Y') }}</div>
                            </div>
                        </td>
                        <td>
                            <span class="role-badge role-{{ strtolower($user->role) }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td>
                            @if($user->is_banned)
                                <span class="status-badge" style="background: #fee2e2; color: #991b1b;">Banned</span>
                            @elseif($user->status)
                                <span class="status-badge status-active">Active</span>
                            @else
                                <span class="status-badge status-inactive">Inactive</span>
                            @endif
                        </td>
                        <td class="action-buttons">
                            <button class="btn-icon" title="View Details" onclick="viewUser({{ $user->user_id }})">
                                <i class="fas fa-eye"></i>
                            </button>
                            @if($user->role !== 'admin')
                                @if($user->is_banned)
                                    <button class="btn-icon" title="Unban User" onclick="unbanUser({{ $user->user_id }})">
                                        <i class="fas fa-user-check" style="color: #10b981;"></i>
                                    </button>
                                @else
                                    <button class="btn-icon" title="Ban User" onclick="banUser({{ $user->user_id }})">
                                        <i class="fas fa-user-slash" style="color: #ef4444;"></i>
                                    </button>
                                @endif
                                <button class="btn-icon" title="Delete User" onclick="deleteUser({{ $user->user_id }})">
                                    <i class="fas fa-trash" style="color: #ef4444;"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 2rem; color: #6b7280;">
                            <i class="fas fa-users" style="font-size: 2rem; margin-bottom: 1rem; color: #d1d5db;"></i>
                            <div>No users found</div>
                            @if(request()->hasAny(['search', 'role', 'status']))
                                <div style="margin-top: 0.5rem;">
                                    <a href="{{ route('admin.users') }}" class="btn btn-outline">Clear Filters</a>
                                </div>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="pagination">
                    {{ $users->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</main>

<!-- Add User Modal -->
<div class="modal" id="addUserModal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Add New User</h2>
            <button class="close-modal" onclick="closeAddUserModal()">&times;</button>
        </div>
        <form id="addUserForm" onsubmit="handleAddUser(event)">
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="firstName">First Name</label>
                        <input type="text" id="firstName" name="first_name" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name</label>
                        <input type="text" id="lastName" name="last_name" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" name="phone_number">
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="adopter">Adopter</option>
                        <option value="shelter">Shelter</option>
                        <option value="rescuer">Rescuer</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" required minlength="8">
                    </div>
                    <div class="form-group">
                        <label for="passwordConfirmation">Confirm Password</label>
                        <input type="password" id="passwordConfirmation" name="password_confirmation" required minlength="8">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeAddUserModal()">Cancel</button>
                <button type="submit" class="btn btn-primary">Add User</button>
            </div>
        </form>
    </div>
</div>

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
                <button class="tab-button" onclick="switchTab('activity')">Activity Log</button>
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

            <div id="activityTab" class="tab-content">
                <ul class="activity-list" id="userActivityList">
                    <!-- Activity items will be populated by JavaScript -->
                </ul>
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
@endsection

@push('scripts')
<script>
let currentUserId = null;

// Add User Modal Functions
function openAddUserModal() {
    document.getElementById('addUserModal').style.display = 'flex';
}

function closeAddUserModal() {
    document.getElementById('addUserModal').style.display = 'none';
    document.getElementById('addUserForm').reset();
}

function handleAddUser(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const data = Object.fromEntries(formData.entries());
    
    fetch("{{ route('admin.users.store') }}", {
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
            alert('User added successfully!');
            closeAddUserModal();
            window.location.reload();
        } else {
            alert(data.message || 'Error adding user');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error adding user');
    });
}

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
            
            // Load activity
            loadUserActivity(userId);
            
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

function loadUserActivity(userId) {
    fetch(`{{ url('admin/users') }}/${userId}/activity`)
        .then(response => response.json())
        .then(activities => {
            const activityList = document.getElementById('userActivityList');
            activityList.innerHTML = activities.map(activity => `
                <li class="activity-item">
                    <span>${activity.description}</span>
                    <span class="activity-date">${new Date(activity.created_at).toLocaleString()}</span>
                </li>
            `).join('');
        })
        .catch(error => {
            console.error('Error loading activity:', error);
        });
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
</script>
@endpush
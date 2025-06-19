@extends('layouts.admin')

@section('title', 'User Management - PawMatch Admin')

@section('page-title', 'User Management')
@section('page-subtitle', 'Manage and monitor user accounts')

@section('content')

    <div class="content-section">
        <div class="filters-section">
            <input type="text" class="form-control search-input" placeholder="Search by name or email...">
            <select class="form-select role-filter">
                <option value="">All Roles</option>
                <option value="admin">Admin</option>
                <option value="shelter">Shelter</option>
                <option value="adopter">Adopter</option>
                <option value="rescuer">Rescuer</option>
            </select>
            <select class="form-select status-filter">
                <option value="">All Status</option>
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
            </select>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value">3</div>
                <div class="stat-title">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">1</div>
                <div class="stat-title">Shelters</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">1</div>
                <div class="stat-title">Adopters</div>
            </div>
            <div class="stat-card">
                <div class="stat-value">0</div>
                <div class="stat-title">Rescuers</div>
            </div>
        </div>
        <div class="action-buttons">
            <button class="btn btn-secondary" onclick="exportUsers()">
                <i class="fas fa-download"></i> Export Users
            </button>
            <button class="btn btn-primary" onclick="addNewUser()">
                <i class="fas fa-plus"></i> Add New User
            </button>
        </div>
        <div class="table-container">
            <table class="user-table">
                <thead>
                    <tr>
                        <th><input type="checkbox" class="select-all"></th>
                        <th>USER</th>
                        <th>ROLE</th>
                        <th>CONTACT</th>
                        <th>JOINED</th>
                        <th>STATUS</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $user)
                    <tr>
                        <td><input type="checkbox"></td>
                        <td>
                            <div class="user-cell">
                                <div class="user-avatar">
                                    @if($user->profile_photo_path)
                                        <img src="{{ asset($user->profile_photo_path) }}" alt="User Avatar">
                                    @else
                                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-semibold">
                                            {{ substr($user->name, 0, 2) }}
                                        </div>
                                    @endif
                                </div>
                                <div class="user-info">
                                    <div class="font-medium">{{ $user->name }}</div>
                                    <div class="text-sm text-gray-500">ID: {{ $user->id }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="role-badge role-{{ strtolower($user->role) }}">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td>
                            <div>{{ $user->email }}</div>
                            <div class="text-sm text-gray-500">{{ $user->phone }}</div>
                        </td>
                        <td>
                            <div>{{ $user->created_at->format('M d, Y') }}</div>
                            <div class="text-sm text-gray-500">{{ $user->created_at->diffForHumans() }}</div>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $user->status ? 'active' : 'inactive' }}">
                                {{ $user->status ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td>
                            <div class="action-buttons">
                                <button class="action-icon" title="View" onclick="viewUser({{ $user->id }})">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <button class="action-icon" title="Edit" onclick="editUser({{ $user->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="action-icon" title="Delete" onclick="confirmDelete({{ $user->id }})">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- User Details Modal -->
    <div id="userDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>User Details</h2>
                <button class="close-modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="user-profile">
                    <div class="user-avatar-large">
                        <img id="userModalAvatar" src="" alt="User Avatar">
                    </div>
                    <div class="user-info-detailed">
                        <h3 id="userModalName" class="user-name">Information Available</h3>
                        <div class="user-meta">
                            <div class="meta-item">
                                <span class="meta-label">Email:</span>
                                <span id="userModalEmail" class="meta-value"></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Phone:</span>
                                <span id="userModalPhone" class="meta-value"></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Role:</span>
                                <span id="userModalRole" class="meta-value role-badge"></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Status:</span>
                                <span id="userModalStatus" class="meta-value status-badge"></span>
                            </div>
                            <div class="meta-item">
                                <span class="meta-label">Joined:</span>
                                <span id="userModalJoined" class="meta-value"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    // Select All Checkbox
    document.getElementById('selectAll').addEventListener('change', function() {
        document.querySelectorAll('input[name="selected_users[]"]')
            .forEach(checkbox => checkbox.checked = this.checked);
    });

    // Bulk Actions
    function applyBulkAction() {
        const action = document.getElementById('bulkAction').value;
        const selectedUsers = Array.from(document.querySelectorAll('input[name="selected_users[]"]:checked'))
            .map(checkbox => checkbox.value);

        if (!action || selectedUsers.length === 0) {
            alert('Please select an action and at least one user.');
            return;
        }

        if (confirm(`Are you sure you want to ${action} the selected users?`)) {
            fetch(`{{ route('admin.users.bulk-action') }}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ action, users: selectedUsers })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message);
                }
            });
        }
    }

    // Export Users
    function exportUsers() {
        const params = new URLSearchParams(window.location.search);
        window.location.href = `{{ route('admin.users.export') }}?${params.toString()}`;
    }

    // View User Modal
    function viewUser(userId) {
        fetch(`{{ url('admin/users') }}/${userId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('userModalName').textContent = `${data.first_name} ${data.last_name}`;
                document.getElementById('userModalEmail').textContent = data.email;
                document.getElementById('userModalPhone').textContent = data.phone_number || 'No phone';
                document.getElementById('userModalRole').textContent = data.role.charAt(0).toUpperCase() + data.role.slice(1);
                document.getElementById('userModalJoined').textContent = new Date(data.created_at).toLocaleDateString();
                document.getElementById('userModalStatus').textContent = data.is_active ? 'Active' : 'Inactive';
                document.getElementById('userModalStatusDot').className = `status-dot ${data.is_active ? 'active' : 'inactive'}`;
                
                // Load activity log
                loadUserActivity(userId);
                
                document.getElementById('userDetailsModal').style.display = 'flex';
            });
    }

    function loadUserActivity(userId) {
        fetch(`{{ url('admin/users') }}/${userId}/activity`)
            .then(response => response.json())
            .then(activities => {
                const activityList = document.getElementById('userModalActivity');
                activityList.innerHTML = activities.map(activity => `
                    <li class="activity-item">
                        <span>${activity.description}</span>
                        <span class="activity-date">${new Date(activity.created_at).toLocaleString()}</span>
                    </li>
                `).join('');
            });
    }

    function closeUserModal() {
        document.getElementById('userDetailsModal').style.display = 'none';
    }

    function switchTab(tabName) {
        document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
        document.querySelectorAll('.tab-button').forEach(btn => btn.classList.remove('active'));
        
        document.getElementById(`${tabName}Tab`).classList.remove('hidden');
        event.target.classList.add('active');
    }

    // Toggle User Status
    function toggleUserStatus(userId) {
        if (confirm('Are you sure you want to change this user\'s status?')) {
            fetch(`{{ url('admin/users') }}/${userId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                }
            });
        }
    }

    // Delete User
    function confirmDelete(userId) {
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
                    window.location.reload();
                }
            });
        }
    }

    // Filter form submission
    document.querySelectorAll('.form-select, .form-input').forEach(input => {
        input.addEventListener('change', () => {
            document.querySelector('form').submit();
        });
    });

    function addNewUser() {
        // Implementation for adding new user
        console.log('Adding new user...');
    }
</script>
@endsection 
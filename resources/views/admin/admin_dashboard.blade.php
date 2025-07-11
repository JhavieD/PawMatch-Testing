@extends('layouts.admin')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . Auth::user()->first_name . ' ' . Auth::user()->last_name)

@section('content')
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            </div>
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-title">Total Users</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fa-solid fa-paw"></i></div>
            </div>
            <div class="stat-value">{{ $activeAdoptions }}</div>
            <div class="stat-title">Active Adoptions</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fa-solid fa-clipboard"></i></div>
            </div>
            <div class="stat-value">{{ $pendingReports }}</div>
            <div class="stat-title">Pending Reports</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fa-solid fa-user-plus"></i></div>
            </div>
            <div class="stat-value">{{ $newUsersToday }}</div>
            <div class="stat-title">New Users Today</div>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Pending User Approvals -->
        <div class="content-card">
            <div class="card-header">
                <h2>Pending User Approvals</h2>
                <a href="{{ route('admin.verifications') }}" class="btn btn-outline">View All</a>
            </div>
            @if (isset($pendingApprovals) && count($pendingApprovals) > 0)
                <ul class="user-list">
                    @foreach ($pendingApprovals as $approval)
                        <li class="list-item">
                            <div class="item-info">
                                <div class="item-title">
                                    {{ $approval->first_name }} {{ $approval->last_name }}
                                    <span class="verification-type">({{ ucfirst($approval->type) }})</span>
                                </div>
                                <div class="item-subtitle">{{ $approval->email }}</div>
                                @if ($approval->type === 'shelter')
                                    <div class="item-subtitle">{{ $approval->organization_name }}</div>
                                @endif
                            </div>
                            <!-- Work in Progress -->
                            <div class="flex gap-2">
                                <form action="{{ route('admin.verifications.approve', $approval->verification_id) }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary btn-sm">Approve</button>
                                </form>
                                <form action="{{ route('admin.verifications.reject', $approval->verification_id) }}"
                                    method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="list-item">
                    <div class="item-info">
                        <div class="item-subtitle">No pending approvals</div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Recent Stray Reports -->
        <div class="content-card">
            <div class="card-header">
                <h2>Recent Stray Reports</h2>
                <a href="{{ route('admin.stray-reports') }}" class="btn btn-outline">View All</a>
            </div>
            @if (isset($recentReports) && count($recentReports) > 0)
                <ul class="report-list">
                    @foreach ($recentReports as $report)
                        <li class="list-item">
                            <div class="item-info">
                               <div class="item-title">Stray Animal Report</div>
                                <div class="item-subtitle">{{ $report->location }}</div>
                                <div class="item-subtitle">Reported
                                    {{ \Carbon\Carbon::parse($report->reported_at)->diffForHumans() }}</div>
                            </div>
                            <div class="btn-group">
                                <span class="status status-{{ $report->status }}">{{ ucfirst($report->status) }}</span>
                                <button class="btn btn-outline"
                                    onclick="window.location.href='{{ route('admin.stray-reports') }}?search={{ $report->report_id }}'">
                                    View
                                </button>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <ul class="report-list">
                    <li class="list-item">
                        <div class="item-info">
                            <div class="item-subtitle">No recent stray reports</div>
                        </div>
                    </li>
                </ul>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function approveVerification(verificationId, type) {
            if (confirm('Are you sure you want to approve this verification?')) {
                const url = type === 'shelter' ?
                    `/admin/verifications/shelter/${verificationId}/approve` :
                    `/admin/verifications/rescuer/${verificationId}/approve`;

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Error approving verification');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred');
                    });
            }
        }

        function rejectVerification(verificationId, type) {
            const reason = prompt('Please provide a reason for rejection:');
            if (reason) {
                const url = type === 'shelter' ?
                    `/admin/verifications/shelter/${verificationId}/reject` :
                    `/admin/verifications/rescuer/${verificationId}/reject`;

                fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            reason: reason
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert(data.message || 'Error rejecting verification');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred');
                    });
            }
        }
    </script>
@endpush

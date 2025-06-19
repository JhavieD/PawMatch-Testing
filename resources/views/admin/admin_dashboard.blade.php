@extends('layouts.admin')

@section('page-title', 'Dashboard')
@section('page-subtitle', 'Welcome back, ' . Auth::user()->name)

@section('content')
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-title">Total Users</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $activeAdoptions }}</div>
            <div class="stat-title">Active Adoptions</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $pendingReports }}</div>
            <div class="stat-title">Pending Reports</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $newUsersToday }}</div>
            <div class="stat-title">New Users Today</div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Pending User Approvals -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Pending User Approvals</h2>
            @if(isset($pendingApprovals) && count($pendingApprovals) > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($pendingApprovals as $approval)
                    <li class="py-4 flex items-center justify-between">
                        <div class="flex items-center">
                            @if($approval->user->profile_photo_path)
                                <img src="{{ asset($approval->user->profile_photo_path) }}" alt="Profile" class="w-10 h-10 rounded-full">
                            @else
                                <div class="w-10 h-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <span class="text-gray-600 font-medium">{{ substr($approval->user->name, 0, 2) }}</span>
                                </div>
                            @endif
                            <div class="ml-3">
                                <p class="text-sm font-medium text-gray-900">{{ $approval->user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $approval->user->email }}</p>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button class="btn btn-primary btn-sm">Approve</button>
                            <button class="btn btn-danger btn-sm">Reject</button>
                        </div>
                    </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 text-center py-4">No pending approvals.</p>
            @endif
        </div>

        <!-- Recent Stray Reports -->
        <div class="bg-white rounded-xl shadow p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Stray Reports</h2>
            @if(isset($recentReports) && count($recentReports) > 0)
                <ul class="divide-y divide-gray-200">
                    @foreach($recentReports as $report)
                    <li class="py-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">{{ $report->location }}</h3>
                                <p class="text-sm text-gray-500">Reported by {{ $report->reporter_name }}</p>
                            </div>
                            <span class="status-badge status-{{ $report->status }}">
                                {{ ucfirst($report->status) }}
                            </span>
                        </div>
                        <p class="mt-2 text-sm text-gray-600">{{ Str::limit($report->description, 100) }}</p>
                    </li>
                    @endforeach
                </ul>
            @else
                <p class="text-gray-500 text-center py-4">No recent stray reports.</p>
            @endif
        </div>
    </div>
@endsection
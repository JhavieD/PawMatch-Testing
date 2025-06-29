
@extends('layouts.adopter')

@section('title', 'My Stray Reports - PawMatch')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/adopter/my-reports.css') }}">
@endpush

@section('adopter-content')
<div class="main-content">
    <div class="content-wrapper">
        <div class="top-bar">
            <div class="welcome-section">
                <h1>My Stray Reports</h1>
                <p>Track the status of your submitted stray animal reports</p>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="content-card">
            <form method="GET" class="search-filter">
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
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Search
                </button>
                
                @if(request('search') || request('status'))
                    <a href="{{ route('adopter.reports.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Clear
                    </a>
                @endif
            </form>
        </div>

        <!-- Reports List -->
        <div class="reports-container">
            @forelse($reports as $report)
                <div class="report-card">
                    <!-- Report Header -->
                    <div class="report-header">
                        <h3 class="report-title">Report #{{ $report->report_id }}</h3>
                        <div class="report-badges">
                            <span class="animal-badge">{{ ucfirst($report->animal_type) }}</span>
                            <span class="status-badge status-{{ $report->status }}">{{ strtoupper($report->status) }}</span>
                        </div>
                    </div>

                    <!-- Report Details -->
                    <div class="report-body">
                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="detail-content">
                                <span class="detail-label">Location:</span>
                                <span class="detail-text">{{ $report->location }}</span>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="detail-content">
                                <span class="detail-label">Date Reported:</span>
                                <span class="detail-text">{{ \Carbon\Carbon::parse($report->reported_at)->format('M d, Y') }} {{ \Carbon\Carbon::parse($report->reported_at)->format('g:i A') }}</span>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="detail-content">
                                <span class="detail-label">Latest Update:</span>
                                @if($report->timeline->isNotEmpty())
                                    <div class="update-info">
                                        <span class="detail-text">{{ $report->timeline->last()['content'] }}</span>
                                        <small class="update-time">{{ $report->timeline->last()['date'] }}</small>
                                    </div>
                                @else
                                    <span class="detail-text no-update">No updates yet</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3>No Reports Found</h3>
                    @if(request('search') || request('status'))
                        <p>No reports match your search criteria. Try adjusting your filters.</p>
                        <a href="{{ route('adopter.reports.index') }}" class="btn btn-primary">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    @else
                        <p>You haven't submitted any stray animal reports yet.</p>
                        <a href="{{ route('adopter.report-stray') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Report a Stray
                        </a>
                    @endif
                </div>
            @endforelse
        </div>

        @if($reports->hasPages())
            <div class="pagination-wrapper">
                {{ $reports->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
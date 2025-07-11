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
            <div style="text-align: right; margin-top: 1rem;">
                <a href="{{ route('adopter.report-stray') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Report a Stray
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <form method="GET" action="{{ route('adopter.my-reports') }}" class="search-filter wide-search-bar">
            <input type="text" 
                   name="search" 
                   class="search-input wide-search-input" 
                   placeholder="Search by report ID or location..." 
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
                <a href="{{ route('adopter.my-reports') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Clear
                </a>
            @endif
        </form>

        <!-- Reports List -->
        <div class="reports-table-container">
            @if($reports->count())
            <table class="reports-table">
                <thead>
                    <tr>
                        <th>Report ID</th>
                        <th>Status</th>
                        <th>Location</th>
                        <th>Date Reported</th>
                        <th>Latest Update</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($reports as $report)
                    @php $rowId = 'report-row-' . $report->report_id; @endphp
                    <tr class="report-row" id="{{ $rowId }}">
                        <td>#{{ $report->report_id }}</td>
                        <td><span class="status-badge status-{{ $report->status }}">{{ strtoupper($report->status) }}</span></td>
                        <td>{{ $report->location }}</td>
                        <td>{{ \Carbon\Carbon::parse($report->reported_at)->format('M d, Y') }}</td>
                        <td>
                            @if($report->timeline->isNotEmpty())
                                <div class="update-info">
                                    <span class="detail-text">{{ $report->timeline->last()['content'] }}</span>
                                    <small class="update-time">{{ $report->timeline->last()['date'] }}</small>
                                </div>
                            @else
                                <span class="detail-text no-update">No updates yet</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="expand-btn" onclick="toggleReportDetails('{{ $rowId }}')">
                                <span class="expand-text">Details</span>
                                <i class="fas fa-chevron-down"></i>
                            </button>
                        </td>
                    </tr>
                    <tr class="report-details-row" style="display: none;">
                        <td colspan="7">
                            <div class="report-details-panel">
                                <div class="detail-block">
                                    <span class="detail-label">Location</span>
                                    <span class="detail-value">{{ $report->location }}</span>
                                </div>
                                <div class="detail-block">
                                    <span class="detail-label">Date Reported</span>
                                    <span class="detail-value">{{ \Carbon\Carbon::parse($report->reported_at)->format('M d, Y g:i A') }}</span>
                                </div>
                                <div class="detail-block">
                                    <span class="detail-label">Latest Update</span>
                                    <span class="detail-value">
                                        @if($report->timeline->isNotEmpty())
                                            <div class="update-info">
                                                <span class="detail-text">{{ $report->timeline->last()['content'] }}</span>
                                                <small class="update-time">{{ $report->timeline->last()['date'] }}</small>
                                            </div>
                                        @else
                                            <span class="detail-text no-update">No updates yet</span>
                                        @endif
                                    </span>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <h3>No Reports Found</h3>
                    @if(request('search') || request('status'))
                        <p>No reports match your search criteria. Try adjusting your filters.</p>
                        <a href="{{ route('adopter.my-reports') }}" class="btn btn-primary">
                            <i class="fas fa-times"></i> Clear Filters
                        </a>
                    @else
                        <p>You haven't submitted any stray animal reports yet.</p>
                        <a href="{{ route('adopter.report-stray') }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Report a Stray
                        </a>
                    @endif
                </div>
            @endif
        </div>

        @if($reports->hasPages())
            <div class="pagination-wrapper">
                {{ $reports->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function toggleReportDetails(rowId) {
    const row = document.getElementById(rowId);
    const detailsRow = row.nextElementSibling;
    const btn = row.querySelector('.expand-btn');
    if (detailsRow.style.display === 'none' || detailsRow.style.display === '') {
        detailsRow.style.display = 'table-row';
        btn.querySelector('i').classList.remove('fa-chevron-down');
        btn.querySelector('i').classList.add('fa-chevron-up');
        btn.classList.add('expanded');
    } else {
        detailsRow.style.display = 'none';
        btn.querySelector('i').classList.remove('fa-chevron-up');
        btn.querySelector('i').classList.add('fa-chevron-down');
        btn.classList.remove('expanded');
    }
}
</script>
@endpush
@endsection
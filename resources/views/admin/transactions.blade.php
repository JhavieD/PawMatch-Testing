@extends('layouts.admin')

@section('title', 'All Transactions')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="container mx-auto px-6">
        <div class="flex items-center justify-between">
            <div>
                <h1>All Transactions</h1>
                <p>View and manage all payment transactions</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>

<div class="container mx-auto px-6 pb-8">
    <!-- Filters -->
    <div class="filters-card">
        <h3>Filters</h3>
        <form method="GET" action="{{ route('admin.transactions') }}">
            <div class="filter-grid">
                <div class="filter-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="">All Status</option>
                        <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Provider Type</label>
                    <select name="provider_type">
                        <option value="">All Providers</option>
                        <option value="shelter" {{ request('provider_type') === 'shelter' ? 'selected' : '' }}>Shelter</option>
                        <option value="rescuer" {{ request('provider_type') === 'rescuer' ? 'selected' : '' }}>Rescuer</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label>Date From</label>
                    <input type="date" name="date_from" value="{{ request('date_from') }}">
                </div>
                
                <div class="filter-group">
                    <label>Date To</label>
                    <input type="date" name="date_to" value="{{ request('date_to') }}">
                </div>
            </div>
            
            <div class="filter-actions">
                <button type="submit" class="btn-primary">
                    <i class="fas fa-search mr-2"></i>Filter
                </button>
                <a href="{{ route('admin.transactions') }}" class="btn-secondary">
                    <i class="fas fa-times mr-2"></i>Clear
                </a>
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="transactions-card">
        <div class="table-header">
            <div class="flex items-center justify-between">
                <h3>Transactions ({{ $transactions->total() }})</h3>
                <div class="results-info">
                    Showing {{ $transactions->firstItem() ?? 0 }} to {{ $transactions->lastItem() ?? 0 }} of {{ $transactions->total() }} results
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Pet</th>
                        <th>Provider</th>
                        <th>Amount</th>
                        <th>Payment Status</th>
                        <th>Payout Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $transaction)
                    <tr>
                        <td>
                            {{ $transaction->payment_date ? $transaction->payment_date->format('M d, Y H:i') : 'N/A' }}
                        </td>
                        <td>
                            <div class="pet-info">
                                <img class="pet-image" 
                                     src="{{ $transaction->application->pet->image_url ?? asset('images/default-pet.png') }}" 
                                     alt="{{ $transaction->application->pet->name }}">
                                <div class="pet-details">
                                    <h4>{{ $transaction->application->pet->name }}</h4>
                                    <p>{{ $transaction->application->pet->breed }}</p>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="font-medium text-gray-900">{{ $transaction->provider_name }}</div>
                                <div class="text-sm text-gray-500">{{ ucfirst($transaction->provider_type) }}</div>
                            </div>
                        </td>
                        <td>
                            <div class="amount-main">₱{{ number_format($transaction->total_amount, 2) }}</div>
                            <div class="amount-commission">Commission: ₱{{ number_format($transaction->pawmatch_commission, 2) }}</div>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $transaction->payment_status }}">
                                {{ ucfirst($transaction->payment_status) }}
                            </span>
                        </td>
                        <td>
                            <span class="status-badge status-{{ $transaction->payout_status }}">
                                {{ ucfirst($transaction->payout_status) }}
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <button onclick="viewTransactionDetails({{ $transaction->transaction_id }})" 
                                        class="action-btn">
                                    <i class="fas fa-eye mr-1"></i>View Details
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-8 text-gray-500">
                            No transactions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($transactions->hasPages())
        <div class="pagination-container">
            {{ $transactions->appends(request()->query())->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Transaction Details Modal -->
<div id="transactionModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Transaction Details</h2>
            <button onclick="closeTransactionModal()" class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <div id="transactionDetails">
                <!-- Transaction details will be loaded here -->
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Transaction Details Modal
function viewTransactionDetails(transactionId) {
    // Show loading state
    document.getElementById('transactionDetails').innerHTML = '<p>Loading...</p>';
    document.getElementById('transactionModal').style.display = 'flex';
    
    const headers = {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };
    
    fetch(`/admin/transactions/${transactionId}/details`, {
        method: 'GET',
        headers: headers,
        credentials: 'same-origin'
    })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                document.getElementById('transactionDetails').innerHTML = data.html;
            } else {
                document.getElementById('transactionDetails').innerHTML = '<p>Error loading transaction details</p>';
            }
        })
        .catch(error => {
            console.error('Error loading transaction details:', error);
            document.getElementById('transactionDetails').innerHTML = '<p>Error loading transaction details: ' + error.message + '</p>';
        });
}

function closeTransactionModal() {
    document.getElementById('transactionModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('transactionModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeTransactionModal();
    }
});
</script>
@endpush

@push('styles')
<link rel="stylesheet" href="{{ asset('css/admin/transactions.css') }}">
@endpush
@endsection 
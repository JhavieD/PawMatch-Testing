@extends('layouts.admin')

@section('title', 'Pending Payouts')

@section('content')
<!-- Page Header -->
<div class="page-header">
    <div class="container mx-auto px-6">
        <div class="flex items-center justify-between">
            <div>
                <h1>Pending Payouts</h1>
                <p>Manage automatic payouts to providers</p>
            </div>
            <a href="{{ route('admin.dashboard') }}" class="back-btn">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
        </div>
    </div>
</div>

<div class="container mx-auto px-6 pb-8">
    <!-- Payout Statistics -->
    <div class="stats-grid" style="margin-bottom: 2rem;">
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
            </div>
            <div class="stat-value">{{ $pendingPayouts->count() }}</div>
            <div class="stat-title">Pending Payouts</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
            </div>
            <div class="stat-value">₱{{ number_format($pendingPayouts->sum('provider_amount'), 2) }}</div>
            <div class="stat-title">Total Pending Amount</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fa-solid fa-building"></i></div>
            </div>
            <div class="stat-value">{{ $pendingPayouts->whereNotNull('shelter_id')->unique('shelter_id')->count() }}</div>
            <div class="stat-title">Shelters</div>
        </div>
        <div class="stat-card">
            <div class="stat-header">
                <div class="stat-icon"><i class="fa-solid fa-heart"></i></div>
            </div>
            <div class="stat-value">{{ $pendingPayouts->whereNotNull('rescuer_id')->unique('rescuer_id')->count() }}</div>
            <div class="stat-title">Rescuers</div>
        </div>
    </div>

    <!-- Payout Actions -->
    <div class="filters-card">
        <h3>Payout Actions</h3>
        <div class="filter-actions">
            <button onclick="processAllPayouts()" class="btn-primary">
                <i class="fas fa-play mr-2"></i>Process All Payouts
            </button>
        </div>
    </div>

    <!-- Pending Payouts Table -->
    <div class="transactions-card">
        <div class="table-header">
            <div class="flex items-center justify-between">
                <h3>Pending Payouts ({{ $pendingPayouts->count() }})</h3>
                <div class="results-info">
                    Total Amount: ₱{{ number_format($pendingPayouts->sum('provider_amount'), 2) }}
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="transactions-table">
                <thead>
                    <tr>
                        <th>Transaction ID</th>
                        <th>Pet</th>
                        <th>Provider</th>
                        <th>Adopter</th>
                        <th>Payment Date</th>
                        <th>Payout Amount</th>
                        <th>Bank Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pendingPayouts as $transaction)
                    <tr>
                        <td>
                            <span class="font-mono text-sm">#{{ $transaction->transaction_id }}</span>
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
                                <div class="font-medium text-gray-900">
                                    {{ $transaction->provider_name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ ucfirst($transaction->provider_type) }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <div class="font-medium text-gray-900">
                                    {{ $transaction->adopter->user->first_name }} {{ $transaction->adopter->user->last_name }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $transaction->adopter->user->email }}
                                </div>
                            </div>
                        </td>
                        <td>
                            {{ $transaction->payment_date ? $transaction->payment_date->format('M d, Y H:i') : 'N/A' }}
                        </td>
                        <td>
                            <div class="amount-main">₱{{ number_format($transaction->provider_amount, 2) }}</div>
                        </td>
                        <td>
                            @if($transaction->shelter)
                                <div class="text-sm">
                                    <div class="font-medium">{{ $transaction->shelter->bank_name ?? 'N/A' }}</div>
                                    <div class="text-gray-500">{{ $transaction->shelter->bank_account_number ?? 'N/A' }}</div>
                                </div>
                            @elseif($transaction->rescuer)
                                <div class="text-sm">
                                    <div class="font-medium">{{ $transaction->rescuer->bank_name ?? 'N/A' }}</div>
                                    <div class="text-gray-500">{{ $transaction->rescuer->bank_account_number ?? 'N/A' }}</div>
                                </div>
                            @else
                                <span class="text-red-500 text-sm">No bank details</span>
                            @endif
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <button onclick="processPayout({{ $transaction->transaction_id }})" 
                                        class="action-btn text-green-600">
                                    <i class="fas fa-play mr-1"></i>Process
                                </button>
                                <button onclick="viewTransactionDetails({{ $transaction->transaction_id }})" 
                                        class="action-btn">
                                    <i class="fas fa-eye mr-1"></i>Details
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-8 text-gray-500">
                            No pending payouts found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
// Process individual payout
function processPayout(transactionId) {
    if (!confirm('Are you sure you want to process this payout?')) {
        return;
    }

    const headers = {
        'X-CSRF-TOKEN': '{{ csrf_token() }}',
        'Content-Type': 'application/json',
        'Accept': 'application/json'
    };

    fetch(`/admin/payouts/${transactionId}/process`, {
        method: 'POST',
        headers: headers,
        credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Payout processed successfully!');
            location.reload();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while processing the payout.');
    });
}

// Process all payouts
function processAllPayouts() {
    if (!confirm('Are you sure you want to process all pending payouts? This will initiate payouts for all eligible transactions.')) {
        return;
    }

    // This would typically call a bulk processing endpoint
    alert('Bulk payout processing feature will be implemented in the next update.');
}

// Check eligibility
function checkEligibility() {
    // This would check which transactions are eligible for payout
    alert('Eligibility check feature will be implemented in the next update.');
}

// Transaction Details Modal
function viewTransactionDetails(transactionId) {
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
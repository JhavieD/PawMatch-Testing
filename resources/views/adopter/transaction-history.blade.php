@extends('layouts.adopter-sidebar')

@section('title', 'Payment History')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adopter/transaction-history.css') }}">
@endpush

@section('adopter-content')
<div class="content-wrapper transaction-history-content">
    <div class="content-card">
        <div class="card-header-row">
            <div>
                <h2 class="card-header">Payment History</h2>
                <p class="card-subtitle">View all your payment transactions and receipts.</p>
            </div>
            <div class="total-spent">
                <span class="total-amount-badge">
                    Total Spent: ₱{{ number_format($transactions->sum('total_amount'), 2) }}
                </span>
            </div>
        </div>
            
            @if($transactions->count() > 0)
                <div class="table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Pet</th>
                                <th>Provider</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $transaction)
                            <tr class="transaction-row">
                                <td class="date-cell">
                                    <div class="date-info">
                                        <div class="date">{{ \Carbon\Carbon::parse($transaction->payment_date)->format('M d, Y') }}</div>
                                        <div class="time">{{ \Carbon\Carbon::parse($transaction->payment_date)->format('h:i A') }}</div>
                                    </div>
                                </td>
                                <td class="pet-cell">
                                    <div class="pet-info">
                                        <div class="pet-image">
                                            <img src="{{ $transaction->application->pet->image_url ?? asset('images/default-pet.png') }}" 
                                                 alt="{{ $transaction->application->pet->name }}">
                                        </div>
                                        <div class="pet-details">
                                            <div class="pet-name">{{ $transaction->application->pet->name }}</div>
                                            <div class="pet-breed">{{ $transaction->application->pet->breed }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="provider-cell">
                                    <div class="provider-info">
                                        <div class="provider-name">
                                            @if($transaction->shelter)
                                                {{ $transaction->shelter->shelter_name }}
                                            @elseif($transaction->rescuer)
                                                {{ $transaction->rescuer->organization_name }}
                                            @else
                                                N/A
                                            @endif
                                        </div>
                                        <div class="provider-type">
                                            @if($transaction->shelter)
                                                <span class="provider-badge shelter">Shelter</span>
                                            @elseif($transaction->rescuer)
                                                <span class="provider-badge rescuer">Rescuer</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="amount-cell">
                                    <div class="amount-info">
                                        <div class="total-amount">₱{{ number_format($transaction->total_amount, 2) }}</div>
                                        <div class="commission-info">Commission: ₱{{ number_format($transaction->pawmatch_commission, 2) }}</div>
                                    </div>
                                </td>
                                <td class="status-cell">
                                    <span class="status-badge status-{{ $transaction->payment_status }}">
                                        {{ ucfirst($transaction->payment_status) }}
                                    </span>
                                </td>
                                <td class="actions-cell">
                                    <button onclick="viewTransactionDetails({{ $transaction->transaction_id }})" class="btn-view-details">
                                        <i class="fas fa-eye"></i>
                                        View Details
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <div class="pagination-container">
                    {{ $transactions->links() }}
                </div>
            @else
                <div class="no-pets-message">
                    <div class="empty-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <h3>No transactions found</h3>
                    <p>You haven't made any payments yet.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<div id="transactionModal" class="modal-overlay">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Transaction Details</h3>
            <button onclick="closeTransactionModal()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
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
function viewTransactionDetails(transactionId) {
    fetch(`/payment/transaction/${transactionId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('transactionDetails').innerHTML = data.html;
                document.getElementById('transactionModal').style.display = 'flex';
            }
        })
        .catch(error => {
            console.error('Error loading transaction details:', error);
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
@endsection 
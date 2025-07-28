@extends('layouts.rescuer-profile')

@section('title', 'Transaction History')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/rescuer/transaction-history.css') }}">
@endpush

@section('rescuer-content')
<main class="main-content transaction-history-content">
    <div class="container">
        <div class="top-bar">
            <div class="welcome-section">
                <h1>Transaction History</h1>
                <p>View all your payment transactions and earnings.</p>
            </div>
        </div>

        <div class="settings-grid">
            <div class="settings-card">
                <div class="card-header">
                    <h2>Payment Transactions</h2>
                    <div class="header-actions">
                        <span class="total-earnings-badge">
                            Total Earnings: ₱{{ number_format($transactions->sum('provider_amount'), 2) }}
                        </span>
                    </div>
                </div>
                <div class="card-content">
                    @if($transactions->count() > 0)
                        <div class="table-container">
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Pet</th>
                                        <th>Adopter</th>
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
                                        <td class="adopter-cell">
                                            <div class="adopter-info">
                                                <div class="adopter-name">{{ $transaction->adopter->user->name ?? 'N/A' }}</div>
                                                <div class="adopter-email">{{ $transaction->adopter->user->email ?? '' }}</div>
                                            </div>
                                        </td>
                                        <td class="amount-cell">
                                            <div class="amount-info">
                                                <div class="total-amount">₱{{ number_format($transaction->total_amount, 2) }}</div>
                                                <div class="your-share">Your share: ₱{{ number_format($transaction->provider_amount, 2) }}</div>
                                            </div>
                                        </td>
                                        <td class="status-cell">
                                            <span class="status-badge status-{{ $transaction->payment_status }}">
                                                {{ ucfirst($transaction->payment_status) }}
                                            </span>
                                        </td>
                                        <td class="actions-cell">
                                            <button onclick="viewTransactionDetails({{ $transaction->transaction_id }})" 
                                                    class="btn-view-details">
                                                <i class="fas fa-eye"></i>
                                                View Details
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        <div class="pagination-container">
                            {{ $transactions->links() }}
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-credit-card"></i>
                            </div>
                            <h3>No transactions found</h3>
                            <p>You haven't received any payments yet.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Transaction Details Modal -->
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

<style>
/* Transaction History Styles */
.total-earnings {
    background: linear-gradient(135deg, #4CAF50, #45a049);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 14px;
}

.table-container {
    overflow-x: auto;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    width: 100%;
    min-width: 100%;
    margin: 0 auto;
    display: flex;
    justify-content: center;
}

.data-table {
    width: 100%;
    min-width: 1400px;
    border-collapse: collapse;
    background: white;
    table-layout: fixed;
}

.data-table th {
    background: #f8f9fa;
    padding: 16px 20px;
    text-align: left;
    font-weight: 600;
    color: #495057;
    border-bottom: 2px solid #e9ecef;
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.data-table th:nth-child(1) { width: 15%; } /* Date */
.data-table th:nth-child(2) { width: 20%; } /* Pet */
.data-table th:nth-child(3) { width: 25%; } /* Adopter */
.data-table th:nth-child(4) { width: 20%; } /* Amount */
.data-table th:nth-child(5) { width: 10%; } /* Status */
.data-table th:nth-child(6) { width: 10%; } /* Actions */

.data-table td {
    padding: 16px 20px;
    border-bottom: 1px solid #e9ecef;
    vertical-align: middle;
}

.transaction-row:hover {
    background-color: #f8f9fa;
    transition: background-color 0.2s ease;
}

.date-cell .date-info {
    display: flex;
    flex-direction: column;
}

.date-cell .date {
    font-weight: 600;
    color: #212529;
    font-size: 14px;
}

.date-cell .time {
    color: #6c757d;
    font-size: 12px;
    margin-top: 2px;
}

.pet-cell .pet-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.pet-cell .pet-image {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
}

.pet-cell .pet-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.pet-cell .pet-details {
    display: flex;
    flex-direction: column;
}

.pet-cell .pet-name {
    font-weight: 600;
    color: #212529;
    font-size: 14px;
}

.pet-cell .pet-breed {
    color: #6c757d;
    font-size: 12px;
    margin-top: 2px;
}

.adopter-cell .adopter-info {
    display: flex;
    flex-direction: column;
}

.adopter-cell .adopter-name {
    font-weight: 600;
    color: #212529;
    font-size: 13px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.adopter-cell .adopter-email {
    color: #6c757d;
    font-size: 12px;
    margin-top: 2px;
}

.amount-cell .amount-info {
    display: flex;
    flex-direction: column;
}

.amount-cell .total-amount {
    font-weight: 700;
    color: #212529;
    font-size: 15px;
    white-space: nowrap;
}

.amount-cell .your-share {
    color: #28a745;
    font-size: 11px;
    margin-top: 2px;
    font-weight: 500;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    max-width: 100%;
}

.status-badge {
    padding: 6px 12px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-completed {
    background-color: #d4edda;
    color: #155724;
}

.status-pending {
    background-color: #fff3cd;
    color: #856404;
}

.status-failed {
    background-color: #f8d7da;
    color: #721c24;
}

.btn-view-details {
    background: linear-gradient(135deg, #007bff, #0056b3);
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    display: flex;
    align-items: center;
    gap: 6px;
}

.btn-view-details:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: translateY(-1px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
}

.empty-state .empty-icon {
    font-size: 48px;
    color: #dee2e6;
    margin-bottom: 16px;
}

.empty-state h3 {
    color: #495057;
    font-size: 18px;
    font-weight: 600;
    margin-bottom: 8px;
}

.empty-state p {
    color: #6c757d;
    font-size: 14px;
}

.pagination-container {
    margin-top: 24px;
    display: flex;
    justify-content: center;
}

/* Modal Styles */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: none;
    align-items: center;
    justify-content: center;
    z-index: 1000;
}

.modal-content {
    background: white;
    border-radius: 12px;
    box-shadow: 0 20px 40px rgba(0,0,0,0.3);
    max-width: 500px;
    width: 90%;
    max-height: 80vh;
    overflow-y: auto;
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    border-bottom: 1px solid #e9ecef;
}

.modal-header h3 {
    margin: 0;
    color: #212529;
    font-size: 18px;
    font-weight: 600;
}

.modal-close {
    background: none;
    border: none;
    font-size: 18px;
    color: #6c757d;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.modal-close:hover {
    background: #f8f9fa;
    color: #495057;
}

.modal-body {
    padding: 24px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .data-table {
        font-size: 12px;
    }
    
    .data-table th,
    .data-table td {
        padding: 12px 8px;
    }
    
    .pet-cell .pet-image {
        width: 32px;
        height: 32px;
    }
    
    .btn-view-details {
        padding: 6px 12px;
        font-size: 11px;
    }
    
    .modal-content {
        width: 95%;
        margin: 20px;
    }
}
</style>

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
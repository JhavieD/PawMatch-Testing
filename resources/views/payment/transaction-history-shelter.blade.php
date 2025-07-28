@extends('layouts.shelter-profile')

@section('title', 'Transaction History')

@section('shelter-content')
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
            </div>
            <div class="card-content">
                @if($transactions->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full bg-white border border-gray-200 rounded-lg">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pet</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Adopter</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($transactions as $transaction)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ \Carbon\Carbon::parse($transaction->payment_date)->format('M d, Y h:i A') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <img class="h-10 w-10 rounded-full object-cover" 
                                                 src="{{ $transaction->application->pet->image_url ?? asset('images/default-pet.png') }}" 
                                                 alt="{{ $transaction->application->pet->name }}">
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ $transaction->application->pet->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $transaction->application->pet->breed }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $transaction->adopter->user->name ?? 'N/A' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <div class="font-medium">₱{{ number_format($transaction->total_amount, 2) }}</div>
                                        <div class="text-xs text-gray-500">
                                            Your share: ₱{{ number_format($transaction->provider_amount, 2) }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                            {{ $transaction->payment_status === 'completed' ? 'bg-green-100 text-green-800' : 
                                               ($transaction->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                               'bg-red-100 text-red-800') }}">
                                            {{ ucfirst($transaction->payment_status) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="viewTransactionDetails({{ $transaction->transaction_id }})" 
                                                class="text-blue-600 hover:text-blue-900">
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $transactions->links() }}
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="text-gray-400 mb-4">
                            <svg class="mx-auto h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No transactions found</h3>
                        <p class="text-gray-500">You haven't received any payments yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Transaction Details Modal -->
<div id="transactionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Transaction Details</h3>
            <div id="transactionDetails" class="space-y-3">
                <!-- Transaction details will be loaded here -->
            </div>
            <div class="mt-6 flex justify-end">
                <button onclick="closeTransactionModal()" 
                        class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function viewTransactionDetails(transactionId) {
    // Load transaction details via AJAX
    fetch(`/payment/transaction/${transactionId}/details`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('transactionDetails').innerHTML = data.html;
                document.getElementById('transactionModal').classList.remove('hidden');
            }
        })
        .catch(error => {
            console.error('Error loading transaction details:', error);
        });
}

function closeTransactionModal() {
    document.getElementById('transactionModal').classList.add('hidden');
}
</script>
@endpush
@endsection 
<div class="space-y-6">
    <!-- Transaction Header -->
    <div class="border-b border-gray-200 pb-4">
        <div class="flex items-center justify-between">
            <div>
                <h4 class="text-lg font-semibold text-gray-900">Transaction #{{ $transaction->transaction_id }}</h4>
                <p class="text-sm text-gray-600">
                    @if($transaction->payment_date)
                        @if($transaction->payment_date instanceof \Carbon\Carbon)
                            {{ $transaction->payment_date->format('F d, Y \a\t g:i A') }}
                        @else
                            {{ \Carbon\Carbon::parse($transaction->payment_date)->format('F d, Y \a\t g:i A') }}
                        @endif
                    @else
                        N/A
                    @endif
                </p>
            </div>
            <div class="text-right">
                <div class="text-2xl font-bold text-green-600">₱{{ number_format($transaction->total_amount, 2) }}</div>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                    {{ $transaction->payment_status === 'paid' ? 'bg-green-100 text-green-800' : 
                       ($transaction->payment_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                    {{ ucfirst($transaction->payment_status) }}
                </span>
            </div>
        </div>
    </div>

    <!-- Pet Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h5 class="font-semibold text-gray-900 mb-3">Pet Information</h5>
        <div class="flex items-center space-x-4">
            <img class="h-16 w-16 rounded-full object-cover" 
                 src="{{ $transaction->application->pet->image_url ?? asset('images/default-pet.png') }}" 
                 alt="{{ $transaction->application->pet->name }}">
            <div>
                <h6 class="font-medium text-gray-900">{{ $transaction->application->pet->name }}</h6>
                <p class="text-sm text-gray-600">{{ $transaction->application->pet->breed }}</p>
                <p class="text-sm text-gray-600">{{ $transaction->application->pet->age }} years old, {{ $transaction->application->pet->gender }}</p>
            </div>
        </div>
    </div>

    <!-- Provider Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h5 class="font-semibold text-gray-900 mb-3">Provider Information</h5>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-700">Provider Name</p>
                <p class="text-sm text-gray-900">{{ $transaction->provider_name }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Provider Type</p>
                <p class="text-sm text-gray-900">{{ ucfirst($transaction->provider_type) }}</p>
            </div>
            @if($transaction->shelter)
            <div>
                <p class="text-sm font-medium text-gray-700">Contact</p>
                <p class="text-sm text-gray-900">{{ $transaction->shelter->contact_number ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Email</p>
                <p class="text-sm text-gray-900">{{ $transaction->shelter->user->email ?? 'N/A' }}</p>
            </div>
            @endif
            @if($transaction->rescuer)
            <div>
                <p class="text-sm font-medium text-gray-700">Contact</p>
                <p class="text-sm text-gray-900">{{ $transaction->rescuer->contact_number ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Email</p>
                <p class="text-sm text-gray-900">{{ $transaction->rescuer->user->email ?? 'N/A' }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Adopter Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h5 class="font-semibold text-gray-900 mb-3">Adopter Information</h5>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-700">Adopter Name</p>
                <p class="text-sm text-gray-900">{{ $transaction->adopter->user->first_name }} {{ $transaction->adopter->user->last_name }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Email</p>
                <p class="text-sm text-gray-900">{{ $transaction->adopter->user->email }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Phone</p>
                <p class="text-sm text-gray-900">{{ $transaction->adopter->user->phone_number ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Application Date</p>
                <p class="text-sm text-gray-900">
                    @if($transaction->application->submitted_at)
                        @if($transaction->application->submitted_at instanceof \Carbon\Carbon)
                            {{ $transaction->application->submitted_at->format('M d, Y') }}
                        @else
                            {{ \Carbon\Carbon::parse($transaction->application->submitted_at)->format('M d, Y') }}
                        @endif
                    @else
                        N/A
                    @endif
                </p>
            </div>
        </div>
    </div>

    <!-- Payment Details -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h5 class="font-semibold text-gray-900 mb-3">Payment Details</h5>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Total Amount</span>
                <span class="text-sm font-medium text-gray-900">₱{{ number_format($transaction->total_amount, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">PawMatch Commission (20%)</span>
                <span class="text-sm font-medium text-blue-600">₱{{ number_format($transaction->pawmatch_commission, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600">Provider Amount (80%)</span>
                <span class="text-sm font-medium text-green-600">₱{{ number_format($transaction->provider_amount, 2) }}</span>
            </div>
            <hr class="border-gray-300">
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-900">Payment Method</span>
                <span class="text-sm text-gray-600">{{ $transaction->payment_method ?? 'Maya Checkout' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm font-medium text-gray-900">Maya Payment ID</span>
                <span class="text-sm text-gray-600 font-mono">{{ $transaction->maya_payment_id }}</span>
            </div>
        </div>
    </div>

    <!-- Payout Information -->
    <div class="bg-gray-50 rounded-lg p-4">
        <h5 class="font-semibold text-gray-900 mb-3">Payout Information</h5>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-700">Payout Status</p>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                    {{ $transaction->payout_status === 'completed' ? 'bg-green-100 text-green-800' : 
                       ($transaction->payout_status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                       ($transaction->payout_status === 'processing' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                    {{ ucfirst($transaction->payout_status ?? 'pending') }}
                </span>
            </div>
            <div>
                <p class="text-sm font-medium text-gray-700">Payout Date</p>
                <p class="text-sm text-gray-900">
                    @if($transaction->payout_date)
                        @if($transaction->payout_date instanceof \Carbon\Carbon)
                            {{ $transaction->payout_date->format('M d, Y H:i') }}
                        @else
                            {{ \Carbon\Carbon::parse($transaction->payout_date)->format('M d, Y H:i') }}
                        @endif
                    @else
                        Not yet processed
                    @endif
                </p>
            </div>
            @if($transaction->payout_reference)
            <div>
                <p class="text-sm font-medium text-gray-700">Payout Reference</p>
                <p class="text-sm text-gray-900 font-mono">{{ $transaction->payout_reference }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Actions -->
    @if($transaction->payment_status === 'paid' && $transaction->payout_status === 'pending')
    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
        <button onclick="processPayout({{ $transaction->transaction_id }})" 
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
            <i class="fas fa-check mr-2"></i>Mark as Paid Out
        </button>
    </div>
    @endif
</div>

<script>
function processPayout(transactionId) {
    if (confirm('Are you sure you want to mark this transaction as paid out to the provider?')) {
        fetch(`/admin/payouts/${transactionId}/process`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Payout marked as completed successfully!');
                closeTransactionModal();
                // Optionally refresh the page or update the UI
                window.location.reload();
            } else {
                alert('Error processing payout: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error processing payout. Please try again.');
        });
    }
}
</script> 
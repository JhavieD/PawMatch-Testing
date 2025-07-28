@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Adoption Payment</h2>
            
            <!-- Pet Information -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Pet Details</h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Pet Name</p>
                        <p class="font-medium">{{ $application->pet->name ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Breed</p>
                        <p class="font-medium">{{ $application->pet->breed ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Provider</p>
                        <p class="font-medium">
                            @if($application->shelter)
                                {{ $application->shelter->shelter_name }}
                            @elseif($application->rescuer)
                                {{ $application->rescuer->organization_name }}
                            @else
                                Unknown
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Application Status</p>
                        <p class="font-medium text-green-600">{{ ucfirst($application->status) }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Information -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Payment Details</h3>
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-sm text-gray-600">Adoption Fee</p>
                        <p class="text-2xl font-bold text-blue-600">₱{{ number_format($adoptionFee, 2) }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">PawMatch Commission (20%)</p>
                        <p class="text-sm text-gray-500">₱{{ number_format($adoptionFee * 0.20, 2) }}</p>
                        <p class="text-sm text-gray-600">Provider Amount (80%)</p>
                        <p class="text-sm text-gray-500">₱{{ number_format($adoptionFee * 0.80, 2) }}</p>
                    </div>
                </div>
            </div>

            <!-- Payment Method -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Payment Method</h3>
                <p class="text-gray-600 mb-4">You will be redirected to Maya Checkout to complete your payment securely.</p>
                        <span class="text-lg font-bold text-orange-800 mr-4">Test Mode</span>
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" id="testModeToggle" class="w-5 h-5 text-orange-600 bg-gray-100 border-orange-300 rounded focus:ring-orange-500 focus:ring-2">
                            <span class="ml-2 text-sm font-medium text-orange-800">Enable Test Mode</span>
                        </label>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 text-yellow-600 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <p class="text-sm text-yellow-800">
                            <strong>Important:</strong> After payment, you will be able to proceed with the meet & greet.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Payment Button -->
            <div class="flex justify-between items-center">
                <a href="{{ route('adopter.application-status') }}" 
                   class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                
                <button id="payButton" 
                        class="px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Pay ₱{{ number_format($adoptionFee, 2) }}
                </button>
            </div>

            <!-- Loading Spinner (Hidden by default) -->
            <div id="loadingSpinner" class="hidden mt-4 text-center">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
                <p class="mt-2 text-gray-600">Processing payment...</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.getElementById('payButton').addEventListener('click', function() {
    const button = this;
    const spinner = document.getElementById('loadingSpinner');
    const testMode = document.getElementById('testModeToggle').checked;
    
    // Show loading state
    button.disabled = true;
    button.textContent = 'Processing...';
    spinner.classList.remove('hidden');
    
    if (testMode) {
        // Test Mode: Simulate payment success
        setTimeout(() => {
            // Redirect to success page with test parameters
            window.location.href = '{{ route("payment.success", $application->application_id) }}?test_mode=1&amount={{ $adoptionFee }}';
        }, 2000);
        return;
    }
    
    // Real Mode: Make AJAX request to create checkout
    fetch('{{ route("payment.process", $application->application_id) }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.redirect_url) {
            // Redirect to Maya Checkout
            window.location.href = data.redirect_url;
        } else {
            // Show error
            alert('Payment error: ' + (data.error || 'Unknown error occurred'));
            
            // Reset button state
            button.disabled = false;
            button.textContent = 'Pay ₱{{ number_format($adoptionFee, 2) }}';
            spinner.classList.add('hidden');
        }
    })
    .catch(error => {
        console.error('Payment error:', error);
        alert('Payment error: Please try again');
        
        // Reset button state
        button.disabled = false;
        button.textContent = 'Pay ₱{{ number_format($adoptionFee, 2) }}';
        spinner.classList.add('hidden');
    });
});
</script>
@endpush
@endsection 
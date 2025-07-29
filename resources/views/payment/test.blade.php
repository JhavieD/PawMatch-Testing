@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Maya Payment Testing</h2>
            
            <!-- Test Cards Section -->
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Test Cards -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Test Cards</h3>
                    
                    @foreach($testCards as $type => $card)
                    <div class="bg-white rounded border p-3 mb-3">
                        <h4 class="font-medium text-gray-800 mb-2">{{ ucfirst($type) }}</h4>
                        <div class="text-sm space-y-1">
                            <p><span class="text-gray-600">Number:</span> <code class="bg-gray-100 px-1 rounded">{{ $card['number'] }}</code></p>
                            <p><span class="text-gray-600">Expiry:</span> {{ $card['expiry_month'] }}/{{ $card['expiry_year'] }}</p>
                            <p><span class="text-gray-600">CVV:</span> {{ $card['cvv'] }}</p>
                            @if($card['password'])
                            <p><span class="text-gray-600">3D Secure Password:</span> {{ $card['password'] }}</p>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Test Wallets -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-lg font-semibold text-gray-700 mb-4">Test Maya Wallets</h3>
                    
                    @foreach($testWallets as $type => $wallet)
                    <div class="bg-white rounded border p-3 mb-3">
                        <h4 class="font-medium text-gray-800 mb-2">{{ ucfirst(str_replace('_', ' ', $type)) }}</h4>
                        <div class="text-sm space-y-1">
                            <p><span class="text-gray-600">Username:</span> <code class="bg-gray-100 px-1 rounded">{{ $wallet['username'] }}</code></p>
                            <p><span class="text-gray-600">Password:</span> {{ $wallet['password'] }}</p>
                            <p><span class="text-gray-600">OTP:</span> {{ $wallet['otp'] }}</p>
                            <p class="text-xs text-gray-500">
                                @if($type === 'successful')
                                    ✅ Successful transactions
                                @else
                                    ❌ Always fails (insufficient balance)
                                @endif
                            </p>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Maya Configuration -->
            <div class="mt-6 bg-blue-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Maya Configuration</h3>
                <div class="grid md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Environment:</p>
                        <p class="font-medium">Sandbox</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Base URL:</p>
                        <p class="font-medium">https://pg-sandbox.paymaya.com</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Public Key:</p>
                        <p class="font-mono text-xs bg-gray-100 p-1 rounded">pk-Z0OSzLvIcOI2UIvDhdTGVVfRSSeiGStnceqwUE7n0Ah</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Secret Key:</p>
                        <p class="font-mono text-xs bg-gray-100 p-1 rounded">sk-X8qolYjy62kIzEbr0QRK1h4b4KDVHaNcwMYk39jInSl</p>
                    </div>
                </div>
            </div>

            <!-- Testing Instructions -->
            <div class="mt-6 bg-yellow-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Testing Instructions</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex items-start">
                        <span class="bg-yellow-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">1</span>
                        <p class="text-gray-700">Use the test cards above to simulate credit/debit card payments</p>
                    </div>
                    <div class="flex items-start">
                        <span class="bg-yellow-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">2</span>
                        <p class="text-gray-700">Use the test wallet accounts to simulate Maya wallet payments</p>
                    </div>
                    <div class="flex items-start">
                        <span class="bg-yellow-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">3</span>
                        <p class="text-gray-700">Test both successful and failed payment scenarios</p>
                    </div>
                    <div class="flex items-start">
                        <span class="bg-yellow-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">4</span>
                        <p class="text-gray-700">Monitor webhook notifications and payment status updates</p>
                    </div>
                </div>
            </div>

            <!-- Test Payment Form -->
            <div class="mt-6 bg-green-50 rounded-lg p-4">
                <h3 class="text-lg font-semibold text-gray-700 mb-4">Quick Test Payment</h3>
                <form id="testPaymentForm" class="space-y-4">
                    @csrf
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Amount (PHP)</label>
                            <input type="number" id="testAmount" value="1000" min="1" step="0.01" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <select id="testPaymentMethod" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="credit_card">Credit/Debit Card</option>
                                <option value="maya_wallet">Maya Wallet</option>
                                <option value="qr_code">QR Code</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                        Test Payment
                    </button>
                </form>
            </div>

            <!-- Back to Dashboard -->
            <div class="mt-6 text-center">
                <a href="{{ route('adopter.dashboard') }}" 
                   class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Prevent form submission and handle it with optimized JavaScript
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('testPaymentForm');
    const submitButton = form.querySelector('button[type="submit"]');
    
    // Disable form's default action
    form.setAttribute('action', 'javascript:void(0);');
    form.setAttribute('method', 'get');
    
    // Handle form submission with optimized event listener
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Disable button to prevent double submission
        submitButton.disabled = true;
        submitButton.textContent = 'Processing...';
        
        // Get form data
        const amount = document.getElementById('testAmount').value;
        const method = document.getElementById('testPaymentMethod').value;
        
        // Show result immediately without blocking
        const resultDiv = document.createElement('div');
        resultDiv.className = 'mt-4 p-4 bg-blue-100 border border-blue-300 rounded-lg';
        resultDiv.innerHTML = `
            <h4 class="font-medium text-blue-800 mb-2">Test Payment Initiated</h4>
            <p class="text-blue-700">Amount: ₱${amount}</p>
            <p class="text-blue-700">Method: ${method}</p>
            <p class="text-blue-700 text-sm mt-2">This would redirect to Maya Checkout in a real implementation.</p>
        `;
        
        // Insert result after the form
        form.parentNode.insertBefore(resultDiv, form.nextSibling);
        
        // Re-enable button
        submitButton.disabled = false;
        submitButton.textContent = 'Test Payment';
        
        // Remove result after 5 seconds
        setTimeout(() => {
            if (resultDiv.parentNode) {
                resultDiv.parentNode.removeChild(resultDiv);
            }
        }, 5000);
    });
});
</script>
@endpush
@endsection 
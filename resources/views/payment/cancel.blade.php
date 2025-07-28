@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <!-- Cancel Icon -->
            <div class="mx-auto w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-800 mb-4">Payment Cancelled</h2>
            <p class="text-gray-600 mb-6">Your payment was cancelled. You can try again anytime or contact us if you need assistance.</p>

            <!-- Application Details -->
            <div class="bg-gray-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Application Details</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Pet Name</p>
                        <p class="font-medium">{{ $application->pet->name ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Application Status</p>
                        <p class="font-medium text-green-600">{{ ucfirst($application->status) }}</p>
                    </div>
                </div>
            </div>

            <!-- Important Note -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Important Note</h3>
                <p class="text-sm text-gray-700 mb-3">
                    Your application is still approved and valid. You can complete the payment at any time to proceed with the adoption process.
                </p>
                <p class="text-sm text-gray-600">
                    The payment is required before you can schedule a meet & greet with the pet.
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('payment.show', $application->application_id) }}" 
                   class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    Try Payment Again
                </a>
                <a href="{{ route('adopter.application-status') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    View Applications
                </a>
            </div>

            <!-- Help Info -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500">
                    Questions about the payment process? Contact us at 
                    <a href="mailto:support@pawmatch.com" class="text-blue-600 hover:underline">support@pawmatch.com</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection 
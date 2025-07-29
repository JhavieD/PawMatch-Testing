@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-6 text-center">
            <!-- Success Icon -->
            <div class="mx-auto w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mb-6">
                <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
            </div>

            <h2 class="text-2xl font-bold text-gray-800 mb-4">Payment Successful!</h2>
            <p class="text-gray-600 mb-6">Your adoption fee has been paid successfully. You can now proceed with the meet & greet.</p>

            <!-- Payment Details -->
            <div class="bg-green-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Payment Details</h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-600">Pet Name</p>
                        <p class="font-medium">{{ $application->pet->name ?? 'Unknown' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Amount Paid</p>
                        <p class="font-medium text-green-600">₱{{ number_format($application->payment_amount ?? $application->pet->shelter->adoption_fee ?? $application->pet->rescuer->adoption_fee ?? 0, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Payment Date</p>
                        <p class="font-medium">{{ $application->payment_date ? $application->payment_date->format('M d, Y H:i') : 'Now' }}</p>
                    </div>
                    <div>
                        <p class="text-gray-600">Status</p>
                        <p class="font-medium text-green-600">Paid</p>
                    </div>
                </div>
            </div>

            <!-- Next Steps -->
            <div class="bg-blue-50 rounded-lg p-4 mb-6">
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Next Steps</h3>
                <div class="text-left space-y-2 text-sm">
                    <div class="flex items-start">
                        <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">1</span>
                        <p class="text-gray-700">The shelter/rescuer will be notified of your payment</p>
                    </div>
                    <div class="flex items-start">
                        <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">2</span>
                        <p class="text-gray-700">You can now schedule your meet & greet</p>
                    </div>
                    <div class="flex items-start">
                        <span class="bg-blue-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">3</span>
                        <p class="text-gray-700">Complete the adoption process with the provider</p>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('adopter.application-status') }}" 
                   class="px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-medium">
                    View Application Status
                </a>
                <a href="{{ route('adopter.dashboard') }}" 
                   class="px-6 py-3 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                    Go to Dashboard
                </a>
            </div>

            <!-- Receipt Info -->
            <div class="mt-6 pt-6 border-t border-gray-200">
                <p class="text-sm text-gray-500">
                    A payment receipt has been sent to your email. 
                    You can also view your payment history in your dashboard.
                </p>
            </div>
        </div>
    </div>
</div>
@endsection 
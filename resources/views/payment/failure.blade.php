@extends('layouts.app')

@section('content')
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                <!-- Failure Icon -->
                <div class="mx-auto w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mb-6">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </div>

                <h2 class="text-2xl font-bold text-gray-800 mb-4">Payment Failed</h2>
                <p class="text-gray-600 mb-6">We're sorry, but your payment could not be processed. Please try again or
                    contact support if the problem persists.</p>

                <!-- Application Details -->
                <div class="bg-gray-50 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Application Details</h3>
                    <div class="grid grid-cols-2 gap-4 text-sm">
                        <div>
                            <p class="text-gray-600">Pet Name</p>
                            <p class="font-medium">{{ $application->pet->name ?? 'Unknown' }}</p>
                        </div>
                        {{-- <div>
                        <p class="text-gray-600">Application Status</p>
                        <p class="font-medium text-green-600">{{ ucfirst($application->status) }}</p>
                    </div> --}}
                    </div>
                </div>

                <!-- Troubleshooting -->
                <div class="bg-yellow-50 rounded-lg p-4 mb-6">
                    <h3 class="text-lg font-semibold text-gray-700 mb-3">Troubleshooting Tips</h3>
                    <div class="text-left space-y-2 text-sm">
                        <div class="flex items-start">
                            <span
                                class="bg-yellow-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">1</span>
                            <p class="text-gray-700">Check your internet connection</p>
                        </div>
                        <div class="flex items-start">
                            <span
                                class="bg-yellow-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">2</span>
                            <p class="text-gray-700">Ensure your payment method has sufficient funds</p>
                        </div>
                        <div class="flex items-start">
                            <span
                                class="bg-yellow-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">3</span>
                            <p class="text-gray-700">Try using a different payment method</p>
                        </div>
                        <div class="flex items-start">
                            <span
                                class="bg-yellow-600 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs font-bold mr-3 mt-0.5">4</span>
                            <p class="text-gray-700">Contact support if the issue persists</p>
                        </div>
                    </div>
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

                <!-- Support Info -->
                <div class="mt-6 pt-6 border-t border-gray-200">
                    <p class="text-sm text-gray-500">
                        Need help? Contact our support team at
                        <a href="mailto:support@pawmatch.com" class="text-blue-600 hover:underline">support@pawmatch.com</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
@endsection

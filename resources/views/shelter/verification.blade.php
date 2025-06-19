@extends('layouts.shelter')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-md p-6">
        <h2 class="text-2xl font-bold mb-6">Shelter Verification</h2>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if($verification)
            <div class="mb-6">
                <h3 class="text-lg font-semibold mb-2">Current Verification Status</h3>
                <div class="p-4 rounded-lg {{ $verification->status === 'pending' ? 'bg-yellow-50' : ($verification->status === 'approved' ? 'bg-green-50' : 'bg-red-50') }}">
                    <p class="font-medium {{ $verification->status === 'pending' ? 'text-yellow-800' : ($verification->status === 'approved' ? 'text-green-800' : 'text-red-800') }}">
                        Status: {{ ucfirst($verification->status) }}
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                        Submitted: {{ \Carbon\Carbon::parse($verification->submitted_at)->format('M d, Y') }}
                    </p>
                    @if($verification->reviewed_at)
                        <p class="text-sm text-gray-600">
                            Reviewed: {{ \Carbon\Carbon::parse($verification->reviewed_at)->format('M d, Y') }}
                        </p>
                    @endif
                </div>
            </div>
        @endif

        @if(!$verification || $verification->status === 'rejected')
            <form action="{{ route('shelter.verification.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                <div>
                    <label for="registration_doc" class="block text-sm font-medium text-gray-700">Registration Document</label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="registration_doc" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload a file</span>
                                    <input id="registration_doc" name="registration_doc" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PDF, JPG, PNG up to 5MB</p>
                        </div>
                    </div>
                    @error('registration_doc')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="facebook_link" class="block text-sm font-medium text-gray-700">Facebook Page Link (Optional)</label>
                    <div class="mt-1">
                        <input type="url" name="facebook_link" id="facebook_link" class="shadow-sm focus:ring-blue-500 focus:border-blue-500 block w-full sm:text-sm border-gray-300 rounded-md" placeholder="https://facebook.com/your-shelter-page">
                    </div>
                    @error('facebook_link')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Submit for Verification
                    </button>
                </div>
            </form>
        @endif
    </div>
</div>
@endsection 
@extends('layouts.app')

@section('title', 'Enter Phone Number')

@section('content')
<div class="public-card-container">
    <div class="public-card-header">
        <h1>Complete Your Registration</h1>
        <p>We need your phone number to finish creating your account.</p>
    </div>
    <div class="mt-6">
        <form action="{{ route('google.phone.store') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Name</label>
                <div class="mt-1">{{ $googleUser['first_name'] }} {{ $googleUser['last_name'] }}</div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <div class="mt-1">{{ $googleUser['email'] }}</div>
            </div>
            <div>
                <label for="phone_number" class="block text-sm font-medium text-gray-700">Phone Number <span class="text-red-500">*</span></label>
                <input type="text" name="phone_number" id="phone_number" required class="register-input register-placeholder-small mt-1" placeholder="Enter your phone number" value="{{ old('phone_number') }}">
                @error('phone_number')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="w-full py-3 px-6 rounded-lg text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow transition">Continue</button>
        </form>
    </div>
</div>
@endsection 
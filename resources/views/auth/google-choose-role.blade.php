@extends('layouts.app')

@section('title', 'Choose Your Role')

@section('content')
<div class="public-card-container">
    <div class="public-card-header">
        <h1>Choose Your Role</h1>
        <p>Welcome, {{ $googleUser['first_name'] }}! Please select your role to complete registration.</p>
    </div>
    <div class="mt-6">
        <form action="{{ route('google.storeRole') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label class="block text-sm font-medium text-gray-700">Email</label>
                <div class="mt-1">{{ $googleUser['email'] }}</div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Select your role <span class="text-red-500">*</span></label>
                <div class="flex flex-col gap-2">
                    <label class="inline-flex items-center">
                        <input type="radio" name="role" value="adopter" class="form-radio" required>
                        <span class="ml-2">Adopter</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="role" value="shelter" class="form-radio" required>
                        <span class="ml-2">Shelter</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="role" value="rescuer" class="form-radio" required>
                        <span class="ml-2">Rescuer</span>
                    </label>
                </div>
                @error('role')
                    <div class="text-red-500 text-xs mt-1">{{ $message }}</div>
                @enderror
            </div>
            <button type="submit" class="w-full py-3 px-6 rounded-lg text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow transition">Continue</button>
        </form>
    </div>
</div>
@endsection 
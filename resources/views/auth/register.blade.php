@extends('layouts.app')

@section('title', 'Register - PawMatch')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/shared/public-card.css') }}">
<link rel="stylesheet" href="{{ asset('css/shared/auth.css') }}">
@endpush

@section('content')
<div class="hamburger-spacer"></div>
<div class="public-card-container">
    <div class="public-card-header">
        <h1>Create Your Account</h1>
        <p>Join PawMatch to connect pets with loving homes</p>
    </div>
    
    <!-- Progress Indicator -->
    <div class="progress-bar">
        <div class="progress-fill" id="progressFill"></div>
    </div>
    
    <!-- Error/Success Messages -->
    @if ($errors->any())
    <div class="error-message">
        <i class="fas fa-exclamation-triangle error-icon"></i>
        <div class="error-content">
            <p>Please correct the following errors:</p>
            <ul class="mt-2 list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
    
    @if(session('account_created') && session('dashboard_route'))
    <div id="successModal" class="fixed inset-0 flex items-center justify-center z-50 bg-black bg-opacity-40 animate-fadeIn">
        <div class="bg-white rounded-2xl shadow-xl p-8 flex flex-col items-center max-w-sm w-full animate-slideUp" style="border-top: 6px solid #4a90e2;">
            <div class="flex items-center justify-center w-16 h-16 rounded-full bg-green-100 mb-4">
                <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h2 class="text-2xl font-bold mb-2 text-gray-900">Account Created!</h2>
            <p class="text-gray-600 mb-6 text-center">Your account was created successfully. Redirecting to your dashboard...</p>
            <a href="{{ session('dashboard_route') }}" class="w-full inline-block text-center py-2 px-4 rounded-md bg-blue-600 text-white font-semibold hover:bg-blue-700 transition">Go to Dashboard Now</a>
        </div>
    </div>
    <style>
    @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    @keyframes slideUp { from { transform: translateY(40px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .animate-fadeIn { animation: fadeIn 0.3s ease; }
    .animate-slideUp { animation: slideUp 0.4s cubic-bezier(.4,0,.2,1); }
    </style>
    <script>
    setTimeout(function() {
        window.location.href = "{{ session('dashboard_route') }}";
    }, 2500);
    </script>
    @endif
    
    @php
        $googleUser = session('google_user');
        $initialStep = 1;
        if (!$googleUser && $errors->any() && old('role')) {
            if (old('role') === 'shelter' || old('role') === 'rescuer' || old('role') === 'adopter') {
                $initialStep = 4;
            }
        }
    @endphp
    
    <div id="google-signup-section" @if(!$googleUser) style="" @else style="display:none;" @endif>
        <div class="flex justify-center mb-2">
            <a href="{{ route('google.login') }}"
               class="flex items-center justify-center gap-3 px-8 py-3 border border-gray-300 rounded-xl bg-white shadow hover:shadow-md focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-gray-800 font-semibold text-lg w-full max-w-xs hover:bg-gray-50">
                <span>
                    <svg width="24" height="24" viewBox="0 0 48 48"><g><path fill="#4285F4" d="M24 9.5c3.54 0 6.7 1.22 9.19 3.22l6.85-6.85C35.63 2.36 30.18 0 24 0 14.82 0 6.73 5.82 2.69 14.09l7.98 6.2C12.13 13.13 17.57 9.5 24 9.5z"/><path fill="#34A853" d="M46.1 24.55c0-1.64-.15-3.22-.42-4.74H24v9.01h12.42c-.54 2.9-2.18 5.36-4.65 7.01l7.19 5.6C43.93 37.36 46.1 31.45 46.1 24.55z"/><path fill="#FBBC05" d="M10.67 28.29a14.5 14.5 0 010-8.58l-7.98-6.2A23.94 23.94 0 000 24c0 3.77.9 7.34 2.69 10.49l7.98-6.2z"/><path fill="#EA4335" d="M24 44c6.18 0 11.36-2.05 15.18-5.59l-7.19-5.6c-2.01 1.35-4.59 2.16-7.99 2.16-6.43 0-11.87-3.63-13.33-8.79l-7.98 6.2C6.73 42.18 14.82 48 24 48z"/><path fill="none" d="M0 0h48v48H0z"/></g></svg>
                </span>
                <span class="ml-2">Sign up with Google</span>
            </a>
        </div>
        <div class="flex items-center my-10 py-4">
            <div class="flex-grow border-t border-gray-300"></div>
            <span class="mx-8 text-gray-500 font-medium text-base whitespace-nowrap">or</span>
            <div class="flex-grow border-t border-gray-300"></div>
        </div>
    </div>
    
    <form action="{{ route('register') }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="registerForm">
        @csrf
        
        <!-- Step 1: Role Selection -->
        <div class="form-step" data-step="1">
            <h2 class="text-lg font-semibold mb-4">Step 1: Choose Your Role</h2>
            <div class="role-cards">
                <div class="role-card" data-role="adopter">
                    <i class="fas fa-heart"></i>
                    <h3>Pet Adopter</h3>
                </div>
                <div class="role-card" data-role="shelter">
                    <i class="fas fa-home"></i>
                    <h3>Shelter</h3>
                </div>
                <div class="role-card" data-role="rescuer">
                    <i class="fas fa-hand-holding-heart"></i>
                    <h3>Rescuer</h3>
                </div>
            </div>
            <input type="hidden" id="role" name="role" required value="{{ old('role') }}">
            <div class="field-error" id="roleError">Please select a role to continue</div>
            @if(!$googleUser)
            <div class="flex justify-end mt-6" id="step1Nav">
                <button type="button" id="nextBtn1" class="flex items-center justify-center py-3 px-6 rounded-lg text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow transition w-56" disabled>
                    Next <i class="fas fa-arrow-right ml-2"></i>
                </button>
            </div>
            @endif
        </div>
        
        <!-- Step 2: Personal Information -->
        <div class="form-step hidden" data-step="2">
            <h2 class="step-title">Step 2: Personal Information</h2>
            <div class="register-grid">
                <div>
                    <label for="first_name" class="register-label">
                        First Name <span class="required">*</span>
                    </label>
                    <input type="text" name="first_name" id="first_name" required class="register-input register-placeholder-small" placeholder="Enter your first name" value="{{ $googleUser ? $googleUser['first_name'] : old('first_name') }}" @if($googleUser) readonly @endif>
                    @if($googleUser)
                        <input type="hidden" name="first_name" value="{{ $googleUser['first_name'] }}">
                    @endif
                    <div class="field-error" id="first_nameError"></div>
                    <div class="field-success" id="first_nameSuccess"></div>
                </div>
                <div>
                    <label for="last_name" class="register-label">
                        Last Name <span class="required">*</span>
                    </label>
                    <input type="text" name="last_name" id="last_name" required class="register-input register-placeholder-small" placeholder="Enter your last name" value="{{ $googleUser ? $googleUser['last_name'] : old('last_name') }}" @if($googleUser) readonly @endif>
                    @if($googleUser)
                        <input type="hidden" name="last_name" value="{{ $googleUser['last_name'] }}">
                    @endif
                    <div class="field-error" id="last_nameError"></div>
                    <div class="field-success" id="last_nameSuccess"></div>
                </div>
                <div>
                    <label for="email" class="register-label">
                        Email Address <span class="required">*</span>
                        <div class="help-tooltip">
                            <i class="fas fa-question-circle"></i>
                            <span class="tooltip-text">We'll use this to send you important updates about your account and pet matches</span>
                        </div>
                    </label>
                    <input type="email" name="email" id="email" required class="register-input register-placeholder-small" placeholder="your.email@example.com" value="{{ $googleUser ? $googleUser['email'] : old('email') }}" @if($googleUser) readonly @endif>
                    @if($googleUser)
                        <input type="hidden" name="email" value="{{ $googleUser['email'] }}">
                    @endif
                    <div class="field-error" id="emailError"></div>
                    <div class="field-success" id="emailSuccess"></div>
                </div>
                <div>
                    <label for="phone_number" class="register-label">
                        Phone Number <span class="required">*</span>
                    </label>
                    <input type="tel" name="phone_number" id="phone_number" required class="register-input register-placeholder-small" placeholder="+63 912 345 6789" value="{{ $googleUser && $googleUser['phone_number'] ? $googleUser['phone_number'] : old('phone_number') }}" @if($googleUser && $googleUser['phone_number']) readonly @endif>
                    @if($googleUser && $googleUser['phone_number'])
                        <input type="hidden" name="phone_number" value="{{ $googleUser['phone_number'] }}">
                    @endif
                    <div class="field-error" id="phone_numberError"></div>
                    <div class="field-success" id="phone_numberSuccess"></div>
                </div>
                <div class="register-grid-span-2">
                    <label for="street_address" class="register-label">
                        Street Address <span class="required">*</span>
                    </label>
                    <input type="text" name="street_address" id="street_address" required class="register-input register-placeholder-small" placeholder="Enter your street address" value="{{ old('street_address') }}">
                    <div class="field-error" id="street_addressError"></div>
                    <div class="field-success" id="street_addressSuccess"></div>
                </div>
                <div>
                    <label for="city" class="register-label">
                        City <span class="required">*</span>
                    </label>
                    <input type="text" name="city" id="city" required class="register-input register-placeholder-small" placeholder="Enter your city" value="{{ old('city') }}">
                    <div class="field-error" id="cityError"></div>
                    <div class="field-success" id="citySuccess"></div>
                </div>
                <div>
                    <label for="zip_code" class="register-label">
                        ZIP Code <span class="required">*</span>
                    </label>
                    <input type="text" name="zip_code" id="zip_code" required class="register-input register-placeholder-small" placeholder="Enter your ZIP code" value="{{ old('zip_code') }}">
                    <div class="field-error" id="zip_codeError"></div>
                    <div class="field-success" id="zip_codeSuccess"></div>
                </div>
                {{--
                <div class="register-grid-span-2">
                    <label for="address" class="register-label">
                        Address <span class="required">*</span>
                    </label>
                    <input type="text" name="address" id="address" required class="register-input register-placeholder-small" placeholder="Enter your full address" value="{{ old('address') }}">
                    <div class="field-error" id="addressError"></div>
                    <div class="field-success" id="addressSuccess"></div>
                </div>
                --}}
            </div>
        </div>
        
        <!-- Step 3: Security -->
        @if(!$googleUser)
        <div class="form-step hidden" data-step="3">
            <h2 class="step-title">Step 3: Create Your Password</h2>
            <div class="register-grid">
                <div>
                    <label for="password" class="register-label">
                        Password <span class="required">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required class="register-input register-placeholder-small pr-12" placeholder="Create a strong password" autocomplete="new-password">
                        <button type="button" class="password-toggle absolute inset-y-0 right-0 flex items-center px-3" id="togglePassword" tabindex="-1">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength" id="passwordStrength"></div>
                    <div class="field-error" id="passwordError"></div>
                    <div class="field-success" id="passwordSuccess"></div>
                </div>
                <div>
                    <label for="password_confirmation" class="register-label">
                        Confirm Password <span class="required">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password_confirmation" id="password_confirmation" required class="register-input register-placeholder-small pr-12" placeholder="Confirm your password" autocomplete="new-password">
                    </div>
                    <div class="field-error" id="password_confirmationError"></div>
                    <div class="field-success" id="password_confirmationSuccess"></div>
                </div>
            </div>
        </div>
        @endif
        
        <!-- Step 4: Role-Specific Information -->
        <div class="form-step hidden" data-step="{{ $googleUser ? 3 : 4 }}">
            <h2 class="text-lg font-semibold mb-4">Step {{ $googleUser ? 3 : 4 }}: Additional Information</h2>
            <!-- Adopter Fields -->
            <div id="adopterFields" class="role-fields space-y-6" style="display:none;">
                <div>
                <label for="adopter_valid_id" class="block text-sm font-medium text-gray-700">
                    Upload Valid ID <span class="text-red-500">*</span>
                    <div class="help-tooltip">
                        <i class="fas fa-question-circle text-gray-400"></i>
                        <span class="tooltip-text">Upload a government-issued ID for verification. This helps ensure the safety of our pets.</span>
                    </div>
                </label>
                    

                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="adopter_valid_id" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload a file</span>
                                    <input id="adopter_valid_id" name="adopter_valid_id" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PDF, JPG, PNG up to 5MB</p>
                        </div>
                    </div>
                    <div class="field-error" id="adopter_valid_idError"></div>
                </div>
                <div>
                    <label for="purpose" class="block text-sm font-medium text-gray-700">
                        What is your main reason for adopting a pet? <span class="text-red-500">*</span>
                    </label>
                    <select name="purpose" id="purpose" class="register-input" required>
                        <option value="">Select purpose</option>
                        <option value="Family Companion" {{ old('purpose') == 'Family Companion' ? 'selected' : '' }}>Family Companion</option>
                        <option value="Emotional Support / Mental Health" {{ old('purpose') == 'Emotional Support / Mental Health' ? 'selected' : '' }}>Emotional Support / Mental Health</option>
                        <option value="Senior Citizen Companion" {{ old('purpose') == 'Senior Citizen Companion' ? 'selected' : '' }}>Senior Citizen Companion</option>
                    </select>
                    <div class="field-error" id="purposeError"></div>
                </div>
            </div>
            <!-- Shelter Fields -->
            <div id="shelterFields" class="role-fields space-y-6" style="display:none;">
                <div>
                    <label for="shelter_name" class="block text-sm font-medium text-gray-700">
                        Shelter Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="shelter_name" id="shelter_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Enter your shelter's name" value="{{ old('shelter_name') }}">
                    <div class="field-error" id="shelter_nameError"></div>
                </div>
                <div class="mb-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="usePersonalAddress" name="usePersonalAddress" value="on" {{ old('usePersonalAddress', 'on') == 'on' ? 'checked' : '' }} class="form-checkbox mr-2">
                        <span class="text-sm">Use the address from Step 2</span>
                    </label>
                </div>
                <div id="shelterLocationGroup">
                    <label for="shelter_location" class="block text-sm font-medium text-gray-700">
                        Location <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="shelter_location" id="shelter_location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Enter your shelter's address" value="{{ old('shelter_location') }}" readonly>
                    <div class="field-error" id="shelter_locationError"></div>
                </div>
                <div id="customShelterAddress" style="display:none;">
                    <div class="mb-2">
                        <label for="shelter_street_address" class="register-label">Street Address <span class="required">*</span></label>
                        <input type="text" name="shelter_street_address" id="shelter_street_address" class="register-input register-placeholder-small" placeholder="Enter your shelter's street address" value="{{ old('shelter_street_address') }}">
                        <div class="field-error" id="shelter_street_addressError"></div>
                    </div>
                    <div class="mb-2">
                        <label for="shelter_city" class="register-label">City <span class="required">*</span></label>
                        <input type="text" name="shelter_city" id="shelter_city" class="register-input register-placeholder-small" placeholder="Enter your shelter's city" value="{{ old('shelter_city') }}">
                        <div class="field-error" id="shelter_cityError"></div>
                    </div>
                    <div class="mb-2">
                        <label for="shelter_zip_code" class="register-label">ZIP Code <span class="required">*</span></label>
                        <input type="text" name="shelter_zip_code" id="shelter_zip_code" class="register-input register-placeholder-small" placeholder="Enter your shelter's ZIP code" value="{{ old('shelter_zip_code') }}">
                        <div class="field-error" id="shelter_zip_codeError"></div>
                    </div>
                </div>
                <div>
                    <label for="shelter_valid_id" class="block text-sm font-medium text-gray-700">
                        Business License/ID <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="shelter_valid_id" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload a file</span>
                                    <input id="shelter_valid_id" name="shelter_valid_id" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PDF, JPG, PNG up to 5MB</p>
                        </div>
                    </div>
                    <div class="field-error" id="shelter_valid_idError"></div>
                </div>
            </div>
            <!-- Rescuer Fields -->
            <div id="rescuerFields" class="role-fields space-y-6" style="display:none;">
                <div>
                    <label for="organization_name" class="block text-sm font-medium text-gray-700">
                        Organization Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="organization_name" id="organization_name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Enter your organization's name" value="{{ old('organization_name') }}">
                    <div class="field-error" id="organization_nameError"></div>
                </div>
                <div class="mb-2">
                    <label class="inline-flex items-center">
                        <input type="checkbox" id="useRescuerPersonalAddress" name="useRescuerPersonalAddress" value="on" {{ old('useRescuerPersonalAddress', 'on') == 'on' ? 'checked' : '' }} class="form-checkbox mr-2">
                        <span class="text-sm">Use the address from Step 2</span>
                    </label>
                </div>
                <div id="rescuerLocationGroup">
                    <label for="rescuer_location" class="block text-sm font-medium text-gray-700">
                        Location <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="rescuer_location" id="rescuer_location" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" placeholder="Enter your organization's address" value="{{ old('rescuer_location') }}" readonly>
                    <div class="field-error" id="rescuer_locationError"></div>
                </div>
                <div id="customRescuerAddress" style="display:none;">
                    <div class="mb-2">
                        <label for="rescuer_street_address" class="register-label">Street Address <span class="required">*</span></label>
                        <input type="text" name="rescuer_street_address" id="rescuer_street_address" class="register-input register-placeholder-small" placeholder="Enter your organization's street address" value="{{ old('rescuer_street_address') }}">
                        <div class="field-error" id="rescuer_street_addressError"></div>
                    </div>
                    <div class="mb-2">
                        <label for="rescuer_city" class="register-label">City <span class="required">*</span></label>
                        <input type="text" name="rescuer_city" id="rescuer_city" class="register-input register-placeholder-small" placeholder="Enter your organization's city" value="{{ old('rescuer_city') }}">
                        <div class="field-error" id="rescuer_cityError"></div>
                    </div>
                    <div class="mb-2">
                        <label for="rescuer_zip_code" class="register-label">ZIP Code <span class="required">*</span></label>
                        <input type="text" name="rescuer_zip_code" id="rescuer_zip_code" class="register-input register-placeholder-small" placeholder="Enter your organization's ZIP code" value="{{ old('rescuer_zip_code') }}">
                        <div class="field-error" id="rescuer_zip_codeError"></div>
                    </div>
                </div>
                <div>
                    <label for="rescuer_valid_id" class="block text-sm font-medium text-gray-700">
                        Verification Document <span class="text-red-500">*</span>
                    </label>
                    <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-gray-300 border-dashed rounded-md hover:border-blue-400 transition-colors">
                        <div class="space-y-1 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="flex text-sm text-gray-600">
                                <label for="rescuer_valid_id" class="relative cursor-pointer bg-white rounded-md font-medium text-blue-600 hover:text-blue-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-blue-500">
                                    <span>Upload a file</span>
                                    <input id="rescuer_valid_id" name="rescuer_valid_id" type="file" class="sr-only" accept=".pdf,.jpg,.jpeg,.png">
                                </label>
                                <p class="pl-1">or drag and drop</p>
                            </div>
                            <p class="text-xs text-gray-500">PDF, JPG, PNG up to 5MB</p>
                        </div>
                    </div>
                    <div class="field-error" id="rescuer_valid_idError"></div>
                </div>
            </div>
        </div>
        
        <!-- Navigation Buttons for Steps 2-4 -->
        <div class="flex justify-between items-center pt-6 gap-4 flex-wrap" id="generalNav">
            <button type="button" id="prevBtn" class="hidden md:inline-flex items-center justify-center px-6 py-3 border border-gray-300 rounded-lg text-base font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 w-44">
                <i class="fas fa-arrow-left mr-2"></i>Previous
            </button>
            <button type="button" id="nextBtn" class="flex items-center justify-center py-3 px-6 rounded-lg text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow transition w-56">
                Next <i class="fas fa-arrow-right ml-2"></i>
            </button>
            <button type="submit" id="submitBtn" class="hidden w-56 flex items-center justify-center py-3 px-6 rounded-lg text-base font-semibold text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 shadow transition">
                Create Account
            </button>
        </div>
        
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Already have an account? <a href="{{ route('login') }}" class="font-medium text-blue-600 hover:text-blue-500">Login</a>
            </p>
        </div>
        @if($googleUser)
            <input type="hidden" name="is_google_registration" value="1">
        @endif
    </form>
</div>

<script>
let currentStep = {{ $initialStep }};
const totalSteps = {{ $googleUser ? 3 : 4 }};

// Initialize the form
document.addEventListener('DOMContentLoaded', function() {
    // Show the correct step on load
    document.querySelectorAll('.form-step').forEach((step, idx) => {
        step.classList.toggle('hidden', idx + 1 !== currentStep);
    });
    updateProgress();
    setupEventListeners();
    showRoleFieldsForStep4();
    showCorrectNav();
    showGoogleSignupSection();
    updateNavigationButtons();
    // Enable Next button on role select
    const nextBtn1 = document.getElementById('nextBtn1');
    document.querySelectorAll('.role-card').forEach(card => {
        card.addEventListener('click', function() {
            nextBtn1.disabled = false;
        });
    });
    nextBtn1.addEventListener('click', function() {
        if (!this.disabled) {
            document.querySelector('[data-step="1"]').classList.add('hidden');
            document.querySelector('[data-step="2"]').classList.remove('hidden');
            currentStep = 2;
            updateProgress();
            updateNavigationButtons();
            showRoleFieldsForStep4();
            showCorrectNav();
            showGoogleSignupSection();
        }
    });
    // Password toggle for main password
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const confirmInput = document.getElementById('password_confirmation');
        const icon = this.querySelector('i');
        // If either is password, show both as text; else, show both as password
        if (passwordInput.type === 'password' || confirmInput.type === 'password') {
            passwordInput.type = 'text';
            confirmInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            confirmInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });
    var isGoogle = {{ $googleUser ? 'true' : 'false' }};
    if (isGoogle) {
        // Hide password step
        var step3 = document.querySelector('[data-step="3"]');
        if (step3) step3.style.display = 'none';
        // Disable fields
        ['first_name', 'last_name', 'email', 'phone_number'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el && el.value) el.setAttribute('disabled', 'disabled');
        });
    }
    showCorrectNav();
    document.getElementById('registerForm').addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            if (currentStep < totalSteps) {
                e.preventDefault();
                nextStep();
            }
        }
    });
    // Shelter address logic
    const usePersonalAddress = document.getElementById('usePersonalAddress');
    const shelterLocation = document.getElementById('shelter_location');
    const customShelterAddress = document.getElementById('customShelterAddress');
    const shelterLocationGroup = document.getElementById('shelterLocationGroup');
    function updateShelterLocation() {
        if (usePersonalAddress && shelterLocation && customShelterAddress && shelterLocationGroup) {
            if (usePersonalAddress.checked) {
                const street = document.getElementById('street_address').value;
                const city = document.getElementById('city').value;
                const zip = document.getElementById('zip_code').value;
                shelterLocation.value = [street, city, zip].filter(Boolean).join(', ');
                shelterLocation.readOnly = true;
                shelterLocationGroup.style.display = '';
                customShelterAddress.style.display = 'none';
            } else {
                shelterLocation.value = '';
                shelterLocation.readOnly = false;
                shelterLocationGroup.style.display = 'none';
                customShelterAddress.style.display = '';
            }
        }
    }
    if (usePersonalAddress) {
        usePersonalAddress.addEventListener('change', updateShelterLocation);
        ['street_address', 'city', 'zip_code'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function() {
                    if (usePersonalAddress.checked) {
                        updateShelterLocation();
                    }
                });
            }
        });
        updateShelterLocation();
    }
    // Rescuer address logic (mirror shelter logic)
    const useRescuerPersonalAddress = document.getElementById('useRescuerPersonalAddress');
    const rescuerLocation = document.getElementById('rescuer_location');
    const customRescuerAddress = document.getElementById('customRescuerAddress');
    const rescuerLocationGroup = document.getElementById('rescuerLocationGroup');
    function updateRescuerLocation() {
        if (useRescuerPersonalAddress && rescuerLocation && customRescuerAddress && rescuerLocationGroup) {
            if (useRescuerPersonalAddress.checked) {
                const street = document.getElementById('street_address').value;
                const city = document.getElementById('city').value;
                const zip = document.getElementById('zip_code').value;
                rescuerLocation.value = [street, city, zip].filter(Boolean).join(', ');
                rescuerLocation.readOnly = true;
                rescuerLocationGroup.style.display = '';
                customRescuerAddress.style.display = 'none';
            } else {
                rescuerLocationGroup.style.display = 'none';
                customRescuerAddress.style.display = '';
            }
        }
    }
    if (useRescuerPersonalAddress) {
        useRescuerPersonalAddress.addEventListener('change', updateRescuerLocation);
        ['street_address', 'city', 'zip_code', 'rescuer_street_address', 'rescuer_city', 'rescuer_zip_code'].forEach(function(id) {
            const el = document.getElementById(id);
            if (el) {
                el.addEventListener('input', function() {
                    updateRescuerLocation();
                });
            }
        });
        updateRescuerLocation();
    }
});

function setupEventListeners() {
    // Role selection
    document.querySelectorAll('.role-card').forEach(card => {
        card.addEventListener('click', function() {
            document.querySelectorAll('.role-card').forEach(c => c.classList.remove('selected'));
            this.classList.add('selected');
            document.getElementById('role').value = this.dataset.role;
            hideError('roleError');
            // Enable Step 1 Next button when a role is selected
            const nextBtn1 = document.getElementById('nextBtn1');
            if (nextBtn1) nextBtn1.disabled = false;
        });
    });
    // Enable Step 1 Next button on page load if a role is already selected
    const nextBtn1 = document.getElementById('nextBtn1');
    const roleInput = document.getElementById('role');
    if (nextBtn1 && roleInput && roleInput.value) {
        nextBtn1.disabled = false;
    }
    // Navigation buttons
    const nextBtn = document.getElementById('nextBtn');
    const prevBtn = document.getElementById('prevBtn');
    if (nextBtn) nextBtn.addEventListener('click', nextStep);
    if (prevBtn) prevBtn.addEventListener('click', prevStep);
    // Real-time validation
    setupRealTimeValidation();
    // Form submission
    document.getElementById('registerForm').addEventListener('submit', handleSubmit);
}

function nextStep() {
    if (validateCurrentStep()) {
        if (currentStep < totalSteps) {
            document.querySelector(`[data-step="${currentStep}"]`).classList.add('hidden');
            currentStep++;
            document.querySelector(`[data-step="${currentStep}"]`).classList.remove('hidden');
            updateProgress();
            updateNavigationButtons();
            showRoleFieldsForStep4();
            showCorrectNav();
            showGoogleSignupSection();
        }
    }
}

function prevStep() {
    if (currentStep > 1) {
        document.querySelector(`[data-step="${currentStep}"]`).classList.add('hidden');
        currentStep--;
        document.querySelector(`[data-step="${currentStep}"]`).classList.remove('hidden');
        updateProgress();
        updateNavigationButtons();
        showRoleFieldsForStep4();
        showCorrectNav();
        showGoogleSignupSection();
    }
}

function updateProgress() {
    const progress = (currentStep / totalSteps) * 100;
    document.getElementById('progressFill').style.width = progress + '%';
}

function updateNavigationButtons() {
    const prevBtn = document.getElementById('prevBtn');
    const nextBtn = document.getElementById('nextBtn');
    const submitBtn = document.getElementById('submitBtn');
    
    // Always show Previous button on steps 2–4
    if (currentStep > 1) {
        prevBtn.classList.remove('hidden');
    } else {
        prevBtn.classList.add('hidden');
    }
    // Next button logic
    if (currentStep === 1) {
        nextBtn.classList.add('hidden'); // Hide general nextBtn on Step 1
    } else if (currentStep < totalSteps) {
        nextBtn.classList.remove('hidden');
    } else {
        nextBtn.classList.add('hidden');
    }
    // Submit button logic
    if (currentStep === totalSteps) {
        submitBtn.classList.remove('hidden');
    } else {
        submitBtn.classList.add('hidden');
    }
}

function validateCurrentStep() {
    let isValid = true;
    
    switch(currentStep) {
        case 1:
            const role = document.getElementById('role').value;
            if (!role) {
                showError('roleError', 'Please select a role to continue');
                isValid = false;
            }
            break;
        case 2:
            const requiredFields = ['first_name', 'last_name', 'email', 'phone_number', 'street_address', 'city', 'zip_code'];
            requiredFields.forEach(field => {
                const value = document.getElementById(field).value.trim();
                if (!value) {
                    showError(field + 'Error', 'This field is required');
                    isValid = false;
                } else {
                    hideError(field + 'Error');
                    showSuccess(field + 'Success', '✓ Valid');
                }
            });
            
            // Email validation
            const email = document.getElementById('email').value;
            if (email && !isValidEmail(email)) {
                showError('emailError', 'Please enter a valid email address');
                isValid = false;
            }
            break;
        case 3:
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('password_confirmation').value;
            
            if (!password) {
                showError('passwordError', 'Password is required');
                isValid = false;
            } else if (password.length < 8) {
                showError('passwordError', 'Password must be at least 8 characters long');
                isValid = false;
            } else {
                hideError('passwordError');
                showSuccess('passwordSuccess', '✓ Password meets requirements');
            }
            
            if (password !== confirmPassword) {
                showError('password_confirmationError', 'Passwords do not match');
                isValid = false;
            } else if (confirmPassword) {
                hideError('password_confirmationError');
                showSuccess('password_confirmationSuccess', '✓ Passwords match');
            }
            break;
        case 4:
            const roleValue = document.getElementById('role').value;
            if (roleValue === 'rescuer') {
                const useRescuer = document.getElementById('useRescuerPersonalAddress');
                if (useRescuer && !useRescuer.checked) {
                    const customFields = ['rescuer_street_address', 'rescuer_city', 'rescuer_zip_code'];
                    customFields.forEach(field => {
                        const value = document.getElementById(field).value.trim();
                        if (!value) {
                            showError(field + 'Error', 'This field is required');
                            isValid = false;
                        } else {
                            hideError(field + 'Error');
                        }
                    });
                }
                // Always require organization_name, rescuer_location, rescuer_valid_id
                const rescuerFields = ['organization_name', 'rescuer_location', 'rescuer_valid_id'];
                rescuerFields.forEach(field => {
                    const value = document.getElementById(field).value.trim();
                    if (!value) {
                        showError(field + 'Error', 'This field is required');
                        isValid = false;
                    } else {
                        hideError(field + 'Error');
                    }
                });
                break;
            }
            const roleFields = getRoleFields(roleValue);
            roleFields.forEach(field => {
                const value = document.getElementById(field).value.trim();
                if (!value) {
                    showError(field + 'Error', 'This field is required');
                    isValid = false;
                } else {
                    hideError(field + 'Error');
                }
            });
            break;
    }
    
    return isValid;
}

function getRoleFields(role) {
    switch(role) {
        case 'adopter':
            return ['adopter_valid_id'];
        case 'shelter':
            return ['shelter_name', 'shelter_location', 'shelter_valid_id'];
        case 'rescuer':
            return ['organization_name', 'rescuer_location', 'rescuer_valid_id'];
        default:
            return [];
    }
}

function setupRealTimeValidation() {
    // Password strength
    document.getElementById('password').addEventListener('input', function() {
        const password = this.value;
        const strengthDiv = document.getElementById('passwordStrength');
        
        if (password.length === 0) {
            strengthDiv.style.display = 'none';
            return;
        }
        
        let strength = 0;
        let feedback = '';
        
        if (password.length >= 8) strength++;
        if (/[a-z]/.test(password)) strength++;
        if (/[A-Z]/.test(password)) strength++;
        if (/[0-9]/.test(password)) strength++;
        if (/[^A-Za-z0-9]/.test(password)) strength++;
        
        if (strength <= 2) {
            strengthDiv.className = 'password-strength strength-weak';
            feedback = 'Weak password';
        } else if (strength <= 3) {
            strengthDiv.className = 'password-strength strength-medium';
            feedback = 'Medium strength password';
        } else {
            strengthDiv.className = 'password-strength strength-strong';
            feedback = 'Strong password';
        }
        
        strengthDiv.textContent = feedback;
        strengthDiv.style.display = 'block';
    });
    
    // Email validation
    document.getElementById('email').addEventListener('blur', function() {
        const email = this.value;
        if (email && !isValidEmail(email)) {
            showError('emailError', 'Please enter a valid email address');
        } else if (email) {
            hideError('emailError');
            showSuccess('emailSuccess', '✓ Valid email');
        }
    });
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

function showError(elementId, message) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = message;
        element.style.display = 'block';
    }
}

function hideError(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        element.style.display = 'none';
    }
}

function showSuccess(elementId, message) {
    const element = document.getElementById(elementId);
    if (element) {
        element.textContent = message;
        element.style.display = 'block';
    }
}

function handleSubmit(e) {
    if (!validateCurrentStep()) {
        e.preventDefault();
        return;
    }
    
    // Show loading state
    const submitBtn = document.getElementById('submitBtn');
    const spinner = submitBtn.querySelector('.spinner');
    const text = submitBtn.querySelector('span:last-child');
    
    submitBtn.disabled = true;
    spinner.style.display = 'inline-block';
    text.textContent = 'Creating Account...';
    
    // Hide loading state after a delay (in case of errors)
    setTimeout(() => {
        submitBtn.disabled = false;
        spinner.style.display = 'none';
        text.textContent = 'Create Account';
    }, 5000);

    // Add debug for rescuer fields
    const role = document.getElementById('role').value;
    if (role === 'rescuer') {
        const useRescuer = document.getElementById('useRescuerPersonalAddress');
        const rescuerLoc = document.getElementById('rescuer_location').value;
        const rescuerStreet = document.getElementById('rescuer_street_address').value;
        const rescuerCity = document.getElementById('rescuer_city').value;
        const rescuerZip = document.getElementById('rescuer_zip_code').value;
        console.log('[DEBUG][SUBMIT] useRescuerPersonalAddress:', useRescuer ? useRescuer.checked : null);
        console.log('[DEBUG][SUBMIT] rescuer_location:', rescuerLoc);
        console.log('[DEBUG][SUBMIT] rescuer_street_address:', rescuerStreet);
        console.log('[DEBUG][SUBMIT] rescuer_city:', rescuerCity);
        console.log('[DEBUG][SUBMIT] rescuer_zip_code:', rescuerZip);
    }
}

function showRoleFieldsForStep4() {
    const role = document.getElementById('role').value;
    const adopterFields = document.getElementById('adopterFields');
    const shelterFields = document.getElementById('shelterFields');
    const rescuerFields = document.getElementById('rescuerFields');
    const purposeField = document.getElementById('purpose');
    adopterFields.style.display = 'none';
    shelterFields.style.display = 'none';
    rescuerFields.style.display = 'none';
    if (purposeField) purposeField.required = false;
    if (currentStep === totalSteps) {
        if (role === 'adopter') {
            adopterFields.style.display = 'block';
            if (purposeField) purposeField.required = true;
        }
        if (role === 'shelter') {
            shelterFields.style.display = 'block';
            autofillShelterLocation();
        }
        if (role === 'rescuer') rescuerFields.style.display = 'block';
    }
}

function showCorrectNav() {
    const step1Nav = document.getElementById('step1Nav');
    const generalNav = document.getElementById('generalNav');
    if (currentStep === 1) {
        if (step1Nav) step1Nav.style.display = '';
        if (generalNav) generalNav.style.display = 'none';
    } else {
        if (step1Nav) step1Nav.style.display = 'none';
        if (generalNav) generalNav.style.display = '';
    }
}

function autofillShelterLocation() {
    const usePersonal = document.getElementById('usePersonalAddress');
    const shelterLocation = document.getElementById('shelter_location');
    const group = document.getElementById('shelterLocationGroup');
    const custom = document.getElementById('customShelterAddress');
    if (!usePersonal || !shelterLocation || !group || !custom) return;
    function setPersonal() {
        const street = document.getElementById('street_address').value.trim();
        const city = document.getElementById('city').value.trim();
        const zip = document.getElementById('zip_code').value.trim();
        shelterLocation.value = street && city && zip ? `${street}, ${city}, ${zip}` : '';
        shelterLocation.readOnly = true;
        group.style.display = '';
        custom.style.display = 'none';
    }
    function setCustom() {
        shelterLocation.value = '';
        shelterLocation.readOnly = true;
        group.style.display = 'none';
        custom.style.display = '';
    }
    usePersonal.addEventListener('change', function() {
        if (this.checked) {
            setPersonal();
        } else {
            setCustom();
        }
    });
    // On load
    if (usePersonal.checked) {
        setPersonal();
    } else {
        setCustom();
    }
}

document.getElementById('role').addEventListener('change', function() {
    showRoleFieldsForStep4();
});

// On page load, ensure correct fields are shown if user reloads on step 4
showRoleFieldsForStep4();

// If there are validation errors, ensure nav buttons are correct
@if ($errors->any())
    updateNavigationButtons();
@endif

function showGoogleSignupSection() {
    var section = document.getElementById('google-signup-section');
    if (section) {
        if (currentStep === 1) {
            section.style.display = '';
        } else {
            section.style.display = 'none';
        }
    }
}
</script>
@endsection 
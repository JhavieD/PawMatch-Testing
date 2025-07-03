@extends('layouts.app')

@section('title', 'Reset Password - PawMatch')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/shared/auth.css') }}">
@endsection

@section('content')
<div class="flex-1 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto">
        <div class="auth-card">
            <div class="p-8">
                <div class="text-center mb-6">
                    <div class="auth-icon">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">Reset Password</h1>
                    <p class="text-gray-600 mt-2">Enter your new password below</p>
                </div>

                <form method="POST" action="{{ route('password.update') }}" class="auth-form">
                    @csrf
                    <input type="hidden" name="token" value="{{ $token }}">

                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i>
                            Email Address
                        </label>
                        <div class="input-wrapper">
                            <input type="email" id="email" name="email" required value="{{ old('email') }}"
                                placeholder="Enter your email address"
                                class="form-input @error('email') is-invalid @enderror">
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        @error('email')
                        <span class="invalid-feedback" role="alert">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">
                            <i class="fas fa-lock"></i>
                            New Password
                        </label>
                        <div class="input-wrapper">
                            <input type="password" id="password" name="password" required
                                placeholder="Enter new password"
                                class="form-input @error('password') is-invalid @enderror">
                            <i class="fas fa-lock input-icon"></i>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="passwordToggleIcon"></i>
                            </button>
                        </div>
                        @error('password')
                        <span class="invalid-feedback" role="alert">
                            <i class="fas fa-exclamation-circle"></i>
                            {{ $message }}
                        </span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="password_confirmation" class="form-label">
                            <i class="fas fa-lock"></i>
                            Confirm New Password
                        </label>
                        <div class="input-wrapper">
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                placeholder="Confirm new password" class="form-input">
                            <i class="fas fa-lock input-icon"></i>
                            <button type="button" class="password-toggle"
                                onclick="togglePassword('password_confirmation')">
                                <i class="fas fa-eye" id="passwordConfirmationToggleIcon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit" class="auth-btn">
                        <span>Reset Password</span>
                        <i class="fas fa-check"></i>
                    </button>
                </form>

                <div class="auth-footer">
                    <p>
                        <a href="{{ route('login') }}" class="auth-link">
                            <i class="fas fa-arrow-left"></i>
                            Back to Login
                        </a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function togglePassword(inputId) {
        const passwordInput = document.getElementById(inputId);
        const toggleIcon = document.getElementById(inputId === 'password' ? 'passwordToggleIcon' : 'passwordConfirmationToggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
</script>
@endpush
@endsection
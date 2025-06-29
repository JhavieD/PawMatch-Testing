@extends('layouts.app')

@section('title', 'Reset Password - PawMatch')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/shared/auth.css') }}">
@endsection

@section('content')
<div class="flex-1 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md mx-auto">
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-8">
                <div class="text-center mb-6">
                    <div class="auth-icon">
                        <i class="fas fa-key"></i>
                    </div>
                    <h1 class="text-2xl font-bold text-gray-900">Reset Password</h1>
                    <p class="text-gray-600 mt-2">Enter your email to receive a password reset link</p>
                </div>
                
                @if (session('status'))
                    <div class="success-message">
                        <div class="error-icon">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="error-content">
                            <p>{{ session('status') }}</p>
                        </div>
                    </div>
                @endif

                <form method="POST" action="{{ route('password.email') }}" class="auth-form">
                    @csrf
                    <div class="form-group">
                        <label for="email" class="form-label">
                            <i class="fas fa-envelope"></i>
                            Email Address
                        </label>
                        <div class="input-wrapper">
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                required 
                                value="{{ old('email') }}" 
                                placeholder="Enter your email address"
                                class="form-input @error('email') is-invalid @enderror"
                            >
                            <i class="fas fa-envelope input-icon"></i>
                        </div>
                        @error('email')
                            <span class="invalid-feedback" role="alert">
                                <i class="fas fa-exclamation-circle"></i>
                                {{ $message }}
                            </span>
                        @enderror
                    </div>

                    <button type="submit" class="auth-btn">
                        <span>Send Password Reset Link</span>
                        <i class="fas fa-paper-plane"></i>
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
@endsection 
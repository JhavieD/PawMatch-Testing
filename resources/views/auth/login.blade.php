@extends('layouts.app')

@section('title', 'Login - PawMatch')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/shared/auth.css') }}">
@endsection

@section('content')
<!-- Account Created Modal -->
@if (session('account_created'))
<div id="accountCreatedModal" class="modal-overlay">
    <div class="modal-box">
        <button onclick="closeAccountCreatedModal()" class="modal-close">&times;</button>
        <div class="modal-checkmark">
            <svg viewBox="0 0 48 48" width="56" height="56">
                <circle cx="24" cy="24" r="24" fill="#4a90e2"/>
                <polyline points="14,26 22,34 34,18" fill="none" stroke="#fff" stroke-width="4" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </div>
        <div class="modal-content">
            <h2>Your account has been created</h2>
            <p>You can now log in to your PawMatch account and start your journey!</p>
            <button onclick="closeAccountCreatedModal()" class="modal-btn">Okay</button>
        </div>
    </div>
</div>
@endif
<style>
</style>
<script>
function closeAccountCreatedModal() {
    document.getElementById('accountCreatedModal').style.display = 'none';
}
window.addEventListener('click', function(e) {
    var modal = document.getElementById('accountCreatedModal');
    if (modal && e.target === modal) {
        closeAccountCreatedModal();
    }
});
</script>

@push('scripts')
<script>
    window.addEventListener('DOMContentLoaded', function() {
        console.log('DOMContentLoaded fired');
        setTimeout(function() {
            var choice = document.getElementById('loginChoice');
            var form = document.getElementById('loginForm');
            var cardAnimator = document.getElementById('cardAnimator');
            if (choice && form) {
                console.log('Setting initial state: choice visible, form hidden');
                choice.classList.remove('hidden');
                form.classList.remove('visible');
                if (cardAnimator) cardAnimator.classList.remove('expanded');
            } else {
                console.log('loginChoice or loginForm not found in DOM.');
            }
        }, 100);
    });

    function showEmailForm() {
        const choiceForm = document.getElementById('loginChoice');
        const emailForm = document.getElementById('loginForm');
        const cardAnimator = document.getElementById('cardAnimator');
        choiceForm.classList.add('hidden');
        emailForm.classList.add('visible');
        if (cardAnimator) cardAnimator.classList.add('expanded');
    }

    function showChoiceForm() {
        const choiceForm = document.getElementById('loginChoice');
        const emailForm = document.getElementById('loginForm');
        const cardAnimator = document.getElementById('cardAnimator');
        emailForm.classList.remove('visible');
        choiceForm.classList.remove('hidden');
        if (cardAnimator) cardAnimator.classList.remove('expanded');
    }

    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('passwordToggleIcon');
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

    // Add loading state to form submission
    document.querySelector('.auth-form').addEventListener('submit', function(e) {
        const submitBtn = this.querySelector('.auth-btn');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Signing In...';
        submitBtn.disabled = true;
    });
</script>
@endpush

<main class="auth-main main-content">
    <div class="auth-container">
        <!-- Login Card -->
        <div class="auth-card">
            <div id="cardAnimator" class="card-animator">
                <div class="auth-header">
                    <div class="auth-icon">
                        <i class="fas fa-user-circle"></i>
                    </div>
                    <h1>Welcome Back</h1>
                    <p>Sign in to your PawMatch account</p>
                </div>

                @if ($errors->any())
                    <div class="error-message">
                        <div class="error-icon">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="error-content">
                            @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Login Container with CSS Animations -->
                <div class="login-container">
                    <!-- Google/Email Choice -->
                    <div id="loginChoice" class="login-choice compact-choice">
                        <div class="flex flex-col gap-2 mb-2">
                            <a href="{{ route('google.login') }}"
                               class="google-btn flex items-center justify-center gap-3 px-6 py-3 border border-gray-200 rounded-xl bg-white shadow-md hover:shadow-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition text-gray-800 font-semibold text-base w-full max-w-xs mx-auto">
                                <span class="inline-block align-middle">
                                    <svg width="22" height="22" viewBox="0 0 48 48"><g><path fill="#4285F4" d="M24 9.5c3.54 0 6.7 1.22 9.19 3.22l6.85-6.85C35.63 2.36 30.18 0 24 0 14.82 0 6.73 5.82 2.69 14.09l7.98 6.2C12.13 13.13 17.57 9.5 24 9.5z"/><path fill="#34A853" d="M46.1 24.55c0-1.64-.15-3.22-.42-4.74H24v9.01h12.42c-.54 2.9-2.18 5.36-4.65 7.01l7.19 5.6C43.93 37.36 46.1 31.45 46.1 24.55z"/>
                                    <path fill="#FBBC05" d="M10.67 28.29a14.5 14.5 0 010-8.58l-7.98-6.2A23.94 23.94 0 000 24c0 3.77.9 7.34 2.69 10.49l7.98-6.2z"/><path fill="#EA4335" d="M24 44c6.18 0 11.36-2.05 15.18-5.59l-7.19-5.6c-2.01 1.35-4.59 2.16-7.99 2.16-6.43 0-11.87-3.63-13.33-8.79l-7.98 6.2C6.73 42.18 14.82 48 24 48z"/>
                                    <path fill="none" d="M0 0h48v48H0z"/></g></svg>
                                </span>
                                <span class="tracking-tight">Sign in with Google</span>
                            </a>
                            <div class="choice-divider my-2 flex items-center justify-center w-full max-w-xs mx-auto">
                                <div class="flex-grow h-px bg-gray-200"></div>
                                <span class="mx-2 text-gray-400 text-xs font-medium">or</span>
                                <div class="flex-grow h-px bg-gray-200"></div>
                            </div>
                            <button onclick="showEmailForm()" 
                                    class="email-btn w-full py-2 px-6 rounded-lg bg-blue-600 text-white font-semibold hover:bg-blue-700 max-w-xs mx-auto shadow-md">
                                Continue with Email
                            </button>
                        </div>
                    </div>
                    <!-- Email/Password Form (hidden by default) -->
                    <form id="loginForm" 
                          action="{{ route('login') }}" 
                          method="POST" 
                          class="login-form auth-form">
                        @csrf
                        <button type="button" 
                                onclick="showChoiceForm()" 
                                class="back-btn mb-4 text-blue-600 hover:text-blue-800 hover:underline flex items-center gap-2">
                            <i class="fas fa-arrow-left text-sm"></i>
                            Back
                        </button>
                        <div class="form-group">
                            <label for="email" class="form-label">
                                Email Address
                            </label>
                            <div class="input-wrapper">
                                <input 
                                    type="email" 
                                    class="form-input @error('email') is-invalid @enderror" 
                                    id="email" 
                                    name="email" 
                                    value="{{ old('email') }}" 
                                    placeholder="Enter your email address"
                                    required 
                                    autofocus
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

                        <div class="form-group">
                            <label for="password" class="form-label">
                                Password
                            </label>
                            <div class="input-wrapper">
                                <input 
                                    type="password" 
                                    class="form-input @error('password') is-invalid @enderror" 
                                    id="password" 
                                    name="password" 
                                    placeholder="Enter your password"
                                    required
                                >
                                <i class="fas fa-lock input-icon"></i>
                                <button type="button" class="password-toggle" onclick="togglePassword()">
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

                        <div class="form-options">
                            <label class="checkbox-wrapper">
                                <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                <span class="checkmark"></span>
                                Remember me
                            </label>
                            <a href="{{ route('password.request') }}" class="forgot-link">Forgot Password?</a>
                        </div>

                        <button type="submit" class="auth-btn">
                            <span>Sign In</span>
                            <i class="fas fa-arrow-right"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="auth-divider">
                <span>or</span>
            </div>

            <div class="auth-footer">
                <p>Don't have an account? 
                    <a href="{{ route('register') }}" class="auth-link">
                        Create one now
                        <i class="fas fa-arrow-right"></i>
                    </a>
                </p>
            </div>
        </div>

        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-content">
                <div class="welcome-icon">
                    <i class="fas fa-heart"></i>
                </div>
                <h2>Find Your Perfect Companion</h2>
                <p>Join thousands of pet lovers who have found their forever friends through PawMatch. Start your adoption journey today.</p>
                <div class="welcome-features">
                    <div class="feature">
                        <i class="fas fa-search"></i>
                        <span>Browse Available Pets</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-home"></i>
                        <span>Connect with Shelters</span>
                    </div>
                    <div class="feature">
                        <i class="fas fa-heart"></i>
                        <span>Save Lives</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection 
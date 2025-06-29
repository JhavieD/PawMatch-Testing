@extends('layouts.app')

@section('title', 'Login - PawMatch')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/shared/auth.css') }}">
@endsection

@section('content')
<!-- Main Content -->
<main class="auth-main main-content">
    <div class="auth-container">
        <!-- Login Card -->
        <div class="auth-card">
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

            <form action="{{ route('login') }}" method="POST" class="auth-form">
                @csrf
                
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

@push('scripts')
<script>
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
@endsection 
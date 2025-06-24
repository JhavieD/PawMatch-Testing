<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PawMatch</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/shared/auth.css') }}" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <nav class="navbar">
        <div class="nav-content">
            <a href="/" class="logo"><img src="{{ asset('images/logo.png') }}" alt="PawMatch Logo" style="height: 40px;"></a>
            <div class="nav-links">
                <a href="/about">About</a>
                <a href="/pets">Find Pets</a>
                <a href="/faq">FAQ</a>
                <a href="/terms">Terms</a>
                <a href="/login" class="btn" style="font-weight: 700;">Login</a>
            </div>
        </div>
    </nav>

    <div class="login-container">
        <h1>Welcome Back</h1>
        @if ($errors->any())
            <div class="error-message">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        <form action="{{ route('login') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autofocus>
                @error('email')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" required>
                @error('password')
                    <span class="invalid-feedback" role="alert">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <button type="submit" class="btn1">Login</button>
            <div class="form-footer">
                <p>Don't have an account? <a href="{{ route('register') }}">Register</a></p>
                <p><a href="{{ route('password.request') }}">Forgot Password?</a></p>
            </div>
        </form>
    </div>
</body>
</html> 
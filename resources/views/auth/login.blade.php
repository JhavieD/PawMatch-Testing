<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PawMatch</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Reset and Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }

        body {
            background: linear-gradient(rgba(74, 144, 226, 0.1), rgba(74, 144, 226, 0.2));
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Navigation Styles */
        .navbar {
            background: white;
            padding: 1rem 2rem;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #4a90e2;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }

        .nav-links a {
            color: #4b5563;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .nav-links a:hover {
            color: #4a90e2;
        }

        .nav-links .btn {
            padding: 0.5rem 1rem;
            background: #4a90e2;
            color: white;
            border-radius: 8px;
        }

        .nav-links .btn:hover {
            background: #357abd;
        }

        .login-container {
            width: 100%;
            max-width: 500px;
            padding: 2rem;
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin: 2rem auto;
        }

        h1 {
            text-align: center;
            margin-bottom: 2rem;
            color: #333;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            color: #666;
        }

        input, select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .btn1 {
            width: 100%;
            padding: 0.8rem;
            background: #4a90e2;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .btn1:hover {
            background: #357abd;
        }

        .form-footer {
            margin-top: 1.5rem;
            text-align: center;
        }

        .form-footer p {
            margin: 0.5rem 0;
            color: #666;
        }

        .form-footer a {
            color: #4a90e2;
            text-decoration: none;
        }

        .form-footer a:hover {
            text-decoration: underline;
        }

        .error-message {
            color: #dc2626;
            font-size: 0.875rem;
            margin-top: 0.5rem;
        }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-content">
            <a href="/" class="logo">🐾 PawMatch</a>
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
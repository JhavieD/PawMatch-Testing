@extends('layouts.app')

@section('content')
<div class="container" style="width:100%;max-width:500px;padding:2rem;background:white;border-radius:10px;box-shadow:0 4px 6px rgba(0,0,0,0.1);margin:2rem auto;">
    <h1 style="text-align:center;margin-bottom:2rem;color:#333;">Reset Password</h1>
    
    @if (session('status'))
        <div class="success-message" style="color:#059669;font-size:0.875rem;margin-top:0.5rem;">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="form-group" style="margin-bottom:1.5rem;">
            <label for="email" style="display:block;margin-bottom:0.5rem;color:#666;">Email Address</label>
            <input type="email" id="email" name="email" required value="{{ old('email') }}" placeholder="Enter your email"
                style="width:100%;padding:0.8rem;border:1px solid #ddd;border-radius:5px;font-size:1rem;">
            @error('email')
                <div class="error-message" style="color:#dc2626;font-size:0.875rem;margin-top:0.5rem;">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn" style="width:100%;padding:0.8rem;background:#4a90e2;color:white;border:none;border-radius:5px;font-size:1rem;cursor:pointer;transition:background 0.3s ease;">
            Send Password Reset Link
        </button>

        <div class="form-footer" style="margin-top:1.5rem;text-align:center;">
            <p><a href="{{ route('login') }}" style="color:#4a90e2;text-decoration:none;">Back to Login</a></p>
        </div>
    </form>
</div>
@endsection
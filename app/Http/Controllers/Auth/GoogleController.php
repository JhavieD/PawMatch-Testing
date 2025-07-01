<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Socialite;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use App\Models\Shared\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class GoogleController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        // Check if user already exists
        $user = User::where('email', $googleUser->getEmail())->first();
        if ($user) {
            Auth::login($user, true);
            return redirect()->route('dashboard'); // or your intended route
        }

        // Split the name into first and last name
        $fullName = $googleUser->getName();
        $nameParts = explode(' ', $fullName, 2);
        $firstName = $nameParts[0] ?? '';
        $lastName = $nameParts[1] ?? '';

        // Try to get phone number from Google (rarely provided)
        $phone = $googleUser->user['phoneNumber'] ?? null;

        // Store Google user info in session and redirect to registration
        $request->session()->put('google_user', [
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $googleUser->getEmail(),
            'phone_number' => $phone,
        ]);
        return redirect()->route('register');
    }
} 
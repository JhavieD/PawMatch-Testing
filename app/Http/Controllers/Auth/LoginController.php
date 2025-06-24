<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Shared\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $request->session()->regenerate();

            // If user is adopter and intended URL is report-stray, redirect there
            $intended = session()->pull('url.intended');
            if (
                $user->role === 'adopter' &&
                $intended &&
                str_contains($intended, route('adopter.report-stray', [], false))
            ) {
                return redirect()->intended(route('adopter.report-stray'));
            }

            switch ($user->role) {
                case 'adopter':
                    return redirect()->route('adopter.dashboard');
                case 'shelter':
                    return redirect()->route('shelter.dashboard');
                case 'rescuer':
                    return redirect()->route('rescuer.dashboard');
                case 'admin':
                    return redirect()->route('admin.dashboard');
                default:
                    Auth::logout();
                    return redirect('/login')->withErrors(['email' => 'Unknown role.']);
            }
        }

        throw ValidationException::withMessages([
            'email' => ['The provided credentials are incorrect.'],
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
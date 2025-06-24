<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Shared\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller {
    public function store(Request $request) {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Attempt login
        if (Auth::attempt($credentials)) {
            // Check user role and redirect accordingly
            if (Auth::user()->usertype == 'shelter') { //Multiauth
                return redirect()->route('shelter.dashboard');
            }
            
            return redirect()->intended('/dashboard'); // Default redirect
        }

        // Return error if login fails
        return back()->withErrors([
            'email' => 'Invalid credentials.',
        ]);
    }

    public function destroy(Request $request) {
        Auth::logout();
        return redirect('/');
    }
}
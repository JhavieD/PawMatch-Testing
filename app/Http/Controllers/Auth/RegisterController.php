<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'role' => ['required', 'in:adopter,shelter,rescuer'],
            'phone_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        // Additional validation for shelter role
        if ($request->role === 'shelter') {
            $request->validate([
                'shelter_name' => ['required', 'string', 'max:255'],
                'shelter_location' => ['required', 'string', 'max:255'],
                'shelter_valid_id_upload' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            ]);
        }

        // Additional validation for rescuer role
        if ($request->role === 'rescuer') {
            $request->validate([
                'organization_name' => ['required', 'string', 'max:255'],
                'rescuer_location' => ['required', 'string', 'max:255'],
            ]);
        }

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone_number' => $validated['phone_number'],
            'address' => $validated['address'],
        ]);

        // Handle shelter-specific data
        if ($request->role === 'shelter') {
            $shelterIdFile = $request->file('shelter_valid_id_upload');
            $shelterIdPath = $shelterIdFile ? $shelterIdFile->store('shelter-ids', 'public') : null;
            $user->shelterProfile()->create([
                'shelter_name' => $request->shelter_name,
                'location' => $request->shelter_location,
                'contact_info' => $validated['phone_number'],
                'verified' => false,
                'user_id' => $user->user_id,
            ]);
        }

        // Handle rescuer-specific data
        if ($request->role === 'rescuer') {
            $user->rescuerProfile()->create([
                'organization_name' => $request->organization_name,
                'location' => $request->rescuer_location,
                'verified' => false,
                'user_id' => $user->user_id,
            ]);
        }

        // Handle adopter-specific data
        if ($request->role === 'adopter') {
            // Optionally handle adopter-specific fields, e.g., valid_id
            if ($request->hasFile('valid_id')) {
                $adopterIdFile = $request->file('valid_id');
                $adopterIdPath = $adopterIdFile->store('adopter-ids', 'public');
                $user->adopterProfile()->create([
                    'address' => $request->address,
                    'adoption_status' => 'pending',
                    'valid_id_path' => $adopterIdPath,
                ]);
            } else {
                $user->adopterProfile()->create([
                    'address' => $request->address,
                    'adoption_status' => 'pending',
                ]);
            }
        }

        Auth::login($user);

        // Redirect based on role
        if ($request->role === 'shelter') {
            return redirect()->intended('/shelter/dashboard');
        } elseif ($request->role === 'rescuer') {
            return redirect()->intended('/rescuer/dashboard');
        } else {
            return redirect()->intended('/adopter/dashboard');
        }
    }
} 
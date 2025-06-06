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
            'role' => ['required', 'in:adopter,shelter'],
            'phone_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
        ]);

        // Additional validation for shelter role
        if ($request->role === 'shelter') {
            $request->validate([
                'shelter_name' => ['required', 'string', 'max:255'],
                'shelter_description' => ['required', 'string'],
                'shelter_license' => ['required', 'string', 'max:255'],
                'shelter_documents' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
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
            $shelterDocument = $request->file('shelter_documents');
            $documentPath = $shelterDocument->store('shelter-documents', 'public');

            $user->shelter()->create([
                'name' => $request->shelter_name,
                'description' => $request->shelter_description,
                'license_number' => $request->shelter_license,
                'document_path' => $documentPath,
            ]);
        }

        // Handle adopter-specific data
        if ($request->role === 'adopter') {
            $user->adopter()->create([
                'address' => $request->address,
                'adoption_status' => 'pending',
            ]);
        }

        Auth::login($user);

        return redirect()->intended($request->role === 'shelter' ? '/shelter/dashboard' : '/adopter/dashboard');
    }
} 
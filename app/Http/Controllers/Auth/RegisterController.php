<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Shared\Controller;
use App\Models\Shared\User;
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
        // If shelter, combine Step 2 address fields into 'address' for validation (must be before validation)
        if ($request->role === 'shelter') {
            $usePersonal = $request->input('usePersonalAddress', 'off') === 'on';
            if ($usePersonal) {
                $address = trim($request->street_address) . ', ' . trim($request->city) . ', ' . trim($request->zip_code);
                $request->merge(['address' => $address]);
            } else {
                $address = trim($request->shelter_street_address) . ', ' . trim($request->shelter_city) . ', ' . trim($request->shelter_zip_code);
                $request->merge(['address' => $address]);
            }
        }
        // Combine address for adopter
        if ($request->role === 'adopter') {
            $address = trim($request->street_address) . ', ' . trim($request->city) . ', ' . trim($request->zip_code);
            $request->merge(['address' => $address]);
        }
        // Combine address for rescuer
        if ($request->role === 'rescuer') {
            $useRescuerPersonal = $request->input('useRescuerPersonalAddress', 'off') === 'on';
            if ($useRescuerPersonal) {
                $address = trim($request->street_address) . ', ' . trim($request->city) . ', ' . trim($request->zip_code);
                $request->merge(['address' => $address, 'rescuer_location' => $address]);
            } else {
                $address = trim($request->rescuer_street_address) . ', ' . trim($request->rescuer_city) . ', ' . trim($request->rescuer_zip_code);
                $request->merge(['address' => $address, 'rescuer_location' => $address]);
            }
        }
        $isGoogle = $request->has('is_google_registration');
        $validationRules = [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'role' => ['required', 'in:adopter,shelter,rescuer'],
            'phone_number' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:255'],
        ];
        if (!$isGoogle) {
            $validationRules['password'] = [
                'required',
                'string',
                'min:8',
                'confirmed',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).+$/',
            ];
        }
        $validated = $request->validate($validationRules, [
            'password.regex' => 'Password must be at least 8 characters and include at least 1 uppercase letter, 1 lowercase letter, 1 number, and 1 special character.'
        ]);

        // Additional validation for shelter role
        if ($request->role === 'shelter') {
            $request->validate([
                'shelter_name' => ['required', 'string', 'max:255'],
                'shelter_valid_id' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            ]);
            $usePersonal = $request->input('usePersonalAddress', 'off') === 'on';
            if (!$usePersonal) {
                $request->validate([
                    'shelter_street_address' => ['required', 'string', 'max:255'],
                    'shelter_city' => ['required', 'string', 'max:255'],
                    'shelter_zip_code' => ['required', 'string', 'max:20'],
                ]);
            }
        }

        // Additional validation for rescuer role
        if ($request->role === 'rescuer') {
            $useRescuerPersonal = $request->input('useRescuerPersonalAddress', 'off') === 'on';
            $request->validate([
                'organization_name' => ['required', 'string', 'max:255'],
                'rescuer_location' => ['required', 'string', 'max:255'],
            ]);
            if (!$useRescuerPersonal) {
                $request->validate([
                    'rescuer_street_address' => ['required', 'string', 'max:255'],
                    'rescuer_city' => ['required', 'string', 'max:255'],
                    'rescuer_zip_code' => ['required', 'string', 'max:20'],
                ]);
            }
        }

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'email' => $validated['email'],
            'password' => $isGoogle ? Hash::make(\Illuminate\Support\Str::random(24)) : Hash::make($validated['password']),
            'role' => $validated['role'],
            'phone_number' => $validated['phone_number'],
            'address' => $validated['address'],
        ]);

        // Handle shelter-specific data
        if ($request->role === 'shelter') {
            $request->validate([
                'shelter_valid_id' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:2048'],
            ]);

            $validIdPath = null;
            $fileType = null;
            if ($request->hasFile('shelter_valid_id')) {
                $validId = $request->file('shelter_valid_id');
                $validIdPath = $validId->store('shelters/', 's3');
                $fileType = $validId->getClientMimeType();
            }
            $user->shelter()->create([
                'shelter_name' => $request->shelter_name,
                'location' => $validated['address'],
                'contact_info' => $validated['phone_number'],
                'verified' => false,
                'user_id' => $user->user_id,
                'shelter_valid_id' => $validIdPath,
            ]);
            // Insert into user_valid_ids using $user->id
            if ($validIdPath) {
                \DB::table('user_valid_ids')->insert([
                    'user_id' => $user->user_id,
                    'image_url' => $validIdPath,
                    'file_type' => $fileType,
                    'uploaded_at' => now(),
                ]);
            }
        }

        // Handle rescuer-specific data
        if ($request->role === 'rescuer') {
            if ($request->role === 'rescuer') {
            $request->validate([
                'rescuer_valid_id' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            ]);

            $validIdPath = null;
            $fileType = null;
            if ($request->hasFile('rescuer_valid_id')) {
                $validId = $request->file('rescuer_valid_id');
                $validIdPath = $validId->store('rescuers/', 's3');
                $fileType = $validId->getClientMimeType();
            }
            $user->rescuer()->create([
                'organization_name' => $request->organization_name,
                'location' => $request->rescuer_location,
                'verified' => false,
                'user_id' => $user->user_id,
            ]);

            if ($validIdPath) {
                \DB::table('user_valid_ids')->insert([
                    'user_id' => $user->user_id,
                    'image_url' => $validIdPath,
                    'file_type' => $fileType,
                    'uploaded_at' => now(),
                ]);
            }
        }
        }
        

        // Handle adopter-specific data
        if ($request->role === 'adopter') {
            $request->validate([
                'adopter_valid_id' => ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            ]);

            $validIdPath = null;
            $fileType = null;
            if ($request->hasFile('adopter_valid_id')) {
                $validId = $request->file('adopter_valid_id');
                $validIdPath = $validId->store('adopters/', 's3');
                $fileType = $validId->getClientMimeType();
            }
            $user->adopter()->create([
                'address' => $request->address,
                'adoption_status' => 'pending',
                'adopter_valid_id' => $validIdPath,
                'purpose' => $request->purpose,
            ]);

            // Insert into user_valid_ids using $user->id
            if ($validIdPath) {
                \DB::table('user_valid_ids')->insert([
                    'user_id' => $user->user_id,
                    'image_url' => $validIdPath,
                    'file_type' => $fileType,
                    'uploaded_at' => now(),
                ]);
            }
        }

        // Redirect to login with success modal
        return redirect()->route('login')->with([
            'account_created' => true,
            'account_message' => 'Your account was created successfully! Please log in to continue.'
        ]);
    }
} 
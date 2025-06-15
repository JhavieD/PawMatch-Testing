<?php

namespace App\Http\Controllers;

use App\Models\AdoptionApplication;
use Illuminate\Http\Request;
use App\Models\Pet;
use App\Models\Application;
use App\Models\Message;

class AdopterDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $adopter = $user->adopter()->with('savedPets')->first();
        $favoritePets = $adopter ? $adopter->savedPets : collect();

        // Get recent applications
        $recentApplications = $adopter ? $adopter->applications()->with(['pet', 'shelter'])->latest()->take(5)->get() : collect();

        // Get recent messages (temporarily disabled)
        $recentMessages = collect();

        return view('adopter.adopter_dashboard', compact(
            'favoritePets',
            'recentApplications',
            'recentMessages'
        ));
    }

    public function profile()
    {
        $user = auth()->user();
        $adopter = $user->adopter;
        return view('adopter.profile', compact('user', 'adopter'));
    }

    public function updateProfile(Request $request)
    {
        $user = auth()->user();
        $adopter = $user->adopter;

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'profile_image' => 'nullable|image|max:2048',
        ]);

        // Split name if needed
        [$first_name, $last_name] = array_pad(explode(' ', $request->name, 2), 2, '');

        $user->update([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);
        $adopter->update([
            'address' => $request->address,
        ]);

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile-images', 'public');
            $user->profile_image = 'storage/' . $path;
            $user->save();
        }
        if ($request->has('remove_photo')) {
            $user->profile_image = null;
            $user->save();
        }

        return back()->with('success', 'Profile updated!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|confirmed|min:8',
        ]);
        $user = auth()->user();
        if (!\Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Current password is incorrect.']);
        }
        $user->password = \Hash::make($request->new_password);
        $user->save();
        return back()->with('success', 'Password updated!');
    }

    public function updateNotifications(Request $request)
    {
        $adopter = auth()->user()->adopter;
        $adopter->update([
            'email_notifications' => $request->has('email_notifications'),
            'application_updates' => $request->has('application_updates'),
            'new_pet_alerts' => $request->has('new_pet_alerts'),
            'marketing_communications' => $request->has('marketing_communications'),
        ]);
        return back()->with('success', 'Notification preferences updated!');
    }

    public function deleteAccount(Request $request)
    {
        $user = auth()->user();
        auth()->logout();
        $user->delete();
        return redirect('/')->with('success', 'Account deleted.');
    }
} 

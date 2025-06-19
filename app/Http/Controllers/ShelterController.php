<?php

namespace App\Http\Controllers;

use App\Models\Shelter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;

class ShelterController extends Controller
{
    public function index()
    {
        $shelter = auth()->user()->shelter;

        // statistics
        $availablePets = $shelter->pets()->where('adoption_status', 'available')->count();
        $pendingApplications = $shelter->applications()->where('status', 'pending')->count();
        $successfulAdoptions = $shelter->applications()->where('status', 'approved')->count();
        $newMessages = $shelter->receivedMessages()->where('is_read', false)->count();
        $averageRating = round($shelter->adopterReviews()->avg('rating'), 1);
        $totalReviews = $shelter->adopterReviews()->count();


        // Recent items
        $recentPets = $shelter->pets()->latest()->take(2)->get();
        $recentApplications = $shelter->applications()->latest()->take(2)->get();
        $recentMessages = $shelter->receivedMessages()->latest()->take(2)->get();
        $recentReviews = $shelter->adopterReviews()->orderByDesc('created_at')->take(2)->get();

        return view('shelter.shelter_dashboard', compact(
            'availablePets',
            'pendingApplications',
            'successfulAdoptions',
            'newMessages',
            'averageRating',
            'totalReviews',
            'recentPets',
            'recentApplications',
            'recentMessages',
            'recentReviews'
        ));
    }

    public function applications()
    {
        return view('shelter.pet_applications');
    }

    public function pets()
    {
        $shelter = auth()->user()->shelter;
        $pets = $shelter->pets()->latest()->get();
        return view('shelter.pets', compact('pets'));
    }
    // for addingpets
    public function store(Request $request)
    {
        $shelter = auth()->user()->shelter;
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string',
            'breed' => 'required|string',
            'age' => 'required|integer',
            'gender' => 'required|string',
            'size' => 'required|string',
            'description' => 'nullable|string',
            'behavior' => 'nullable|string',
            'daily_activity' => 'nullable|string',
            'special_needs' => 'nullable|string',
            'compatibility' => 'nullable|string',
        ]);
        $data['adoption_status'] = 'available'; // always set to available on add
        $data['shelter_id'] = $shelter->shelter_id;

        // Debug: log all request and validated data
        Log::info('Pet Add Request Data', $request->all());
        Log::info('Pet Add Validated Data', $data);

        $pet = \App\Models\Pet::create($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('shelter.pets')->with('success', 'Pet added successfully!');
    }
    // for editing pets
    public function update(Request $request, $petId)
    {
        $pet = \App\Models\Pet::findOrFail($petId);
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string',
            'breed' => 'required|string',
            'age' => 'required|integer',
            'gender' => 'required|string',
            'size' => 'required|string',
            'description' => 'nullable|string',
            'adoption_status' => 'required|string',
            'behavior' => 'nullable|string',
            'daily_activity' => 'nullable|string',
            'special_needs' => 'nullable|string',
            'compatibility' => 'nullable|string',
        ]);
        $pet->update($data);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('shelter.pets')->with('success', 'Pet updated successfully!');
    }

    // for deleting pets
    public function destroy($petId)
    {
        $pet = \App\Models\Pet::findOrFail($petId);
        $pet->delete();
        return redirect()->route('shelter.pets')->with('success', 'Pet deleted successfully!');
    }

    public function dashboard()
    {
        return view('shelter.shelter_dashboard');
    }

    public function profile()
    {
        return view('shelter.profile');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'shelter_name' => ['required', 'string', 'max:255'],
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . Auth::id() . ',user_id'],
            'contact_number' => ['required', 'string', 'max:255'],
        ]);

        // Update user information
        $user = Auth::user();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->save();

        // Update shelter information
        $shelter = $user->shelter;
        $shelter->name = $request->shelter_name;
        $shelter->contact_number = $request->contact_number;
        $shelter->save();

        return redirect()->back()->with('success', 'Profile updated successfully.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => ['required', 'current_password'],
            'new_password' => ['required', Password::defaults(), 'confirmed'],
        ]);

        Auth::user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return redirect()->back()->with('success', 'Password updated successfully.');
    }

    public function updateNotifications(Request $request)
    {
        $user = Auth::user();
        $shelter = $user->shelter;
        
        $shelter->update([
            'email_notifications' => $request->has('email_notifications'),
            'application_updates' => $request->has('application_updates'),
            'marketing_communications' => $request->has('marketing_communications')
        ]);

        return redirect()->back()->with('success', 'Notification preferences updated successfully.');
    }
}

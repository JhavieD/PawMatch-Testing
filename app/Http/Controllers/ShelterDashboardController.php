<?php

namespace App\Http\Controllers;

use App\Models\Shelter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;
use App\Models\Message;
use App\Models\User;

class ShelterDashboardController extends Controller
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
        $recentApplications = $shelter->applications()->with('adopter.user')->latest()->take(2)->get();
        $recentMessages = $shelter->receivedMessages()
            ->select('sender_id', \DB::raw('MAX(message_id) as max_id'))
            ->groupBy('sender_id')
            ->get()
            ->pluck('max_id')
            ->map(function ($id) {
                return \App\Models\Message::with('sender')->find($id);
            });
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
            // 'profilePictureUrl' // No longer needed
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

    public function messages(Request $request)
    {

        $shelter = auth()->user()->shelter;

        $partnerIds = Message::where('sender_id', $shelter->user_id)
            ->orWhere('receiver_id', $shelter->user_id)
            ->get()
            ->flatMap(function ($message) use ($shelter) {
                return [
                    $message->sender_id !== $shelter->user_id ? $message->sender_id : null,
                    $message->receiver_id !== $shelter->user_id ? $message->receiver_id : null
                ];
            })
            ->filter()
            ->unique()
            ->values();
        
        $partners = User::whereIn('user_id', $partnerIds)->get();

        $receiver = $partners->first(); // default chat open to first partner

        return view('shelter.messages', compact('partners', 'receiver'));
    }
    
    public function profile()
    {
        $user = auth()->user();
        $shelter = $user->shelter;
        return view('shelter.profile', compact('user', 'shelter'));
    }
    //Upload New Photo Feature
    public function updateProfile(Request $request)
    {
        \Log::info('updateProfile called'); // Identical to adopter's log

        $user = auth()->user();
        $shelter = $user->shelter; // Use shelter relationship

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'required|string|max:20',
            'address' => 'required|string|max:255', 
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Split name if needed
        [$first_name, $last_name] = array_pad(explode(' ', $request->name, 2), 2, '');

        $user->update([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);
        
        $shelter->update([
            'address' => $request->address,
        ]);

        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');
            $profileImagePath = $file->store('profileimage', 's3'); 
            $fileType = $file->getClientMimeType();

            \DB::table('user_profile_pic')->updateOrInsert(
                ['user_id' => $user->user_id],
                [
                    'image_url' => $profileImagePath,
                    'file_type' => $fileType,
                    'uploaded_at' => now(),
                    'is_displayed' => true,
                ]
            );
        }
        
        if ($request->has('remove_photo')) {
            \DB::table('user_profile_pic')
            ->where('user_id', $user->user_id)
            ->update(['is_displayed' => false]); // hide image
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

    public function deleteAccount(Request $request)
    {
        $user = auth()->user();
        auth()->logout();
        $user->delete();
        return redirect('/')->with('success', 'Account deleted.');
    }
}

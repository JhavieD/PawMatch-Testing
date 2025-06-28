<?php

namespace App\Http\Controllers\Shelter;

use App\Models\Shelter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;
use App\Models\Shared\Message;
use App\Models\Shared\User;
use App\Http\Controllers\Shared\Controller;

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
                return Message::with('sender')->find($id);
            });
        $recentReviews = $shelter->adopterReviews()->orderByDesc('created_at')->take(2)->get();

        return view('shelter.shelter_dashboard', compact(
            'shelter',
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
        $pets = $shelter->pets()->with('images')->latest()->get();
        return view('shelter.pets', compact('pets'));
    }

    public function getPetImages($petId)
    {
        $pet = \App\Models\Shared\Pet::with('images')->findOrFail($petId);
        return response()->json([
            'images' => $pet->images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'image_url' => $image->image_url,
                ];
            }),
        ]);
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
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:5120',
            'eating_habits' => 'nullable|string',
        ]);
        $data['adoption_status'] = 'available'; // always set to available on add
        $data['shelter_id'] = $shelter->shelter_id;

        // Debug: log all request and validated data
        Log::info('Pet Add Request Data', $request->all());
        Log::info('Pet Add Validated Data', $data);

        $pet = \App\Models\Shared\Pet::create($data);

        if ($request->hasFile('images')) {
            try {
                foreach ($request->file('images') as $image) {
                    $path = $image->store('petimages', 's3');
                    \Storage::disk('s3')->setVisibility($path, 'public');
                    $imageUrl = \Storage::disk('s3')->url($path);

                    \App\Models\Shared\PetImage::create([
                        'pet_id' => $pet->pet_id,
                        'image_url' => $imageUrl,
                    ]);
                }
            } catch (\Exception $e) {
                \Log::error('Error uploading pet images: ' . $e->getMessage());
                if ($request->ajax() || $request->wantsJson()) {
                    return response()->json(['success' => false, 'error' => 'Image upload failed.'], 500);
                }
                return back()->withErrors(['images' => 'Image upload failed.']);
            }
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('shelter.pets')->with('success', 'Pet added successfully!');
    }
    // for editing pets
    public function update(Request $request, $petId)
    {
        $pet = \App\Models\Shared\Pet::findOrFail($petId);
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
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5024',
            'eating_habits' => 'nullable|string',
        ]);
        $pet->update($request->only([
            'name',
            'species',
            'breed',
            'age',
            'gender',
            'size',
            'description',
            'adoption_status',
            'behavior',
            'daily_activity',
            'special_needs',
            'compatibility',
            'eating_habits',
        ]));

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('petimages', 's3');
                \Storage::disk('s3')->setVisibility($path, 'public');
                $imageUrl = \Storage::disk('s3')->url($path);

                \App\Models\Shared\PetImage::create([
                    'pet_id' => $pet->pet_id,
                    'image_url' => $imageUrl,
                ]);
            }
        }
        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('shelter.pets')->with('success', 'Pet updated successfully!');
    }

    // for deleting pets
    public function destroy($petId)
    {
        $pet = \App\Models\Shared\Pet::findOrFail($petId);
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
        $verification = $shelter->verifications()->latest()->first(); // Get the latest verification record
        return view('shelter.profile', compact('user', 'shelter', 'verification'));
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
    // Delete a specific photo
    public function deleteImage($imageId)
    {
        $image = \App\Models\Shared\PetImage::findOrFail($imageId);

        // Delete from S3
        $path = parse_url($image->image_url, PHP_URL_PATH);
        $path = ltrim($path, '/');
        \Storage::disk('s3')->delete($path);

        // Delete from database
        $image->delete();

        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return back()->with('success', 'Image deleted successfully!');
    }
}

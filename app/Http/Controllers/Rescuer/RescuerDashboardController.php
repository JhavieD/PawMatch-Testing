<?php

namespace App\Http\Controllers\Rescuer;

use Illuminate\Http\Request;
use App\Models\Rescuer\RescuerVerification;
use App\Http\Controllers\Shared\Controller;
use App\Models\Shared\AdoptionApplication;

class RescuerDashboardController extends Controller
{
    public function index()
    {
        $rescuer = auth()->user()->rescuer;

        $availablePets = $rescuer->pets()->where('adoption_status', 'available')->count();
        $pendingApplications = $rescuer->applications()->where('status', 'pending')->count();
        $successfulAdoptions = $rescuer->applications()->where('status', 'approved')->count();
        $newMessages = $rescuer->messages()->where('is_read', false)->count();
        $averageRating = $rescuer->reviews()->avg('rating') ?? 0;
        $totalReviews = $rescuer->reviews()->count();

        $recentPets = $rescuer->pets()->latest()->take(5)->get();
        $recentApplications = $rescuer->applications()
            ->with(['adopter.user', 'pet'])
            ->latest()
            ->take(2)
            ->get();
        // Fetch all recent messages, order by newest, keep only one per sender
        $recentMessages = $rescuer->messages()
            ->with('sender')
            ->orderByDesc('sent_at')
            ->get()
            ->unique('sender_id')
            ->take(2)
            ->map(function ($msg) {
                // Try to decrypt message_content if present
                if (!empty($msg->message_content)) {
                    try {
                        $msg->decrypted_content = \Crypt::decryptString($msg->message_content);
                    } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                        $msg->decrypted_content = '[Unable to decrypt]';
                    }
                } else {
                    $msg->decrypted_content = $msg->content ?? '';
                }
                return $msg;
            })
            ->values();
        $recentReviews = $rescuer->reviews()->with(['adopter.user'])->latest()->take(3)->get();
        $verification = $rescuer->verifications()->latest()->first();

        return view('rescuer.rescuer_dashboard', compact(
            'rescuer',
            'availablePets',
            'pendingApplications',
            'successfulAdoptions',
            'newMessages',
            'averageRating',
            'totalReviews',
            'recentPets',
            'recentApplications',
            'recentMessages',
            'recentReviews',
            'verification'
        ));
    }

    public function petManagement(Request $request)
    {
        $rescuer = auth()->user()->rescuer;
        $query = $rescuer->pets()->with('images');

        // Filtering
        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('adoption_status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('breed', 'like', "%$search%")
                    ->orWhere('pet_id', 'like', "%$search%");
            });
        }
        $pets = $query->latest()->get();
        return view('rescuer.pet-management', compact('pets'));
    }

    public function petApplications()
    {
        return view('rescuer.pet_applications');
    }

    public function rescuerMessages(Request $request)
    {
        // Fetch all adopters and shelters as possible partners for rescuers
        $partners = \App\Models\Shared\User::whereIn('role', ['adopter', 'shelter'])->get();
        foreach ($partners as $partner) {
            $lastMessage = \App\Models\Shared\Message::where(function ($q) use ($partner) {
                $q->where('sender_id', auth()->id())->where('receiver_id', $partner->user_id);
            })->orWhere(function ($q) use ($partner) {
                $q->where('sender_id', $partner->user_id)->where('receiver_id', auth()->id());
            })->orderByDesc('sent_at')->first();

            if ($lastMessage && !empty($lastMessage->message_content)) {
                try {
                    $partner->decrypted_last_message = \Crypt::decryptString($lastMessage->message_content);
                } catch (\Illuminate\Contracts\Encryption\DecryptException $e) {
                    $partner->decrypted_last_message = '[Unable to decrypt]';
                }
            } else {
                $partner->decrypted_last_message = null;
            }
            $partner->last_message_time = $lastMessage ? $lastMessage->sent_at : null;
        }

        $receiver = $request->query('receiver_id')
            ? \App\Models\Shared\User::where('user_id', $request->query('receiver_id'))->first()
            : null;

        return view('rescuer.rescuer-messages', compact('partners', 'receiver'));
    }

    public function profile()
    {
        $user = auth()->user();
        $rescuer = $user->rescuer;

        if (!$rescuer) {
            return view('rescuer.profile', compact('user'))->with([
                'rescuer' => null,
                'verification' => null
            ]);
        }

        $verification = $rescuer->verifications()->latest()->first(); // or correct relationship
        return view('rescuer.profile', compact('user', 'rescuer', 'verification'));
    }

    protected function clearDashboardCache($userId)
    {
        \Cache::forget("user_profile_image_{$userId}");
    }

    public function updateProfile(Request $request)
    {
        \Log::info('updateProfile called');

        $user = auth()->user();
        $rescuer = $user->rescuer;

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
        $rescuer->update([
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
            $user->update([
                'profile_image' => $profileImagePath,
            ]);
        }

        if ($request->has('remove_photo')) {
            // Mark all as not displayed
            \DB::table('user_profile_pic')
                ->where('user_id', $user->user_id)
                ->update(['is_displayed' => false]);
            // Optionally set user's profile_image to null
            $user->update(['profile_image' => null]);
        }
        $this->clearDashboardCache($user->user_id);

        return back()->with('success', 'Profile updated!');
    }

    //Upload Profile Picture Feature
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

    // --- PET MANAGEMENT CRUD ---
    public function pets()
    {
        $rescuer = auth()->user()->rescuer;
        $pets = $rescuer->pets()->with('images')->latest()->get();
        return view('rescuer.pet-management', compact('pets'));
    }

    public function AddPetListing(Request $request)
    {
        $rescuer = auth()->user()->rescuer;
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
            'adoption_status' => 'required|string',
            'suitable_for' => 'nullable|string',
            'medical_history.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif|max:5120',
        ]);
        $data['rescuer_id'] = $rescuer->rescuer_id;

        // Handle medical records upload
        $medicalHistory = [];
        if ($request->hasFile('medical_history')) {
            foreach ($request->file('medical_history') as $file) {
                $path = $file->store('medicalrecords', 's3');
                \Storage::disk('s3')->setVisibility($path, 'public');
                $url = \Storage::disk('s3')->url($path);
                $medicalHistory[] = [
                    'url' => $url,
                    'name' => $file->getClientOriginalName(),
                ];
            }
        }
        $data['medical_history'] = $medicalHistory;

        $pet = \App\Models\Shared\Pet::create($data);
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
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('rescuer.pet-management')->with('success', 'Pet added successfully!');
    }

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
            'suitable_for' => 'nullable|string',
            'medical_history.*' => 'nullable|file|mimes:pdf,jpeg,png,jpg,gif|max:5120',
        ]);

        // Handle medical records upload (append to existing)
        $medicalHistory = $pet->medical_history ?? [];
        if ($request->hasFile('medical_history')) {
            foreach ($request->file('medical_history') as $file) {
                $path = $file->store('medicalrecords', 's3');
                \Storage::disk('s3')->setVisibility($path, 'public');
                $url = \Storage::disk('s3')->url($path);
                $medicalHistory[] = [
                    'url' => $url,
                    'name' => $file->getClientOriginalName(),
                ];
            }
        }
        $data['medical_history'] = $medicalHistory;

        $pet->update($data);
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
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }
        return redirect()->route('rescuer.pet-management')->with('success', 'Pet updated successfully!');
    }

    public function destroy($petId)
    {
        $pet = \App\Models\Shared\Pet::findOrFail($petId);
        $pet->delete();
        return redirect()->route('rescuer.pet-management')->with('success', 'Pet deleted successfully!');
    }

    public function deleteImage($imageId)
    {
        $image = \App\Models\Shared\PetImage::findOrFail($imageId);
        $path = parse_url($image->image_url, PHP_URL_PATH);
        $path = ltrim($path, '/');
        \Storage::disk('s3')->delete($path);
        $image->delete();
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back()->with('success', 'Image deleted successfully!');
    }

    public function reviewApplication($id)
    {
        $application = AdoptionApplication::with(['adopter.user', 'pet'])->findOrFail($id);
        if (request()->ajax()) {
            return response()->view('rescuer.rescuer-application_modal', compact('application'));
        }
        return view('rescuer.rescuer-application_modal', compact('application'));
    }
}

<?php

namespace App\Http\Controllers\Adopter;

use App\Models\Shared\AdoptionApplication;
use Illuminate\Http\Request;
use App\Models\Shared\Pet;
use App\Models\Application;
use App\Models\Shared\Message;
use App\Models\Shared\User;
use App\Http\Controllers\Shared\Controller;
use Illuminate\Support\Facades\Cache;

class AdopterDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userId = $user->user_id;
        
        // Cache the dashboard data for 5 minutes to improve performance
        $cacheKey = "adopter_dashboard_{$userId}";
        
        $dashboardData = Cache::remember($cacheKey, 300, function () use ($user) {
            $adopter = $user->adopter()->with([
                'savedPets.images',
                'applications.pet.images',
                'applications.pet.shelter'
            ])->first();
            
            $favoritePets = $adopter ? $adopter->savedPets : collect();
            
            // Get recent applications with proper eager loading
            $recentApplications = $adopter ? $adopter->applications()
                ->with(['pet.images', 'pet.shelter'])
                ->latest()
                ->take(5)
                ->get() : collect();
            
            // Get recent messages (temporarily disabled)
            $recentMessages = collect();
            
            return [
                'favoritePets' => $favoritePets,
                'recentApplications' => $recentApplications,
                'recentMessages' => $recentMessages
            ];
        });
        
        return view('adopter.adopter_dashboard', array_merge(
            ['user' => $user],
            $dashboardData
        ));
    }

    /**
     * Clear dashboard cache when data is updated
     */
    private function clearDashboardCache($userId)
    {
        Cache::forget("adopter_dashboard_{$userId}");
        Cache::forget("user_profile_image_{$userId}");
    }

    public function profile()
    {
        $user = auth()->user();
        $adopter = $user->adopter;
        return view('adopter.profile', compact('user', 'adopter'));
    }
    //Upload New Photo Feature
    public function updateProfile(Request $request)
    {
        \Log::info('updateProfile called');

        $user = auth()->user();
        $adopter = $user->adopter;

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
        $adopter->update([
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

        // Clear cache after profile update
        $this->clearDashboardCache($user->user_id);

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

    public function messages(Request $request)
    {
        $adopter = auth()->user();

        // Get all shelter users who messaged or were messaged by this adopter, eager load shelterProfile
        $partners = User::whereHas('sentMessages', function ($q) use ($adopter) {
            $q->where('receiver_id', $adopter->user_id);
        })->orWhereHas('receivedMessages', function ($q) use ($adopter) {
            $q->where('sender_id', $adopter->user_id);
        })->with('shelterProfile')->get();

        // Use receiver_id from query if present
        $receiver = null;
        if ($request->has('receiver_id')) {
            $receiver = $partners->where('user_id', $request->receiver_id)->first();
        }
        if (!$receiver) {
            $receiver = $partners->first();
        }

        return view('adopter.messages', compact('partners', 'receiver'));
    }
    
}


        


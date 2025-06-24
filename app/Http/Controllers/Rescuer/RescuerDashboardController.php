<?php

namespace App\Http\Controllers\Rescuer;

use Illuminate\Http\Request;
use App\Http\Controllers\Shared\Controller;

class RescuerDashboardController extends Controller
{
    public function index()
    {
        return view('rescuer.rescuer_dashboard');
    }

    public function profile()
    {
        $user = auth()->user();
        $rescuer = $user->rescuer;
        return view('rescuer.profile', compact('user', 'rescuer'));
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
        }
        if ($request->has('remove_photo')) {
            \DB::table('user_profile_pic')
            ->where('user_id', $user->user_id)
            ->update(['is_displayed' => false]); // hide image
        }

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
}

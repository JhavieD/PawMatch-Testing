<?php

namespace App\Http\Controllers\Shelter;

use App\Models\Shelter;
use App\Models\Adopter\AdopterReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Log;
use App\Models\Shared\Message;
use App\Models\Shared\User;
use App\Http\Controllers\Shared\Controller;
use App\Models\Shared\StrayReportStatusLog;
use App\Models\Shared\StrayReports;


class ShelterDashboardController extends Controller
{
    public function index()
    {
        $shelter = auth()->user()->shelter;

        // Add null check for shelter
        if (!$shelter) {
            // Handle case where user has no shelter - redirect or show error
            return redirect()->route('shelter.profile')->with('error', 'Please complete your shelter profile first.');
            // Or you could abort with a 404:
            // abort(404, 'Shelter profile not found');
        }

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

        // Get reviews for this shelter
        $recentReviews = collect();

        // Get shelter reviews
        $shelterReviews = \App\Models\Adopter\AdopterReview::where('shelter_id', $shelter->shelter_id)
            ->with(['adopter.user'])
            ->orderByDesc('created_at')
            ->take(5)
            ->get();

        $recentReviews = $shelter->adopterReviews()->orderByDesc('created_at')->take(2)->get();
        $verification = $shelter->verifications()->latest()->first();

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
            'recentReviews',
            'verification'
        ));
    }
    public function applications()
    {
        return view('shelter.pet_applications');
    }

    public function pets()
    {
        $shelter = auth()->user()->shelter;

        if (!$shelter) {
            abort(404, 'Shelter not found');
        }

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
    public function createPetListing(Request $request)
    {
        $shelter = auth()->user()->shelter;

        if (!$shelter) {
            return response()->json(['success' => false, 'error' => 'Shelter not found'], 404);
        }

        // Ensure adoption_status is present for validation (default to 'available' if missing)
        if (!$request->has('adoption_status')) {
            $request->merge(['adoption_status' => 'available']);
        }

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'species' => 'required|string',
            'breed' => 'required|string',
            'age' => 'required|integer',
            'gender' => 'required|string',
            'size' => 'required|string',
            'description' => 'nullable|string',
            'adoption_status' => 'required|string',
            'medical_history.*' => 'file|mimes:pdf,docx,jpg,png,jpeg|max:5120',
            'behavior' => 'nullable|string',
            'daily_activity' => 'nullable|string',
            'special_needs' => 'nullable|string',
            'compatibility' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5024',
            'eating_habits' => 'nullable|string',
            'suitable_for' => 'nullable|string',
        ]);
        $data['adoption_status'] = 'available';
        $data['shelter_id'] = $shelter->shelter_id;

        // Remove medical_history from $data to avoid array-to-string error
        unset($data['medical_history']);

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

        $medicalFiles = [];
        if ($request->hasFile('medical_history')) {
            foreach ($request->file('medical_history') as $file) {
                $path = $file->store('medical_history', 's3');
                \Storage::disk('s3')->setVisibility($path, 'public');
                $medicalFiles[] = [
                    'url' => \Storage::disk('s3')->url($path),
                    'name' => $file->getClientOriginalName(),
                ];
            }
            $pet->medical_history = $medicalFiles; // assign as array, not json_encode
            $pet->save();
        }

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('shelter.pets')->with('success', 'Pet added successfully!');
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
            'medical_history.*' => 'file|mimes:pdf,docx,jpg,png,jpeg|max:5120',
            'behavior' => 'nullable|string',
            'daily_activity' => 'nullable|string',
            'special_needs' => 'nullable|string',
            'compatibility' => 'nullable|string',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5024',
            'eating_habits' => 'nullable|string',
            'suitable_for' => 'nullable|string',
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
            'suitable_for',
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

        if ($request->hasFile('medical_history')) {
            $medicalFiles = [];
            foreach ($request->file('medical_history') as $file) {
                $path = $file->store('medical_records', 's3');
                \Storage::disk('s3')->setVisibility($path, 'public');
                $medicalFiles[] = [
                    'url' => \Storage::disk('s3')->url($path),
                    'name' => $file->getClientOriginalName(),
                ];
            }
            $existing = $pet->medical_history ? json_decode($pet->medical_history, true) : [];
            $pet->medical_history = array_merge($existing, $medicalFiles);
            $pet->save();
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->route('shelter.pets')->with('success', 'Pet updated successfully!');
    }

    public function destroy($petId)
    {
        $user = auth()->user();
        $shelter = $user->shelter;
        $pet = \App\Models\Shared\Pet::findOrFail($petId);

        // Ensure the pet belongs to the current shelter
        if ($pet->shelter_id !== $shelter->shelter_id) {
            abort(403, 'Unauthorized action.');
        }

        $pet->delete();

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json(['success' => true, 'message' => 'Pet deleted successfully!']);
        }

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
        $receiver = $partners->first();

        return view('shelter.messages', compact('partners', 'receiver'));
    }

    public function profile()
    {
        $user = auth()->user();
        $shelter = $user->shelter;

        // Add null check for shelter
        if (!$shelter) {
            // If no shelter exists, create a basic one or redirect to setup
            return view('shelter.profile', compact('user'))->with([
                'shelter' => null,
                'verification' => null
            ]);
        }

        $verification = $shelter->verifications()->latest()->first();
        return view('shelter.profile', compact('user', 'shelter', 'verification'));
    }

    protected function clearDashboardCache($userId)
    {
        \Cache::forget("user_profile_image_{$userId}");
    }

    public function updateProfile(Request $request)
    {
        \Log::info('Shelter profile update called');
        \Log::info('updateProfile called');

        $user = auth()->user();
        $shelter = $user->shelter;

        $request->validate([
            'shelter_name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone_number' => 'required|string|max:20',
            'address' => 'nullable|string|max:255',
            'profile_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        [$first_name, $last_name] = array_pad(explode(' ', $request->name, 2), 2, '');

        $user->update([
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
        ]);

        $shelter->update([
            'shelter_name' => $request->shelter_name,
            'location' => $request->address,
            'purpose' => $request->purpose,
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

    // STRAY REPORTS METHODS added by andrea
    public function strayReports(Request $request)
        {
            $shelter = auth()->user()->shelter;

            if (!$shelter) {
                abort(404, 'Shelter not found');
            }

            $query = \DB::table('stray_reports')
                ->join('adopters', 'stray_reports.adopter_id', '=', 'adopters.adopter_id')
                ->join('users', 'adopters.user_id', '=', 'users.user_id')
                ->leftJoin('stray_report_notifications', function ($join) use ($shelter) {
                    $join->on('stray_reports.report_id', '=', 'stray_report_notifications.report_id')
                        ->where('stray_report_notifications.shelter_id', '=', $shelter->shelter_id);
                })
                ->whereNotNull('stray_report_notifications.id')
                ->select([
                    'stray_reports.*',
                    \DB::raw("CONCAT(users.first_name, ' ', users.last_name) as reporter_name"),
                    'users.email as reporter_email',
                    'stray_report_notifications.admin_message',
                    'stray_report_notifications.is_read',
                    'stray_report_notifications.sent_at'
                ])
                ->distinct(); // to  remove duplicates

            if ($request->filled('search')) {
                $search = $request->get('search');
                $query->where(function ($q) use ($search) {
                    $q->where('stray_reports.location', 'LIKE', "%{$search}%")
                        ->orWhere('stray_reports.description', 'LIKE', "%{$search}%")
                        ->orWhere(\DB::raw("CONCAT(users.first_name, ' ', users.last_name)"), 'LIKE', "%{$search}%");
                });
            }

            if ($request->filled('status')) {
                $status = $request->get('status');
                if ($status !== 'all') {
                    $query->where('stray_reports.status', $status);
                }
            }

            // Exclude 'pending' reports by default
            if (!$request->filled('status') || $request->get('status') === 'all') {
                $query->where('stray_reports.status', '!=', 'pending');
            }

            $reports = $query->orderByDesc('stray_report_notifications.sent_at')->paginate(12);

            $reports->getCollection()->transform(function ($report) {
                if ($report->image_url) {
                    $report->image_url = json_decode($report->image_url, true);
                }
                return $report;
            });
            return view('shelter.stray-reports', compact('reports'));
        }
    public function acceptStrayReport($reportId)
    {
        $shelter = auth()->user()->shelter;

        try {
            \DB::beginTransaction();
            

            $report = StrayReports::find($reportId);
            
            if (!$report) {
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Report not found'
                ], 404);
            }

            // Validation logic
            if ($report->status === 'cancelled') {
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot accept cancelled reports'
                ], 400);
            }

            //Store the old status before updating
            $oldStatus = $report->status;
            
            if (in_array($report->status, ['pending', 'investigating'])) {
                $report->status = 'accepted';
                $report->updated_at = now();
                $report->save();
            } else {
                \DB::rollback();
                return response()->json([
                    'success' => false,
                    'message' => 'Report cannot be accepted in its current state'
                ], 400);
            }

            // Update the notification record
            \DB::table('stray_report_notifications')
                ->where('report_id', $reportId)
                ->where('shelter_id', $shelter->shelter_id)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                    'handled_at' => now(),
                    'updated_at' => now()
                ]);

            $message = "{$shelter->shelter_name} has accepted your stray animal report and will be taking action to help the animal.";
            
            $report->logStatusChange($oldStatus, 'accepted', auth()->id(), $message);

            \DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Report accepted successfully'
            ]);
            
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Accept stray report error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to accept report: ' . $e->getMessage()
            ], 500);
        }
    }
    public function markStrayReportRead($reportId)
    {
        $shelter = auth()->user()->shelter;

        try {
            \DB::table('stray_report_notifications')
                ->where('report_id', $reportId)
                ->where('shelter_id', $shelter->shelter_id)
                ->update([
                    'is_read' => true,
                    'read_at' => now()
                ]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['success' => false], 500);
        }
    }

    // POST /shelter/stray-reports/{reportId}/resolve
    public function resolveStrayReport($reportId, Request $request)
    {
        $shelter = auth()->user()->shelter;
        $resolution = $request->input('resolution'); // 'found' or 'cancelled'
        $note = $request->input('note');

        if (!in_array($resolution, ['found', 'cancelled'])) {
            return response()->json(['success' => false, 'message' => 'Invalid resolution.'], 400);
        }

        try {
            \DB::beginTransaction();
            $report = \App\Models\Shared\StrayReports::find($reportId);
            if (!$report) {
                \DB::rollback();
                return response()->json(['success' => false, 'message' => 'Report not found'], 404);
            }
            if ($report->status !== 'accepted') {
                \DB::rollback();
                return response()->json(['success' => false, 'message' => 'Report must be accepted before resolving.'], 400);
            }
            $oldStatus = $report->status;
            $report->status = $resolution;
            $report->updated_at = now();
            $report->save();
            // Update notification
            \DB::table('stray_report_notifications')
                ->where('report_id', $reportId)
                ->where('shelter_id', $shelter->shelter_id)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                    'handled_at' => now(),
                    'updated_at' => now()
                ]);
            $message = $resolution === 'found'
                ? "{$shelter->shelter_name} has found and helped the animal you reported. Thank you for making a difference!"
                : "{$shelter->shelter_name} was unable to find the animal you reported. Thank you for your concern.";
            if ($note) {
                $message .= "\nNote: $note";
            }
            $report->logStatusChange($oldStatus, $resolution, auth()->id(), $message);
            \DB::commit();
            return response()->json(['success' => true, 'message' => 'Report resolved successfully.']);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Resolve stray report error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to resolve report: ' . $e->getMessage()], 500);
        }
    }

    // POST /shelter/stray-reports/{reportId}/return-pending
    public function returnStrayReportToPending($reportId, Request $request)
    {
        $shelter = auth()->user()->shelter;
        $note = $request->input('note');
        try {
            \DB::beginTransaction();
            $report = \App\Models\Shared\StrayReports::find($reportId);
            if (!$report) {
                \DB::rollback();
                return response()->json(['success' => false, 'message' => 'Report not found'], 404);
            }
            if ($report->status !== 'investigating') {
                \DB::rollback();
                return response()->json(['success' => false, 'message' => 'Only investigating reports can be returned to pending.'], 400);
            }
            $oldStatus = $report->status;
            $report->status = 'pending';
            $report->updated_at = now();
            $report->save();
            // Update notification
            \DB::table('stray_report_notifications')
                ->where('report_id', $reportId)
                ->where('shelter_id', $shelter->shelter_id)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                    'handled_at' => now(),
                    'updated_at' => now()
                ]);
            $message = "{$shelter->shelter_name} returned the stray animal report to pending.";
            if ($note) {
                $message .= "\nNote: $note";
            }
            $report->logStatusChange($oldStatus, 'pending', auth()->id(), $message);
            \DB::commit();
            return response()->json(['success' => true, 'message' => 'Report returned to pending successfully.']);
        } catch (\Exception $e) {
            \DB::rollback();
            \Log::error('Return to pending error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to return report to pending: ' . $e->getMessage()], 500);
        }
    }
}


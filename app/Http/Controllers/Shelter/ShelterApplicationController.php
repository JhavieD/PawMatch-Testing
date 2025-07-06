<?php

namespace App\Http\Controllers\Shelter;

use Illuminate\Http\Request;
use App\Models\Shared\AdoptionApplication;
use App\Models\ApplicationAnswer;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Shared\Controller;

class ShelterApplicationController extends Controller
{
    // List all applications for the shelter's pets
    public function index()
    {
        $shelter = Auth::user()->shelter;
        $applications = AdoptionApplication::where('shelter_id', $shelter->shelter_id)
            ->with(['adopter.user', 'pet', 'answers'])
            ->orderByDesc('submitted_at')
            ->get();
        return view('shelter.applications.index', compact('applications'));
    }

    // Show details of a specific application
    public function reviewApplication($id)
    {
        $application = AdoptionApplication::with(['adopter.user', 'pet', 'answers'])
            ->findOrFail($id);
        if (request()->ajax()) {
            return response()->view('shelter.application_modal', compact('application'));
        }
        return view('shelter.application_modal', compact('application'));
    }

    // Approve an application
    public function approve($id)
    {
        $application = AdoptionApplication::findOrFail($id);
        $application->status = 'approved';
        $application->save();

        // Send notification to adopter
        if ($application->adopter && $application->adopter->user) {
            \App\Models\Notification::create([
                'user_id' => $application->adopter->user->user_id,
                'type' => 'application',
                'title' => 'Application Approved!',
                'message' => 'Your adoption application for ' . ($application->pet->name ?? 'a pet') . ' has been approved. Please schedule your meet & greet!',
                'is_read' => false,
                'action_url' => url('/adopter/application-status'),
            ]);
        }

        return response()->json(['success' => true]);
    }

    // Reject an application
    public function reject(Request $request, $id)
    {
        $application = AdoptionApplication::findOrFail($id);

        // ✅ If you're sending JSON, you must decode it properly
        $rejectionReason = $request->input('rejection_reason'); // This will work for both JSON and form data

        if (!$rejectionReason) {
            return response()->json(['error' => 'Rejection reason required.'], 400);
        }

        $application->status = 'rejected';
        $application->rejection_reason = $rejectionReason; // Make sure this column exists in your DB
        $application->save();

        return response()->json(['message' => 'Application rejected successfully.']);
    }

    // Request more info from applicant
    public function requestInfo(Request $request, $id)
    {
        $application = AdoptionApplication::findOrFail($id);
        $application->status = 'info-requested';
        $application->reviewed_at = now();
        $application->save();


        return response()->json(['message' => 'Information request sent successfully.']);
    }

    // Return applications for a specific pet as JSON (for modal)
    public function forPet($petId)
    {
        $applications = AdoptionApplication::where('pet_id', $petId)
            ->with(['adopter.user', 'pet', 'answers'])
            ->orderByDesc('submitted_at')
            ->get();

        $formatted = $applications->map(function ($app) {
            $adopter = $app->adopter;
            $user = $adopter ? $adopter->user : null;
            return [
                'application_id' => $app->id,
                'applicant_name' => $user ? ($user->name ?? 'Unknown') : 'Unknown',
                'phone' => $user ? ($user->phone ?? '') : '',
                'submitted_at' => $app->submitted_at,
                'status' => ucfirst($app->status),
            ];
        });

        return response()->json(['applications' => $formatted]);
    }
}

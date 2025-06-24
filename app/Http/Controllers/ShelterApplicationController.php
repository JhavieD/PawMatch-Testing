<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdoptionApplication;
use App\Models\ApplicationAnswer;
use Illuminate\Support\Facades\Auth;

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
    public function show($id)
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
}

<?php

namespace App\Http\Controllers\Shelter;

use App\Http\Controllers\Shared\Controller;
use Illuminate\Http\Request;
use App\Models\Shared\AdoptionApplication;

class AdoptionApplicationController extends Controller
{
    // View all applications for a specific pet
    public function index(Request $request)
    {
        $user = auth()->user();
        $shelter = $user->shelter;
        if (!$shelter) {
            abort(403, 'Shelter profile not found.');
        }
        $shelterId = $shelter->shelter_id ?? $shelter->id ?? null;
        $applications = AdoptionApplication::with(['adopter.user', 'pet'])
            ->where('shelter_id', $shelterId)
            ->orderByDesc('submitted_at')
            ->get();
        return view('shelter.pet_applications', compact('applications'));
    }

    // Return applications for a specific pet as JSON
    public function forPet($petId)
    {
        $applications = AdoptionApplication::with(['adopter.user', 'pet'])
            ->where('pet_id', $petId)
            ->orderByDesc('submitted_at')
            ->get();

        // Format applications for frontend JS
        $formatted = $applications->map(function ($app) {
            return [
                'id' => $app->id,
                'applicant_name' => $app->adopter->user->name ?? 'Unknown',
                'submitted_at' => $app->submitted_at,
                'status' => ucfirst($app->status),
            ];
        });

        return response()->json(['applications' => $formatted]);
    }

    // for pet aadoption application modal

    public function reviewApplication($id)
    {
        $application = AdoptionApplication::with(['adopter.user', 'pet'])->findOrFail($id);
        return view('shelter.application_modal', compact('application'));
    }

    public function approve($id)
    {
        $application = AdoptionApplication::findOrFail($id);
        $application->status = 'approved';
        $application->save();

        // Optionally, you can redirect or return a response
        return response()->json(['success' => true]);
    }

    public function reject(Request $request, $id)
    {
        $application = AdoptionApplication::findOrFail($id);
        $request->validate(['rejection_reason' => 'required|string|max:255']);
        $application->status = 'rejected';
        $application->rejection_reason = $request->input('rejection_reason');
        $application->save();

        return response()->json(['success' => true]);
    }

    public function requestInfo($id)
    {
        return response()->json([
            'success' => true,
            'message' => 'Request sent.'
        ]);
    }
}

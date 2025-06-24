<?php

namespace App\Http\Controllers\Adopter;

use Illuminate\Support\Facades\Auth;
use App\Models\Shared\AdoptionApplication;
use App\Http\Controllers\Shared\Controller;

class AdopterApplicationController extends Controller
{
    public function index()
    {
        $adopter = Auth::user()->adopter;
        if (!$adopter) {
            abort(403, 'Adopter profile not found.');
        }
        $applications = AdoptionApplication::where('adopter_id', $adopter->adopter_id)
            ->with(['pet.shelter'])
            ->orderByDesc('submitted_at')
            ->get();
        return view('adopter.application-status', compact('applications'));
    }

    public function store(\Illuminate\Http\Request $request)
    {
        $user = Auth::user();
        $adopter = $user->adopter;
        if (!$adopter) {
            return response()->json(['error' => 'Adopter profile not found'], 422);
        }
        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,pet_id',
            'reason_for_adoption' => 'required|string',
            'living_arrangement' => 'required|string',
            'experience_with_pets' => 'required|string',
            'household_members' => 'required',
            'allergies' => 'required',
            'has_other_pets' => 'required',
            'other_pets_details' => 'nullable|string',
            'can_provide_vet_care' => 'required',
        ]);
        $pet = \App\Models\Shared\Pet::find($validated['pet_id']);
        $application = AdoptionApplication::create([
            'adopter_id' => $adopter->adopter_id,
            'pet_id' => $pet->pet_id,
            'shelter_id' => $pet->shelter_id,
            'reason_for_adoption' => $validated['reason_for_adoption'],
            'living_environment' => $validated['living_arrangement'],
            'experience_with_pets' => $validated['experience_with_pets'],
            'household_members' => (string) $validated['household_members'],
            'allergies' => $validated['allergies'],
            'has_other_pets' => $validated['has_other_pets'],
            'other_pets_details' => $validated['other_pets_details'] ?? null,
            'can_provide_vet_care' => $validated['can_provide_vet_care'],
            'status' => 'pending',
            'submitted_at' => now(),
        ]);
        return response()->json(['success' => true, 'application_id' => $application->application_id]);
    }
} 